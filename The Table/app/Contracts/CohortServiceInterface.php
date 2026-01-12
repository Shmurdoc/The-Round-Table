<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Cohort;
use App\Models\User;
use App\DTOs\CohortData;
use App\DTOs\CohortFilterData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CohortServiceInterface
{
    /**
     * Get paginated cohorts with optional filters
     */
    public function getPaginatedCohorts(CohortFilterData $filters, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get cohort by ID with all relationships
     */
    public function getCohortWithDetails(int $cohortId): Cohort;

    /**
     * Get user's cohorts with investment data
     */
    public function getUserCohorts(User $user): Collection;

    /**
     * Get user's portfolio summary
     */
    public function getUserPortfolioSummary(User $user): array;

    /**
     * Join a cohort
     */
    public function joinCohort(User $user, Cohort $cohort, int $contributionAmount): bool;

    /**
     * Check if user can join cohort
     */
    public function canJoinCohort(User $user, Cohort $cohort, int $contributionAmount): bool;

    /**
     * Leave a cohort
     */
    public function leaveCohort(User $user, Cohort $cohort): bool;

    /**
     * Create a new cohort
     */
    public function createCohort(CohortData $data, User $admin): Cohort;

    /**
     * Get admin's managed cohorts
     */
    public function getAdminCohorts(User $admin): Collection;

    /**
     * Get cohort statistics for admin dashboard
     */
    public function getAdminDashboardStats(User $admin): array;
}
