<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\CohortFund;
use App\Models\ProfitSetting;
use App\Models\Cohort;
use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletAdminController extends Controller
{
    /**
     * Wallet admin dashboard
     */
    public function index()
    {
        $pendingDeposits = WalletTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->with('user', 'wallet')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingWithdrawals = WalletTransaction::where('type', 'withdrawal')
            ->where('status', 'pending')
            ->with('user', 'wallet')
            ->orderBy('created_at', 'desc')
            ->get();

        $recentTransactions = WalletTransaction::with('user', 'wallet')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // Stats
        $totalDeposits = WalletTransaction::where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');
        $totalWithdrawals = WalletTransaction::where('type', 'withdrawal')
            ->where('status', 'completed')
            ->sum('amount');
        $totalInWallets = Wallet::sum('balance');
        $totalInCohorts = CohortFund::whereIn('status', ['active', 'locked'])->sum('current_value');

        return view('admin.wallet.index', compact(
            'pendingDeposits',
            'pendingWithdrawals',
            'recentTransactions',
            'totalDeposits',
            'totalWithdrawals',
            'totalInWallets',
            'totalInCohorts'
        ));
    }

    /**
     * Approve a deposit
     */
    public function approveDeposit(WalletTransaction $transaction)
    {
        if ($transaction->type !== 'deposit' || $transaction->status !== 'pending') {
            return back()->with('error', 'Invalid transaction.');
        }

        DB::transaction(function () use ($transaction) {
            $wallet = $transaction->wallet;
            
            // Credit wallet
            $wallet->balance += $transaction->amount;
            $wallet->total_deposited += $transaction->amount;
            $wallet->last_activity_at = now();
            $wallet->save();

            // Update transaction
            $transaction->status = 'completed';
            $transaction->balance_after = $wallet->balance;
            $transaction->processed_by = Auth::id();
            $transaction->processed_at = now();
            $transaction->save();
        });

        ActivityLog::log('deposit_approved', Auth::id(), null, "Approved deposit of R" . number_format($transaction->amount / 100, 2) . " for user #{$transaction->user_id}");

        return back()->with('success', 'Deposit approved successfully.');
    }

    /**
     * Reject a deposit
     */
    public function rejectDeposit(Request $request, WalletTransaction $transaction)
    {
        if ($transaction->type !== 'deposit' || $transaction->status !== 'pending') {
            return back()->with('error', 'Invalid transaction.');
        }

        $transaction->status = 'cancelled';
        $transaction->admin_notes = $request->input('reason', 'Rejected by admin');
        $transaction->processed_by = Auth::id();
        $transaction->processed_at = now();
        $transaction->save();

        ActivityLog::log('deposit_rejected', Auth::id(), null, "Rejected deposit for user #{$transaction->user_id}");

        return back()->with('success', 'Deposit rejected.');
    }

    /**
     * Approve a withdrawal
     */
    public function approveWithdrawal(WalletTransaction $transaction)
    {
        if ($transaction->type !== 'withdrawal' || $transaction->status !== 'pending') {
            return back()->with('error', 'Invalid transaction.');
        }

        $wallet = $transaction->wallet;
        $wallet->total_withdrawn += $transaction->amount;
        $wallet->save();

        $transaction->status = 'completed';
        $transaction->processed_by = Auth::id();
        $transaction->processed_at = now();
        $transaction->save();

        ActivityLog::log('withdrawal_approved', Auth::id(), null, "Approved withdrawal of R" . number_format($transaction->amount / 100, 2) . " for user #{$transaction->user_id}");

        return back()->with('success', 'Withdrawal approved. Funds will be transferred.');
    }

    /**
     * Reject a withdrawal (refund to wallet)
     */
    public function rejectWithdrawal(Request $request, WalletTransaction $transaction)
    {
        if ($transaction->type !== 'withdrawal' || $transaction->status !== 'pending') {
            return back()->with('error', 'Invalid transaction.');
        }

        DB::transaction(function () use ($transaction, $request) {
            $wallet = $transaction->wallet;
            
            // Refund to wallet
            $wallet->balance += $transaction->amount;
            $wallet->save();

            // Update transaction
            $transaction->status = 'cancelled';
            $transaction->admin_notes = $request->input('reason', 'Rejected by admin');
            $transaction->processed_by = Auth::id();
            $transaction->processed_at = now();
            $transaction->save();
        });

        ActivityLog::log('withdrawal_rejected', Auth::id(), null, "Rejected withdrawal for user #{$transaction->user_id}, funds refunded");

        return back()->with('success', 'Withdrawal rejected. Funds refunded to wallet.');
    }

    /**
     * Lock cohort fund (admin action)
     */
    public function lockFund(CohortFund $fund)
    {
        if ($fund->is_locked) {
            return back()->with('error', 'Fund is already locked.');
        }

        $fund->lock();

        ActivityLog::log('fund_locked', Auth::id(), $fund->cohort_id, "Locked fund #{$fund->fund_id}");

        return back()->with('success', 'Fund locked successfully.');
    }

    /**
     * Unlock cohort fund (admin action)
     */
    public function unlockFund(CohortFund $fund)
    {
        if (!$fund->is_locked) {
            return back()->with('error', 'Fund is not locked.');
        }

        $fund->unlock();

        ActivityLog::log('fund_unlocked', Auth::id(), $fund->cohort_id, "Unlocked fund #{$fund->fund_id}");

        return back()->with('success', 'Fund unlocked. User can now withdraw.');
    }

    /**
     * Set maturity date for a fund
     */
    public function setMaturityDate(Request $request, CohortFund $fund)
    {
        $request->validate([
            'maturity_date' => 'required|date|after:today',
        ]);

        $fund->maturity_date = $request->maturity_date;
        $fund->save();

        return back()->with('success', 'Maturity date set successfully.');
    }

    /**
     * Profit settings management
     */
    public function profitSettings()
    {
        $globalSetting = ProfitSetting::whereNull('cohort_id')->first();
        $cohortSettings = ProfitSetting::whereNotNull('cohort_id')
            ->with('cohort')
            ->get();
        $cohorts = Cohort::whereIn('status', ['active', 'operational'])->get();

        return view('admin.wallet.profit-settings', compact('globalSetting', 'cohortSettings', 'cohorts'));
    }

    /**
     * Update profit settings
     */
    public function updateProfitSettings(Request $request)
    {
        $request->validate([
            'cohort_id' => 'nullable|exists:cohorts,id',
            'daily_profit_rate' => 'required|numeric|min:0|max:50',
            'auto_apply' => 'boolean',
        ]);

        $setting = ProfitSetting::updateOrCreate(
            ['cohort_id' => $request->cohort_id],
            [
                'daily_profit_rate' => $request->daily_profit_rate,
                'auto_apply' => $request->boolean('auto_apply'),
                'is_active' => true,
                'set_by' => Auth::id(),
            ]
        );

        $cohortName = $request->cohort_id ? Cohort::find($request->cohort_id)->name : 'Global';
        ActivityLog::log('profit_settings_updated', Auth::id(), $request->cohort_id, "Updated profit rate to {$request->daily_profit_rate}% for {$cohortName}");

        return back()->with('success', 'Profit settings updated successfully.');
    }

    /**
     * Apply daily profits manually
     */
    public function applyDailyProfits(Request $request)
    {
        $cohortId = $request->input('cohort_id');
        
        if ($cohortId) {
            $setting = ProfitSetting::where('cohort_id', $cohortId)->first();
            if (!$setting) {
                return back()->with('error', 'No profit settings found for this cohort.');
            }
            $results = $setting->applyDailyProfits();
            $message = "Applied profits to " . count($results) . " funds in cohort.";
        } else {
            // Apply to all cohorts with auto_apply enabled
            $settings = ProfitSetting::where('auto_apply', true)->where('is_active', true)->get();
            $totalFunds = 0;
            foreach ($settings as $setting) {
                $results = $setting->applyDailyProfits();
                $totalFunds += count($results);
            }
            $message = "Applied profits to {$totalFunds} funds across all cohorts.";
        }

        ActivityLog::log('daily_profits_applied', Auth::id(), $cohortId, $message);

        return back()->with('success', $message);
    }

    /**
     * View all cohort funds
     */
    public function cohortFunds(Request $request)
    {
        $query = CohortFund::with('user', 'cohort', 'wallet');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('cohort_id')) {
            $query->where('cohort_id', $request->cohort_id);
        }

        $funds = $query->orderBy('created_at', 'desc')->paginate(20);
        $cohorts = Cohort::all();

        return view('admin.wallet.cohort-funds', compact('funds', 'cohorts'));
    }

    /**
     * Bulk lock all pending funds older than 24h
     */
    public function autoLockFunds()
    {
        $funds = CohortFund::where('is_locked', false)
            ->where('status', 'pending')
            ->where('auto_lock_at', '<=', now())
            ->get();

        foreach ($funds as $fund) {
            $fund->lock();
        }

        $count = $funds->count();
        ActivityLog::log('auto_lock_executed', Auth::id(), null, "Auto-locked {$count} funds");

        return back()->with('success', "Auto-locked {$count} funds.");
    }
}
