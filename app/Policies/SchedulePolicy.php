<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;

class SchedulePolicy
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
    public function view(User $user, Schedule $schedule): bool
    {
        return $user->school_id === $schedule->school_id;
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
    public function update(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin() && $user->school_id === $schedule->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->isAdmin() && $user->school_id === $schedule->school_id;
    }
}
