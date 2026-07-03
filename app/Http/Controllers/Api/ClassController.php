<?php

namespace App\Http\Controllers\Api;

use App\Models\SchoolClass;
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

            $schoolId = $request->query('school_id') ?? $user->school_id;

            if (!$schoolId) {
                return $this->error('ID sekolah diperlukan.', 400);
            }

            $classes = SchoolClass::where('school_id', $schoolId)
                ->with(['homeroomTeacher' => function ($query) {
                    $query->select('id', 'name');
                }])
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
                if ($user->role === 'guru') {
                    $teacherId = $user->teacher?->id;
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
                if ($user->role === 'guru') {
                    $teacherId = $user->teacher?->id;
                    if ($class->homeroom_teacher_id !== $teacherId) {
                        return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
                    }
                } else {
                    return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
                }
            }

            $students = $class->students()
                ->select('id', 'nis', 'nisn', 'name', 'gender', 'status')
                ->orderBy('name')
                ->get();

            return $this->success([
                'class' => $class->only(['id', 'name', 'grade', 'major']),
                'students' => $students
            ], 'Daftar siswa berhasil diambil');

        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}