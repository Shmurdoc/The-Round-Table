<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'platform_admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Platform admin can view anyone
        return $user->role === 'platform_admin';
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->role === 'platform_admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Platform admin can update anyone
        return $user->role === 'platform_admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Cannot delete self
        if ($user->id === $model->id) {
            return false;
        }

        return $user->role === 'platform_admin';
    }

    /**
     * Determine whether the user can suspend the model.
     */
    public function suspend(User $user, User $model): bool
    {
        // Cannot suspend self
        if ($user->id === $model->id) {
            return false;
        }

        // Cannot suspend another platform admin
        if ($model->role === 'platform_admin') {
            return false;
        }

        return $user->role === 'platform_admin';
    }

    /**
     * Determine whether the user can approve KYC.
     */
    public function approveKyc(User $user, User $model): bool
    {
        return $user->role === 'platform_admin';
    }

    /**
     * Determine whether the user can promote to admin.
     */
    public function promote(User $user, User $model): bool
    {
        // Cannot promote self
        if ($user->id === $model->id) {
            return false;
        }

        return $user->role === 'platform_admin';
    }
}
