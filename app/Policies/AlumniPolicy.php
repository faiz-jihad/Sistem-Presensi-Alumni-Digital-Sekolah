<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AlumniPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function view(User $user, Alumni $alumni): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->school_id === $alumni->school_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function update(User $user, Alumni $alumni): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->school_id === $alumni->school_id;
    }

    public function delete(User $user, Alumni $alumni): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->school_id === $alumni->school_id;
    }

    public function restore(User $user, Alumni $alumni): bool
    {
        return $user->isSuperAdmin() || ($user->isAdmin() && $user->school_id === $alumni->school_id);
    }

    public function forceDelete(User $user, Alumni $alumni): bool
    {
        return $user->isSuperAdmin();
    }
}