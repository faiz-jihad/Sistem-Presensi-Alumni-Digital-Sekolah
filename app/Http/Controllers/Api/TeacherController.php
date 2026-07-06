<?php

namespace App\Http\Controllers\Api;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeacherController extends BaseController
{
    /**
     * Ambil semua guru berdasarkan sekolah
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

            $teachers = Teacher::where('school_id', $schoolId)
                ->with(['user' => function ($query) {
                    $query->select('id', 'name', 'email');
                }])
                ->orderBy('name')
                ->get();

            return $this->success($teachers, 'Daftar guru berhasil diambil');

        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Ambil detail guru
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $teacher = Teacher::with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'email', 'phone');
                },
                'school' => function ($query) {
                    $query->select('id', 'name');
                }
            ])->find($id);

            if (!$teacher) {
                return $this->notFound('Guru tidak ditemukan');
            }

            // Cek akses
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                if ($user->role === 'teacher') {
                    // Guru hanya bisa melihat profil sendiri
                    if ($user->teacher?->id != $id) {
                        return $this->forbidden('Anda hanya dapat melihat profil Anda sendiri');
                    }
                } else {
                    return $this->forbidden('Anda tidak memiliki akses');
                }
            }

            return $this->success($teacher, 'Detail guru berhasil diambil');

        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Ambil kelas yang diampu oleh seorang guru
     */
    public function classes(Request $request, $id): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = $request->user();

            $teacher = Teacher::find($id);

            if (!$teacher) {
                return $this->notFound('Guru tidak ditemukan');
            }

            // Cek akses
            if ($user->role !== 'admin' && $user->role !== 'super_admin') {
                if ($user->role === 'teacher' && $user->teacher?->id != $id) {
                    return $this->forbidden('Anda tidak memiliki akses');
                }
            }

            $classes = $teacher->homeroomClasses()
                ->select('id', 'name', 'grade', 'major')
                ->orderBy('grade')
                ->get();

            return $this->success([
                'teacher' => $teacher->only(['id', 'name', 'nip']),
                'classes' => $classes
            ], 'Daftar kelas yang diampu berhasil diambil');

        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}