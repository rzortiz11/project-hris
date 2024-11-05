<?php

namespace App\Policies;

use App\Models\PayPeriod;
use App\Models\User;

class PayPeriodPolicy
{
   /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PayPeriod $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PayPeriod $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PayPeriod $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PayPeriod $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PayPeriod $model): bool
    {
        return $user->hasRole(['Admin', 'Human_resource']);
    }
}
