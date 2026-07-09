<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Teacher;

class SchoolClassPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin', 'teacher'], true);
    }

    public function view(User $user, SchoolClass $class): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'admin') {
            return $class->school_id === $user->school_id;
        }

        if ($user->role === 'teacher') {
            $teacherId = Teacher::where('user_id', $user->id)->value('id');
            return $teacherId && (int) $class->homeroom_teacher_id === (int) $teacherId;
        }

        return false;
    }
}
