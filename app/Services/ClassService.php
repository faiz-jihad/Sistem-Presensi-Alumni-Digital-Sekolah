<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ClassService
{
    /**
     * Mendapatkan daftar kelas sesuai filter sekolah dan wewenang guru
     */
    public function listClasses(User $user, ?int $schoolId): Collection
    {
        $teacher = $user->role === 'teacher'
            ? Teacher::where('user_id', $user->id)->first()
            : null;

        $resolvedSchoolId = $schoolId ?? ($user->school_id ?? $teacher?->school_id);

        if (!$resolvedSchoolId) {
            throw new \Exception('ID sekolah diperlukan.', 400);
        }

        return SchoolClass::where('school_id', $resolvedSchoolId)
            ->when($teacher, function ($query) use ($teacher) {
                $query->where('homeroom_teacher_id', $teacher->id);
            })
            ->with(['homeroomTeacher' => function ($query) {
                $query->select('id', 'name');
            }])
            ->withCount('students')
            ->orderBy('grade')
            ->orderBy('name')
            ->get();
    }

    /**
     * Mendapatkan detail kelas beserta relasinya
     */
    public function getClassDetail(int $classId): SchoolClass
    {
        return SchoolClass::with([
            'homeroomTeacher' => function ($query) {
                $query->select('id', 'name', 'nip');
            },
            'students' => function ($query) {
                $query->select('id', 'class_id', 'parent_user_id', 'nis', 'nisn', 'name', 'gender', 'birth_date', 'status')
                    ->with(['parent:id,name,phone'])
                    ->orderBy('name');
            }
        ])->findOrFail($classId);
    }

    /**
     * Mendapatkan daftar siswa kelas dan memetakan status presensi pada tanggal tertentu
     */
    public function getClassStudents(SchoolClass $class, ?string $date): Collection
    {
        $studentsQuery = $class->students()
            ->select('id', 'class_id', 'parent_user_id', 'nis', 'nisn', 'name', 'gender', 'birth_date', 'status')
            ->with(['parent:id,name,phone'])
            ->orderBy('name');

        if ($date) {
            $studentsQuery->with(['attendances' => function ($q) use ($date) {
                $q->where('date', $date);
            }]);
        }

        $students = $studentsQuery->get();

        if ($date) {
            foreach ($students as $student) {
                $att = $student->attendances->first();
                $student->attendance_status = $att && $att->status instanceof \BackedEnum
                    ? $att->status->value
                    : $att?->status;
                $student->attendance_note = $att ? $att->note : null;
            }
        }

        return $students;
    }
}
