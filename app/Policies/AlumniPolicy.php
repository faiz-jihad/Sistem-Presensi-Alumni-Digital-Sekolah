<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AlumniPolicy
{
    public function viewAny(User $user): bool
    {
<<<<<<< Updated upstream
        return true;
=======
        return $user->isSuperAdmin() || $user->isAdmin();
>>>>>>> Stashed changes
    }

    public function view(User $user, Alumni $alumni): bool
    {
<<<<<<< Updated upstream
        return true;
=======
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->school_id === $alumni->school_id;
>>>>>>> Stashed changes
    }

    public function create(User $user): bool
    {
<<<<<<< Updated upstream
        return true;
=======
        return $user->isSuperAdmin() || $user->isAdmin();
>>>>>>> Stashed changes
    }

    public function update(User $user, Alumni $alumni): bool
    {
<<<<<<< Updated upstream
        return true;
=======
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->school_id === $alumni->school_id;
>>>>>>> Stashed changes
    }

    public function delete(User $user, Alumni $alumni): bool
    {
<<<<<<< Updated upstream
        return true;
=======
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->isAdmin() && $user->school_id === $alumni->school_id;
>>>>>>> Stashed changes
    }

    public function restore(User $user, Alumni $alumni): bool
    {
<<<<<<< Updated upstream
        return true;
=======
        return $user->isSuperAdmin() || ($user->isAdmin() && $user->school_id === $alumni->school_id);
>>>>>>> Stashed changes
    }

    public function forceDelete(User $user, Alumni $alumni): bool
    {
<<<<<<< Updated upstream
        return true;
=======
        return $user->isSuperAdmin();
>>>>>>> Stashed changes
    }
}