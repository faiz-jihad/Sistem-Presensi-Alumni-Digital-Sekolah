<?php

namespace App\Policies;

use App\Models\AlumniEvent;
use App\Models\User;

class AlumniEventPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AlumniEvent $event): bool
    {
        if ($user->role === 'alumni') {
            return $event->approval_status === 'approved' || $event->posted_by === $user->id;
        }

        if ($user->role === 'super_admin') {
            return true;
        }

        return $event->school_id === $user->school_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AlumniEvent $event): bool
    {
        if ($user->role === 'alumni') {
            return $event->posted_by === $user->id && $event->approval_status === 'pending';
        }

        if ($user->role === 'super_admin') {
            return true;
        }

        return $event->school_id === $user->school_id;
    }

    public function delete(User $user, AlumniEvent $event): bool
    {
        return $this->update($user, $event);
    }

    public function approve(User $user, AlumniEvent $event): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'admin') {
            return $event->school_id === $user->school_id;
        }

        return false;
    }

    public function reject(User $user, AlumniEvent $event): bool
    {
        return $this->approve($user, $event);
    }
}
