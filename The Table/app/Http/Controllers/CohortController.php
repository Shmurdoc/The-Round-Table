<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\CohortServiceInterface;
use App\DTOs\CohortData;
use App\DTOs\CohortFilterData;
use App\Models\Cohort;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CohortController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly CohortServiceInterface $cohortService
    ) {}

    public function index(Request $request): View
    {
        $filters = CohortFilterData::fromRequest($request);
        $cohorts = $this->cohortService->getPaginatedCohorts($filters);

        return view('cohorts.index-modern', compact('cohorts'));
    }

    public function create(): View
    {
        $this->authorize('create', Cohort::class);
        
        return view('cohorts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Cohort::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contribution_amount' => 'required|numeric|min:1',
            'max_members' => 'required|integer|min:2',
            'start_date' => 'required|date|after:today',
        ]);

        $cohortData = CohortData::fromRequest($request);
        $cohort = $this->cohortService->createCohort($cohortData, $request->user());

        return redirect()->route('cohorts.show', $cohort)
            ->with('success', 'Cohort created successfully!');
    }

    public function edit(Cohort $cohort): View
    {
        $this->authorize('update', $cohort);

        return view('cohorts.edit', compact('cohort'));
    }

    public function update(Request $request, Cohort $cohort): RedirectResponse
    {
        $this->authorize('update', $cohort);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contribution_amount' => 'required|numeric|min:1',
            'max_members' => 'required|integer|min:2',
            'start_date' => 'required|date',
        ]);

        $cohort->update($validated);

        return redirect()->route('cohorts.show', $cohort)
            ->with('success', 'Cohort updated successfully!');
    }

    public function activate(Cohort $cohort): RedirectResponse
    {
        $this->authorize('update', $cohort);

        if ($cohort->status !== 'open' || $cohort->members_count < $cohort->max_members) {
            return back()->with('error', 'Cohort cannot be activated yet.');
        }

        $cohort->update(['status' => 'active']);

        return back()->with('success', 'Cohort activated successfully!');
    }

    public function show(Cohort $cohort): View
    {
        // Load admin, members (with user details), and transactions
        // Note: Investments are tracked via cohort_members table
        $cohort->load(['admin', 'members.user', 'transactions']);

        return view('cohorts.show-modern', compact('cohort'));
    }

    public function showJoinForm(Cohort $cohort): View
    {
        $this->authorize('join', $cohort);

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $walletBalance = $wallet->balance ?? 0;

        return view('cohorts.join-usdt', compact('cohort', 'walletBalance'));
    }

    public function processPayment(Request $request, Cohort $cohort): RedirectResponse
    {
        $this->authorize('join', $cohort);

        $request->validate([
            'amount' => 'required|numeric|min:' . ($cohort->min_contribution / 100),
            'payment_source' => 'required|in:wallet,direct',
        ]);

        $user = Auth::user();
        $wallet = $user->getOrCreateWallet();
        $amountCents = (int) round($request->amount * 100);

        // Check contribution limits
        if ($amountCents < $cohort->min_contribution) {
            return back()->with('error', 'Amount is below minimum contribution.');
        }

        if ($amountCents > $cohort->max_contribution) {
            return back()->with('error', 'Amount exceeds maximum contribution.');
        }

        // Check cohort capacity
        $remainingCapacity = $cohort->hard_cap - $cohort->current_capital;
        if ($amountCents > $remainingCapacity) {
            return back()->with('error', 'Amount exceeds remaining cohort capacity.');
        }

        // Process payment based on source
        if ($request->payment_source === 'wallet') {
            if ($wallet->balance < $amountCents) {
                return back()->with('error', 'Insufficient wallet balance. Please deposit funds first.');
            }

            // Deduct from wallet and join cohort
            DB::transaction(function () use ($user, $cohort, $wallet, $amountCents) {
                // Deduct from wallet
                $wallet->balance -= $amountCents;
                $wallet->save();

                // Create wallet transaction
                $wallet->transactions()->create([
                    'user_id' => $user->id,
                    'transaction_id' => 'INV-' . date('Ymd') . '-' . strtoupper(\Str::random(8)),
                    'type' => 'investment',
                    'amount' => -$amountCents,
                    'balance_before' => $wallet->balance + $amountCents,
                    'balance_after' => $wallet->balance,
                    'status' => 'completed',
                    'description' => 'Investment in ' . $cohort->title,
                ]);

                // Join the cohort
                $this->cohortService->joinCohort($user, $cohort, $amountCents);
            });

            return redirect()->route('cohorts.show', $cohort)
                ->with('success', 'Investment successful! You are now a member of ' . $cohort->title);
        }

        // Direct payment - create pending membership
        $this->cohortService->joinCohort($user, $cohort, $amountCents, 'pending');

        return redirect()->route('wallet.deposit.form')
            ->with('info', 'Please complete your payment to confirm your investment. Your seat has been reserved.');
    }

    public function leave(Request $request, Cohort $cohort): RedirectResponse
    {
        $this->authorize('leave', $cohort);

        $this->cohortService->leaveCohort($request->user(), $cohort);
        
        return redirect()->route('cohorts.index')
            ->with('success', 'You have left the cohort.');
    }

    public function portfolio(Request $request): View
    {
        $portfolioData = $this->cohortService->getUserPortfolioSummary($request->user());

        return view('member.portfolio-modern', $portfolioData);
    }

    public function adminDashboard(Request $request): View
    {
        $stats = $this->cohortService->getAdminDashboardStats($request->user());
        
        return view('admin.dashboard-modern', $stats);
    }

    public function manageMembers(): View
    {
        $user = Auth::user();
        
        // Get cohorts managed by this admin
        $cohortIds = Cohort::where('admin_id', $user->id)->pluck('id');
        
        // Get all members from cohort_members table
        $memberIds = \App\Models\CohortMember::whereIn('cohort_id', $cohortIds)
            ->pluck('user_id')
            ->unique();
        
        $members = \App\Models\User::whereIn('id', $memberIds)
            ->where('role', 'member')
            ->with(['cohortMemberships' => function($q) use ($cohortIds) {
                $q->whereIn('cohort_id', $cohortIds)->with('cohort:id,title');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Stats
        $totalMembers = $members->total();
        $verifiedMembers = \App\Models\User::whereIn('id', $memberIds)
            ->where('role', 'member')
            ->where('kyc_status', 'verified')
            ->count();
        $pendingMembers = \App\Models\User::whereIn('id', $memberIds)
            ->where('role', 'member')
            ->where('kyc_status', 'pending')
            ->count();
        $totalInvested = \App\Models\CohortMember::whereIn('cohort_id', $cohortIds)
            ->where('status', 'active')
            ->sum('capital_paid');
        
        return view('admin.members', compact(
            'members',
            'totalMembers',
            'verifiedMembers',
            'pendingMembers',
            'totalInvested'
        ));
    }

    public function manageInvestments(): View
    {
        return view('admin.investments');
    }

    public function createInvestment(): View
    {
        return view('admin.investments.create');
    }

    public function dashboard(): RedirectResponse
    {
        return redirect()->route('member.portfolio');
    }

    public function memberCohortDetails(Cohort $cohort): RedirectResponse
    {
        return redirect()->route('cohorts.show', $cohort);
    }
}
