<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AlumniPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Alumni tetap boleh membaca dan memperbarui profil miliknya sendiri.
        // Status sekolah tidak boleh memblokir halaman profil pribadi.
        if (in_array($ability, ['viewProfile', 'updateProfile'], true)) {
            return null;
        }

        if (!$user->isSchoolActive()) {
            return false;
        }

        return null;
    }

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

    public function viewProfile(User $user, Alumni $alumni): bool
    {
        return $user->id === $alumni->user_id || $user->isSuperAdmin() || $user->isAdmin();
    }

    public function updateProfile(User $user, Alumni $alumni): bool
    {
        return $user->id === $alumni->user_id;
    }

    public function verify(User $user, Alumni $alumni): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->school_id === $alumni->school_id;
    }

    public function forceDelete(User $user, Alumni $alumni): bool
    {
        return $user->isSuperAdmin();
    }
}
