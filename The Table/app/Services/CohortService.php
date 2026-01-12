<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CohortServiceInterface;
use App\Models\Cohort;
use App\Models\User;
use App\Models\Transaction;
use App\DTOs\CohortData;
use App\DTOs\CohortFilterData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

final class CohortService implements CohortServiceInterface
{
    /**
     * Get paginated cohorts with optional filters
     */
    public function getPaginatedCohorts(CohortFilterData $filters, int $perPage = 12): LengthAwarePaginator
    {
        return Cohort::query()
            ->with(['admin:id,first_name,last_name,email'])
            ->withCount('members')
            ->when($filters->status, fn($q, $status) => $q->where('status', $status))
            ->when($filters->cohortClass, fn($q, $class) => $q->where('cohort_class', $class))
            ->when($filters->minContribution, fn($q, $min) => $q->where('min_contribution', '>=', $min))
            ->when($filters->maxContribution, fn($q, $max) => $q->where('max_contribution', '<=', $max))
            ->when($filters->search, fn($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get cohort by ID with all relationships
     */
    public function getCohortWithDetails(int $cohortId): Cohort
    {
        return Cohort::query()
            ->with([
                'admin:id,first_name,last_name,email',
                'members.user:id,first_name,last_name',
                'transactions' => fn($q) => $q->latest()->limit(10),
                'investments',
                'reports' => fn($q) => $q->latest()->limit(5),
            ])
            ->withCount('members')
            ->findOrFail($cohortId);
    }

    /**
     * Get user's cohorts with investment data
     */
    public function getUserCohorts(User $user): Collection
    {
        return $user->cohorts()
            ->with(['investments', 'admin:id,first_name,last_name'])
            ->withPivot(['contribution_amount', 'joined_at', 'status'])
            ->get();
    }

    /**
     * Get user's portfolio summary
     */
    public function getUserPortfolioSummary(User $user): array
    {
        $cohorts = $this->getUserCohorts($user);
        
        $totalInvested = $cohorts->sum(fn($c) => $c->pivot->contribution_amount ?? 0);
        $activeCohorts = $cohorts->where('status', 'operational')->count();
        
        $totalReturns = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'distribution')
            ->sum('amount');
        
        $returnRate = $totalInvested > 0 
            ? ($totalReturns / $totalInvested) * 100 
            : 0;

        return [
            'totalInvested' => $totalInvested,
            'totalReturns' => $totalReturns,
            'activeCohorts' => $activeCohorts,
            'returnRate' => round($returnRate, 2),
            'cohorts' => $cohorts,
        ];
    }

    /**
     * Join a cohort
     */
    public function joinCohort(User $user, Cohort $cohort, int $contributionAmount, string $status = 'active'): bool
    {
        if (!$this->canJoinCohort($user, $cohort, $contributionAmount)) {
            return false;
        }

        return DB::transaction(function () use ($user, $cohort, $contributionAmount, $status): bool {
            // Attach user to cohort (pivot table for legacy support)
            $cohort->users()->attach($user->id, [
                'contribution_amount' => $contributionAmount,
                'joined_at' => now(),
                'status' => $status,
            ]);

            // Create CohortMember record (primary member tracking)
            \App\Models\CohortMember::create([
                'cohort_id' => $cohort->id,
                'user_id' => $user->id,
                'capital_committed' => $contributionAmount,
                'capital_paid' => $contributionAmount,
                'ownership_percentage' => 0, // Will be calculated when cohort activates
                'status' => $status,
                'joined_at' => now(),
                'commitment_date' => now(),
                'payment_date' => now(),
            ]);

            // Only update cohort capital for active memberships
            if ($status === 'active') {
                $cohort->increment('current_capital', $contributionAmount);
                $cohort->increment('member_count');

                // Send notifications
                app(NotificationService::class)->notifyMemberJoined($user, $cohort->fresh(), $contributionAmount);

                // Create completed transaction record
                Transaction::create([
                    'user_id' => $user->id,
                    'cohort_id' => $cohort->id,
                    'type' => 'contribution',
                    'amount' => $contributionAmount,
                    'status' => 'completed',
                    'description' => "Contribution to {$cohort->title}",
                ]);
            } else {
                // Create pending transaction record
                Transaction::create([
                    'user_id' => $user->id,
                    'cohort_id' => $cohort->id,
                    'type' => 'contribution',
                    'amount' => $contributionAmount,
                    'status' => 'pending',
                    'description' => "Pending contribution to {$cohort->title}",
                ]);
            }

            return true;
        });
    }

    /**
     * Check if user can join cohort
     */
    public function canJoinCohort(User $user, Cohort $cohort, int $contributionAmount): bool
    {
        // Check KYC status
        if ($user->kyc_status !== 'approved') {
            return false;
        }

        // Check cohort is accepting members
        if (!in_array($cohort->status, ['funding', 'open'])) {
            return false;
        }

        // Check contribution limits
        if ($contributionAmount < $cohort->min_contribution) {
            return false;
        }

        if ($contributionAmount > $cohort->max_contribution) {
            return false;
        }

        // Check hard cap
        if (($cohort->current_capital + $contributionAmount) > $cohort->hard_cap) {
            return false;
        }

        // Check if already a member
        if ($cohort->users()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Leave a cohort (if allowed)
     */
    public function leaveCohort(User $user, Cohort $cohort): bool
    {
        // Can only leave during funding phase
        if ($cohort->status !== 'funding') {
            return false;
        }

        return DB::transaction(function () use ($user, $cohort): bool {
            $pivot = $cohort->users()->where('user_id', $user->id)->first()?->pivot;
            
            if (!$pivot) {
                return false;
            }

            $contributionAmount = $pivot->contribution_amount;

            // Detach user
            $cohort->users()->detach($user->id);

            // Update cohort capital
            $cohort->decrement('current_capital', $contributionAmount);
            $cohort->decrement('member_count');

            // Create refund transaction
            Transaction::create([
                'user_id' => $user->id,
                'cohort_id' => $cohort->id,
                'type' => 'refund',
                'amount' => $contributionAmount,
                'status' => 'completed',
                'description' => "Refund from {$cohort->title}",
            ]);

            return true;
        });
    }

    /**
     * Create a new cohort
     */
    public function createCohort(CohortData $data, User $admin): Cohort
    {
        return DB::transaction(function () use ($data, $admin): Cohort {
            $cohort = Cohort::create([
                'admin_id' => $admin->id,
                'title' => $data->title,
                'description' => $data->description,
                'cohort_class' => $data->cohortClass,
                'asset_type' => $data->assetType,
                'minimum_viable_capital' => $data->minimumViableCapital,
                'ideal_target' => $data->idealTarget,
                'hard_cap' => $data->hardCap,
                'min_contribution' => $data->minContribution,
                'max_contribution' => $data->maxContribution,
                'duration_months' => $data->durationMonths,
                'projected_annual_return' => $data->projectedAnnualReturn,
                'risk_level' => $data->riskLevel,
                'status' => 'draft',
            ]);

            return $cohort;
        });
    }

    /**
     * Get admin's managed cohorts
     */
    public function getAdminCohorts(User $admin): Collection
    {
        return Cohort::query()
            ->where('admin_id', $admin->id)
            ->withCount('members')
            ->with(['members.user', 'transactions' => fn($q) => $q->latest()->limit(5)])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get cohort statistics for admin dashboard
     */
    public function getAdminDashboardStats(User $admin): array
    {
        $cohorts = $this->getAdminCohorts($admin);
        
        return [
            'totalCohorts' => $cohorts->count(),
            'activeCohorts' => $cohorts->where('status', 'operational')->count(),
            'totalMembers' => $cohorts->sum('members_count'),
            'totalCapital' => $cohorts->sum('current_capital'),
            'totalRevenue' => $cohorts->sum('total_revenue'),
            'cohorts' => $cohorts,
        ];
    }
}
