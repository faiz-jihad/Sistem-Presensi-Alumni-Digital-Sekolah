<?php

namespace App\Http\Controllers\Api;

use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends BaseController
{
    /**
     * List siswa (dengan filter)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Student::query();

        if ($request->school_id) {
            $query->where('school_id', $request->school_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('nis', 'like', "%{$request->search}%")
                    ->orWhere('nisn', 'like', "%{$request->search}%");
            });
        }

        $students = $query->orderBy('name')->paginate($request->per_page ?? 15);
        return $this->success($students, 'List siswa berhasil diambil');
    }

    /**
     * Detail siswa
     */
    public function show($id): JsonResponse
    {
        $student = Student::findOrFail($id);
        return $this->success($student, 'Detail siswa berhasil diambil');
    }

    /**
     * Tambah siswa
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'nis' => 'required|string|max:20|unique:students,nis',
            'nisn' => 'required|string|size:10|unique:students,nisn',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        $student = Student::create($request->all());
        return $this->success($student, 'Siswa berhasil ditambahkan', 201);
    }

    /**
     * Update siswa
     */
    public function update(Request $request, $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'nis' => 'required|string|max:20|unique:students,nis,' . $id,
            'nisn' => 'required|string|size:10|unique:students,nisn,' . $id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date',
            'status' => 'required|in:active,inactive,graduated,transferred,dropout',
        ]);

        $student->update($request->all());
        return $this->success($student, 'Siswa berhasil diupdate');
    }

    /**
     * Hapus siswa (soft delete)
     */
    public function destroy($id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return $this->success(null, 'Siswa berhasil dihapus');
    }
}