<?php

namespace App\Policies;

use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudentAttendance $studentAttendance): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return ($user->isAdmin() || $user->isTeacher()) && $user->school_id === $studentAttendance->school_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudentAttendance $studentAttendance): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return ($user->isAdmin() || $user->isTeacher()) && $user->school_id === $studentAttendance->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudentAttendance $studentAttendance): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->school_id === $studentAttendance->school_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StudentAttendance $studentAttendance): bool
    {
        return $user->isSuperAdmin() || ($user->isAdmin() && $user->school_id === $studentAttendance->school_id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StudentAttendance $studentAttendance): bool
    {
        return $user->isSuperAdmin();
    }
}
