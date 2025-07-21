<?php

namespace App\Policies;

use App\Models\EmployeeSelfService;
use App\Models\User;

class EmployeeSelfServicePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Employee']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployeeSelfService $model): bool
    {
        return $user->hasRole(['Employee']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Employee']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmployeeSelfService $model): bool
    {
        return $user->hasRole(['Employee']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployeeSelfService $model): bool
    {
        return $user->hasRole(['Employee']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmployeeSelfService $model): bool
    {
        return $user->hasRole(['Employee']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EmployeeSelfService $model): bool
    {
        return $user->hasRole(['Employee']);
    }
}
