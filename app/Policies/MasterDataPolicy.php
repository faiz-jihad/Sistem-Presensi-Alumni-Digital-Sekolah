<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MasterDataPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isSchoolActive()) {
            return false;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Super Admin, Admin, and Teachers can view master data
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Model $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admins and Teachers can only view records belonging to their own school
        $schoolId = $model->school_id ?? null;
        return ($user->isAdmin() || $user->isTeacher()) && $schoolId !== null && (int) $user->school_id === (int) $schoolId;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Super Admin and School Admins can create master data
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Model $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admins can update records belonging to their own school
        $schoolId = $model->school_id ?? null;
        return $user->isAdmin() && $schoolId !== null && (int) $user->school_id === (int) $schoolId;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Model $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admins can delete records belonging to their own school
        $schoolId = $model->school_id ?? null;
        return $user->isAdmin() && $schoolId !== null && (int) $user->school_id === (int) $schoolId;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Model $model): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $schoolId = $model->school_id ?? null;
        return $user->isAdmin() && $schoolId !== null && (int) $user->school_id === (int) $schoolId;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Model $model): bool
    {
        return $user->isSuperAdmin();
    }
}
