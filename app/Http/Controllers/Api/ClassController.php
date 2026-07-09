<?php

namespace App\Http\Controllers\Api;

use App\Models\SchoolClass;
use App\Http\Resources\ClassResource;
use App\Http\Resources\StudentResource;
use App\Services\ClassService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ClassController extends BaseController
{
    public function __construct(
        private readonly ClassService $classService
    ) {}

    /**
     * Ambil semua kelas berdasarkan sekolah
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $schoolId = $request->query('school_id') ? (int) $request->query('school_id') : null;
            $classes = $this->classService->listClasses($request->user(), $schoolId);

            return $this->success(
                ClassResource::collection($classes),
                'Daftar kelas berhasil diambil'
            );
        } catch (\Exception $e) {
            $code = $e->getCode();
            $status = in_array($code, [400, 403, 404, 422], true) ? $code : 500;
            return $this->error($e->getMessage(), $status);
        }
    }

    /**
     * Ambil detail kelas beserta siswa
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $class = $this->classService->getClassDetail((int) $id);

            // Otorisasi via Policy
            if (!Gate::forUser($request->user())->allows('view', $class)) {
                return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
            }

            return $this->success(
                new ClassResource($class),
                'Detail kelas berhasil diambil'
            );
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
            $class = SchoolClass::find($id);

            if (!$class) {
                return $this->notFound('Kelas tidak ditemukan');
            }

            // Otorisasi via Policy
            if (!Gate::forUser($request->user())->allows('view', $class)) {
                return $this->forbidden('Anda tidak memiliki akses ke kelas ini');
            }

            $date = $request->query('date');
            $students = $this->classService->getClassStudents($class, $date);

            return $this->success([
                'class'    => [
                    'id'    => $class->id,
                    'name'  => $class->name,
                    'grade' => $class->grade,
                    'major' => $class->major
                ],
                'date'     => $date,
                'students' => StudentResource::collection($students)
            ], 'Daftar siswa berhasil diambil');
        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
