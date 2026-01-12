<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Cohort;
use App\Models\CohortFund;
use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    /**
     * Display wallet dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $cohortFunds = $wallet->cohortFunds()
            ->with('cohort')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $totalPortfolioValue = $wallet->balance + $cohortFunds->sum('current_value');
        $totalInCohorts = $cohortFunds->sum('current_value');
        $totalEarnings = $cohortFunds->sum('total_earnings');
        $availableBalance = $wallet->balance;

        return view('member.wallet.index', compact(
            'wallet',
            'transactions',
            'cohortFunds',
            'totalPortfolioValue',
            'totalInCohorts',
            'totalEarnings',
            'availableBalance'
        ));
    }

    /**
     * Show deposit form
     */
    public function depositForm()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        
        return view('member.wallet.deposit', compact('wallet'));
    }

    /**
     * Process deposit request
     */
    public function depositSubmit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|max:100000', // $10 min, $100k max
            'payment_method' => 'required|in:usdttrc20,usdtbep20,usdterc20',
        ]);

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        
        $amountCents = (int) round($request->amount * 100);
        
        // Create pending deposit transaction
        $transaction = $wallet->transactions()->create([
            'user_id' => $user->id,
            'transaction_id' => 'DEP-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
            'type' => 'deposit',
            'amount' => $amountCents,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance + $amountCents,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'description' => 'Deposit via ' . strtoupper($request->payment_method),
        ]);

        ActivityLog::log('deposit_requested', $user->id, null, "Deposit request of \${$request->amount} USDT");

        // Redirect to NOWPayments crypto payment page
        return redirect()->route('wallet.deposit.confirm', $transaction)
            ->with('info', 'Complete your USDT payment using the crypto wallet address provided.');
    }

    /**
     * Show EFT deposit confirmation page
     */
    public function depositConfirm(WalletTransaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        return view('member.wallet.deposit-confirm', compact('transaction'));
    }

    /**
     * Show withdraw form
     */
    public function withdrawForm()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        
        return view('member.wallet.withdraw', compact('wallet'));
    }

    /**
     * Process withdrawal request
     */
    public function withdrawSubmit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        
        $amountCents = (int) round($request->amount * 100);

        // Check available balance
        if ($amountCents > $wallet->available_balance) {
            return back()->withErrors(['amount' => 'Insufficient available balance.']);
        }

        // Create withdrawal (deducts from balance, pending approval)
        DB::transaction(function () use ($wallet, $user, $amountCents, $request) {
            $balanceBefore = $wallet->balance;
            $wallet->balance -= $amountCents;
            $wallet->save();

            $wallet->transactions()->create([
                'user_id' => $user->id,
                'transaction_id' => 'WTH-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
                'type' => 'withdrawal',
                'amount' => $amountCents,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'status' => 'pending',
                'description' => 'Withdrawal to ' . $request->bank_name,
                'metadata' => [
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_holder' => $request->account_holder,
                ],
            ]);
        });

        ActivityLog::log('withdrawal_requested', $user->id, null, "Withdrawal request of R{$request->amount}");

        return redirect()->route('wallet.index')
            ->with('success', 'Withdrawal request submitted. Funds will be transferred within 24-48 hours.');
    }

    /**
     * Show transfer to cohort form
     */
    public function transferForm(Cohort $cohort)
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        
        // Check if cohort accepts funds
        if (!in_array($cohort->status, ['open', 'funding', 'active'])) {
            return back()->with('error', 'This cohort is not currently accepting funds.');
        }

        // Check if user already has funds in this cohort
        $existingFund = CohortFund::where('user_id', $user->id)
            ->where('cohort_id', $cohort->id)
            ->whereNotIn('status', ['withdrawn', 'cancelled'])
            ->first();

        return view('member.wallet.transfer', compact('wallet', 'cohort', 'existingFund'));
    }

    /**
     * Process transfer to cohort
     */
    public function transferSubmit(Request $request, Cohort $cohort)
    {
        $request->validate([
            'amount' => 'required|numeric|min:' . ($cohort->min_contribution / 100),
        ]);

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        
        $amountCents = (int) round($request->amount * 100);

        // Validate amount
        if ($cohort->min_contribution && $amountCents < $cohort->min_contribution) {
            return back()->withErrors(['amount' => 'Minimum contribution is R' . number_format($cohort->min_contribution / 100, 2)]);
        }
        if ($cohort->max_contribution && $amountCents > $cohort->max_contribution) {
            return back()->withErrors(['amount' => 'Maximum contribution is R' . number_format($cohort->max_contribution / 100, 2)]);
        }
        if ($amountCents > $wallet->available_balance) {
            return back()->withErrors(['amount' => 'Insufficient wallet balance.']);
        }

        // Create cohort fund
        $fund = $wallet->transferToCohort($cohort, $amountCents);

        if (!$fund) {
            return back()->with('error', 'Transfer failed. Please try again.');
        }

        ActivityLog::log('cohort_fund_created', $user->id, $cohort->id, "Transferred R" . number_format($amountCents / 100, 2) . " to cohort");

        return redirect()->route('wallet.index')
            ->with('success', 'Funds transferred to cohort successfully! They will be locked after 24 hours or when admin confirms.');
    }

    /**
     * Withdraw from cohort back to wallet
     */
    public function withdrawFromCohort(CohortFund $fund)
    {
        $user = Auth::user();

        if ($fund->user_id !== $user->id) {
            abort(403);
        }

        if (!$fund->canWithdraw()) {
            return back()->with('error', 'These funds are currently locked and cannot be withdrawn.');
        }

        $fund->withdrawToWallet();

        ActivityLog::log('cohort_fund_withdrawn', $user->id, $fund->cohort_id, "Withdrew R" . number_format($fund->current_value / 100, 2) . " from cohort");

        return redirect()->route('wallet.index')
            ->with('success', 'Funds withdrawn to wallet successfully!');
    }

    /**
     * View all transactions
     */
    public function transactions()
    {
        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        
        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('member.wallet.transactions', compact('wallet', 'transactions'));
    }

    /**
     * View transaction details
     */
    public function transactionShow(WalletTransaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        return view('member.wallet.transaction-show', compact('transaction'));
    }
}
