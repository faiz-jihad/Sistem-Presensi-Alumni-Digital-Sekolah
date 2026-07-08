<?php

namespace App\Policies;

use App\Models\ClassHourPackage;
use App\Models\User;

class ClassHourPackagePolicy
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
        return $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClassHourPackage $package): bool
    {
        return $user->school_id === $package->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClassHourPackage $package): bool
    {
        return $user->isAdmin() && $user->school_id === $package->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClassHourPackage $package): bool
    {
        return $user->isAdmin() && $user->school_id === $package->school_id;
    }
}
