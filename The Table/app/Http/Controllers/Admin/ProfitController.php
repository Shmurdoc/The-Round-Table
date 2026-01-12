<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cohort;
use App\Models\DailyProfit;
use App\Models\MemberProfitDistribution;
use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfitController extends Controller
{
    /**
     * Show profit management dashboard for a cohort
     */
    public function index(Cohort $cohort)
    {
        // Verify user is admin of this cohort
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Unauthorized');
        }

        $dailyProfits = $cohort->dailyProfits()
            ->orderBy('profit_date', 'desc')
            ->paginate(15);

        $stats = [
            'total_profit' => $cohort->total_profit_generated,
            'admin_taken' => $cohort->admin_profit_taken,
            'members_distributed' => $cohort->members_profit_distributed,
            'pending_distribution' => DailyProfit::where('cohort_id', $cohort->id)
                ->where('distributed', false)
                ->sum('members_share'),
            'admin_share_rate' => $cohort->admin_profit_share,
            'active_members' => $cohort->members()->where('status', 'active')->count(),
            'special_members' => $cohort->special_member_count,
        ];

        return view('admin.cohorts.profits.index', compact('cohort', 'dailyProfits', 'stats'));
    }

    /**
     * Show form to record daily profit
     */
    public function create(Cohort $cohort)
    {
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Unauthorized');
        }

        return view('admin.cohorts.profits.create', compact('cohort'));
    }

    /**
     * Record new daily profit
     */
    public function store(Request $request, Cohort $cohort)
    {
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'profit_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if profit already recorded for this date
        $existing = DailyProfit::where('cohort_id', $cohort->id)
            ->where('profit_date', $validated['profit_date'])
            ->first();

        if ($existing) {
            return back()->withErrors(['profit_date' => 'Profit already recorded for this date.']);
        }

        $amountCents = (int) round($validated['amount'] * 100);
        
        $dailyProfit = $cohort->recordDailyProfit($amountCents, $validated['notes']);

        ActivityLog::log(
            'profit_recorded',
            Auth::id(),
            $cohort->id,
            "Recorded daily profit of R{$validated['amount']} for {$validated['profit_date']}"
        );

        return redirect()->route('admin.cohorts.profits.index', $cohort)
            ->with('success', 'Daily profit recorded successfully!');
    }

    /**
     * Update admin profit share percentage
     */
    public function updateShareRate(Request $request, Cohort $cohort)
    {
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'admin_profit_share' => 'required|numeric|min:0|max:100',
        ]);

        $oldRate = $cohort->admin_profit_share;
        $cohort->admin_profit_share = $validated['admin_profit_share'];
        $cohort->save();

        ActivityLog::log(
            'profit_share_updated',
            Auth::id(),
            $cohort->id,
            "Updated admin profit share from {$oldRate}% to {$validated['admin_profit_share']}%"
        );

        return back()->with('success', 'Admin profit share updated successfully!');
    }

    /**
     * Distribute a daily profit to members
     */
    public function distribute(DailyProfit $dailyProfit)
    {
        $cohort = $dailyProfit->cohort;

        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Unauthorized');
        }

        if ($dailyProfit->distributed) {
            return back()->withErrors(['error' => 'This profit has already been distributed.']);
        }

        DB::transaction(function () use ($dailyProfit) {
            $dailyProfit->distributed_by = Auth::id();
            $dailyProfit->distribute();
        });

        ActivityLog::log(
            'profit_distributed',
            Auth::id(),
            $cohort->id,
            "Distributed profit for {$dailyProfit->profit_date->format('d M Y')}"
        );

        return back()->with('success', 'Profit distributed to members successfully!');
    }

    /**
     * Credit all pending distributions to member wallets
     */
    public function creditToWallets(Cohort $cohort)
    {
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Unauthorized');
        }

        $pending = MemberProfitDistribution::whereHas('dailyProfit', function ($q) use ($cohort) {
            $q->where('cohort_id', $cohort->id);
        })->where('status', 'pending')->get();

        $credited = 0;
        $totalAmount = 0;

        DB::transaction(function () use ($pending, &$credited, &$totalAmount) {
            foreach ($pending as $distribution) {
                if ($distribution->creditToWallet()) {
                    $credited++;
                    $totalAmount += $distribution->amount;
                }
            }
        });

        ActivityLog::log(
            'profits_credited',
            Auth::id(),
            $cohort->id,
            "Credited {$credited} profit distributions totaling R" . number_format($totalAmount / 100, 2)
        );

        return back()->with('success', "Credited {$credited} distributions to member wallets!");
    }

    /**
     * Update cohort targets
     */
    public function updateTargets(Request $request, Cohort $cohort)
    {
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'target_amount' => 'required|numeric|min:0',
            'target_member_count' => 'required|integer|min:1',
            'per_member_target' => 'nullable|numeric|min:0',
        ]);

        $cohort->target_amount = (int) round($validated['target_amount'] * 100);
        $cohort->target_member_count = $validated['target_member_count'];
        
        // Calculate per-member target if not explicitly set
        if (!empty($validated['per_member_target'])) {
            $cohort->per_member_target = (int) round($validated['per_member_target'] * 100);
        } else {
            $cohort->per_member_target = (int) ceil($cohort->target_amount / $cohort->target_member_count);
        }

        $cohort->save();

        // Re-evaluate all members for special status
        foreach ($cohort->members as $member) {
            $member->evaluateSpecialMemberStatus();
        }

        ActivityLog::log(
            'cohort_targets_updated',
            Auth::id(),
            $cohort->id,
            "Updated cohort targets: R" . number_format($cohort->target_amount / 100, 2) . 
            " with {$cohort->target_member_count} members"
        );

        return back()->with('success', 'Cohort targets updated! Member statuses recalculated.');
    }

    /**
     * Show member profit details
     */
    public function memberProfits(Cohort $cohort)
    {
        if ($cohort->admin_id !== Auth::id() && !Auth::user()->isPlatformAdmin()) {
            abort(403, 'Unauthorized');
        }

        $members = $cohort->members()
            ->with(['user', 'profitDistributions'])
            ->where('status', 'active')
            ->orderBy('capital_committed', 'desc')
            ->get();

        return view('admin.cohorts.profits.members', compact('cohort', 'members'));
    }
}
