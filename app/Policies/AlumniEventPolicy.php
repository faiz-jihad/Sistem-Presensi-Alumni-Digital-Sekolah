<?php

namespace App\Policies;

use App\Models\AlumniEvent;
use App\Models\User;

class AlumniEventPolicy
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
        // All authenticated users with access to the panel can view the list
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AlumniEvent $event): bool
    {
        if (in_array($user->role, ['super_admin', 'admin', 'teacher'])) {
            return $user->role === 'super_admin' || $event->school_id === $user->school_id;
        }

        if ($user->role === 'alumni') {
            return $event->approval_status === 'approved' || $event->posted_by === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All roles can create/propose events
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AlumniEvent $event): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'admin') {
            return $event->school_id === $user->school_id;
        }

        if ($user->role === 'teacher') {
            return $event->school_id === $user->school_id;
        }

        if ($user->role === 'alumni') {
            // Alumni can only update their own event if it is still pending
            return $event->posted_by === $user->id && $event->approval_status === 'pending';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AlumniEvent $event): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'admin') {
            return $event->school_id === $user->school_id;
        }

        if ($user->role === 'alumni') {
            // Alumni can only delete their own event if it is still pending
            return $event->posted_by === $user->id && $event->approval_status === 'pending';
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AlumniEvent $event): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AlumniEvent $event): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }
}
