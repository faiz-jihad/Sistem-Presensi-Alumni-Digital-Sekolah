<?php

namespace App\Http\Controllers\Api;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends BaseController
{
    /**
     * List semua sekolah dengan statistik
     */
    public function index(): JsonResponse
    {
        $schools = School::withCount([
            'users as total_users',
            'users as total_students' => function ($query) {
                $query->where('role', 'student');
            },
            'users as total_alumni' => function ($query) {
                $query->where('role', 'alumni');
            },
            'users as total_teachers' => function ($query) {
                $query->where('role', 'teacher');
            },
            'users as total_admins' => function ($query) {
                $query->where('role', 'admin');
            },
        ])->get();

        return $this->success($schools, 'Data sekolah berhasil diambil');
    }

    /**
     * Detail sekolah
     */
    public function show($id): JsonResponse
    {
        $school = School::withCount([
            'users as total_users',
            'users as total_students' => function ($query) {
                $query->where('role', 'student');
            },
            'users as total_alumni' => function ($query) {
                $query->where('role', 'alumni');
            },
        ])->findOrFail($id);

        return $this->success($school, 'Detail sekolah berhasil diambil');
    }

    /**
     * Create sekolah
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        $school = School::create($request->all());

        return $this->success($school, 'Sekolah berhasil ditambahkan', 201);
    }

    /**
     * Update sekolah
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        $school = School::findOrFail($id);
        $school->update($request->all());

        return $this->success($school, 'Sekolah berhasil diupdate');
    }

    /**
     * Delete sekolah
     */
    public function destroy($id): JsonResponse
    {
        $school = School::findOrFail($id);
        
        // Cek apakah sekolah memiliki user
        if ($school->users()->count() > 0) {
            return $this->error('Sekolah masih memiliki user', 400);
        }
        
        $school->delete();
        return $this->success(null, 'Sekolah berhasil dihapus');
    }
}