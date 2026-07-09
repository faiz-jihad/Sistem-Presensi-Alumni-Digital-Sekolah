<?php

namespace App\Policies;

use App\Models\JobVacancy;
use App\Models\User;

class JobVacancyPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Semua user yang terautentikasi bisa melihat lowongan kerja
    }

    public function view(User $user, JobVacancy $jobVacancy): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        // Admin, Super Admin, dan Alumni bisa membuat lowongan kerja
        return in_array($user->role, ['super_admin', 'admin', 'alumni'], true);
    }

    public function update(User $user, JobVacancy $jobVacancy): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'admin') {
            return $jobVacancy->school_id === $user->school_id;
        }

        // Alumni hanya bisa mengupdate lowongan yang mereka post sendiri
        return $user->role === 'alumni' && $jobVacancy->posted_by === $user->id;
    }

    public function delete(User $user, JobVacancy $jobVacancy): bool
    {
        return $this->update($user, $jobVacancy);
    }
}
