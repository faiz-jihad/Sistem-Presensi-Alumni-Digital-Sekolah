<?php

namespace App\Http\Controllers\Api;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClassController extends BaseController
{
    /**
     * Ambil semua kelas berdasarkan sekolah
     */
    public function index(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $teacher = $user->role === 'teacher'
                ? Teacher::where('user_id', $user->id)->first()
                : null;

            $schoolId = $request->query('school_id')
                ?? $user->school_id
                ?? $teacher?->school_id;

            if (!$schoolId) {
                return $this->error('ID sekolah diperlukan.', 400);
            }

            $classes = SchoolClass::where('school_id', $schoolId)
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

            return $this->success($classes, 'Daftar kelas berhasil diambil');

        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Ambil detail kelas beserta siswa
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $class = SchoolClass::with([
                'homeroomTeacher' => function ($query) {
                    $query->select('id', 'name', 'nip');
                },
                'students' => function ($query) {
                    $query->select('id', 'class_id', 'parent_user_id', 'nis', 'nisn', 'name', 'gender', 'birth_date', 'status')
                        ->with(['parent:id,name,phone'])
                        ->orderBy('name');
                }
            ])->find($id);

            if (!$class) {
                return $this->notFound('Kelas tidak ditemukan');
            }

            // Cek akses (admin atau guru wali kelas)
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                if ($user->role === 'teacher') {
                    $teacherId = Teacher::where('user_id', $user->id)->value('id');
                    if (!$teacherId || (int) $class->homeroom_teacher_id !== (int) $teacherId) {
                        return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
                    }
                } else {
                    return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
                }
            }

            return $this->success($class, 'Detail kelas berhasil diambil');

        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Ambil daftar siswa dalam satu kelas
     */
    public function students(Request $request, $id): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $class = SchoolClass::find($id);

            if (!$class) {
                return $this->notFound('Kelas tidak ditemukan');
            }

            // Cek akses
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                if ($user->role === 'teacher') {
                    $teacherId = Teacher::where('user_id', $user->id)->value('id');
                    if (!$teacherId || (int) $class->homeroom_teacher_id !== (int) $teacherId) {
                        return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
                    }
                } else {
                    return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
                }
            }

            $date = $request->query('date');
            
            $studentsQuery = $class->students()
                ->select('id', 'class_id', 'parent_user_id', 'nis', 'nisn', 'name', 'gender', 'birth_date', 'status')
                ->with(['parent:id,name,phone'])
                ->orderBy('name');

            if ($date) {
                $studentsQuery->with(['attendances' => function ($q) use ($date) {
                    $q->where('date', $date);
                }]);
            }

            $students = $studentsQuery->get()->map(function ($student) use ($date) {
                $att = $date ? $student->attendances->first() : null;
                $attendanceStatus = $att && $att->status instanceof \BackedEnum
                    ? $att->status->value
                    : $att?->status;

                return [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'gender' => $student->gender,
                    'birth_date' => $student->birth_date,
                    'parent' => $student->parent ? [
                        'id' => $student->parent->id,
                        'name' => $student->parent->name,
                        'phone' => $student->parent->phone,
                    ] : null,
                    'parent_name' => $student->parent?->name,
                    'parent_phone' => $student->parent?->phone,
                    'status' => $student->status,
                    'attendance_status' => $attendanceStatus,
                    'attendance_note' => $att ? $att->note : null,
                ];
            });

            return $this->success([
                'class' => $class->only(['id', 'name', 'grade', 'major']),
                'date' => $date,
                'students' => $students
            ], 'Daftar siswa berhasil diambil');

        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
