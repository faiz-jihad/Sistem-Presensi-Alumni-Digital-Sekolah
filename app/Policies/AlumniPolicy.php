<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AlumniPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Alumni $alumni): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Alumni $alumni): bool
    {
        return true;
    }

    public function delete(User $user, Alumni $alumni): bool
    {
        return true;
    }

    public function restore(User $user, Alumni $alumni): bool
    {
        return true;
    }

    public function forceDelete(User $user, Alumni $alumni): bool
    {
        return true;
    }
}