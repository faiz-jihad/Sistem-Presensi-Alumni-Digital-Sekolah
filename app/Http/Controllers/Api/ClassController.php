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
                    $query->select('id', 'class_id', 'nis', 'nisn', 'name', 'gender', 'status')
                        ->orderBy('name');
                }
            ])->find($id);

            if (!$class) {
                return $this->notFound('Kelas tidak ditemukan');
            }

            // Cek akses (admin atau guru wali kelas)
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                // Untuk guru, cek apakah dia wali kelas ini
                if ($user->role === 'teacher') {
<<<<<<< Updated upstream
                    $teacherId = Teacher::where('user_id', $user->id)->value('id');
=======
                    $teacherId = $user->teacher?->id;
>>>>>>> Stashed changes
                    if ($class->homeroom_teacher_id !== $teacherId) {
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
<<<<<<< Updated upstream
                    $teacherId = Teacher::where('user_id', $user->id)->value('id');
=======
                    $teacherId = $user->teacher?->id;
>>>>>>> Stashed changes
                    if ($class->homeroom_teacher_id !== $teacherId) {
                        return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
                    }
                } else {
                    return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
                }
            }

<<<<<<< Updated upstream
            $students = $class->students()
                ->select('id', 'class_id', 'parent_user_id', 'nis', 'nisn', 'name', 'gender', 'birth_date', 'status')
                ->with(['parent' => function ($query) {
                    $query->select('id', 'name');
                }])
                ->orderBy('name')
                ->get();
=======
            $date = $request->query('date');
            
            $studentsQuery = $class->students()
                ->select('id', 'class_id', 'nis', 'nisn', 'name', 'gender', 'status')
                ->orderBy('name');

            if ($date) {
                $studentsQuery->with(['attendances' => function ($q) use ($date) {
                    $q->where('date', $date);
                }]);
            }

            $students = $studentsQuery->get()->map(function ($student) use ($date) {
                $att = $date ? $student->attendances->first() : null;
                return [
                    'id' => $student->id,
                    'nis' => $student->nis,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'gender' => $student->gender,
                    'status' => $student->status,
                    'attendance_status' => $att ? $att->status : null,
                    'attendance_note' => $att ? $att->note : null,
                ];
            });
>>>>>>> Stashed changes

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
