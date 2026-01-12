<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Cohort;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

final class CohortPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any cohorts.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view cohorts list
        return true;
    }

    /**
     * Determine whether the user can view the cohort.
     */
    public function view(User $user, Cohort $cohort): bool
    {
        // Published cohorts can be viewed by anyone
        if (in_array($cohort->status, ['funding', 'operational', 'completed'])) {
            return true;
        }

        // Draft/pending cohorts can only be viewed by admin or platform admin
        return $this->isAdminOrPlatformAdmin($user, $cohort);
    }

    /**
     * Determine whether the user can create cohorts.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'cohort_admin', 'platform_admin']);
    }

    /**
     * Determine whether the user can update the cohort.
     */
    public function update(User $user, Cohort $cohort): bool
    {
        // Platform admin can update any cohort
        if ($user->role === 'platform_admin') {
            return true;
        }

        // Cohort admin can only update their own cohorts
        return $user->id === $cohort->admin_id;
    }

    /**
     * Determine whether the user can delete the cohort.
     */
    public function delete(User $user, Cohort $cohort): bool
    {
        // Only draft cohorts can be deleted
        if ($cohort->status !== 'draft') {
            return false;
        }

        return $this->isAdminOrPlatformAdmin($user, $cohort);
    }

    /**
     * Determine whether the user can join the cohort.
     */
    public function join(User $user, Cohort $cohort): bool
    {
        // Must have approved/verified KYC
        if (!in_array($user->kyc_status, ['approved', 'verified'])) {
            return false;
        }

        // Cohort must be in funding status
        if ($cohort->status !== 'funding') {
            return false;
        }

        // Cannot join if already a member
        if ($cohort->users()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Cannot be the cohort admin (they're automatically a member)
        if ($user->id === $cohort->admin_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can leave the cohort.
     */
    public function leave(User $user, Cohort $cohort): bool
    {
        // Can only leave during funding phase
        if ($cohort->status !== 'funding') {
            return false;
        }

        // Must be a member
        return $cohort->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can manage cohort members.
     */
    public function manageMembers(User $user, Cohort $cohort): bool
    {
        return $this->isAdminOrPlatformAdmin($user, $cohort);
    }

    /**
     * Determine whether the user can create distributions.
     */
    public function createDistribution(User $user, Cohort $cohort): bool
    {
        // Cohort must be operational
        if ($cohort->status !== 'operational') {
            return false;
        }

        return $this->isAdminOrPlatformAdmin($user, $cohort);
    }

    /**
     * Determine whether the user can submit cohort for approval.
     */
    public function submit(User $user, Cohort $cohort): bool
    {
        // Must be draft status
        if ($cohort->status !== 'draft') {
            return false;
        }

        // Only the cohort admin can submit
        return $user->id === $cohort->admin_id;
    }

    /**
     * Determine whether the user can approve the cohort.
     */
    public function approve(User $user, Cohort $cohort): bool
    {
        // Only platform admin can approve
        if ($user->role !== 'platform_admin') {
            return false;
        }

        // Must be pending approval
        return $cohort->status === 'pending_approval';
    }

    /**
     * Check if user is admin of cohort or platform admin
     */
    private function isAdminOrPlatformAdmin(User $user, Cohort $cohort): bool
    {
        if ($user->role === 'platform_admin') {
            return true;
        }

        return $user->id === $cohort->admin_id;
    }
}
