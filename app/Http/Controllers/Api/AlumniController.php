<?php

namespace App\Http\Controllers\Api;

use App\Models\Alumni;
use App\Models\AlumniProfile;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AlumniController extends BaseController
{
    /**
     * Registrasi alumni baru
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'nisn' => 'required|string|unique:alumni,nisn|max:20',
            'gender' => 'required|in:male,female',
            'graduation_year' => 'required|integer',
            'class_name' => 'required|string|max:255',
            'major' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        DB::beginTransaction();

        try {
            // 1. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'alumni',
                'school_id' => $request->school_id,
                'status' => 'inactive', // inactive until verified by admin
            ]);

            // 2. Create Alumni Record
            $alumni = Alumni::create([
                'school_id' => $request->school_id,
                'user_id' => $user->id,
                'nisn' => $request->nisn,
                'name' => $request->name,
                'gender' => $request->gender,
                'graduation_year' => $request->graduation_year,
                'class_name' => $request->class_name,
                'major' => $request->major,
                'email' => $request->email,
                'phone' => $request->phone,
                'verification_status' => 'pending',
            ]);

            // 3. Create empty Alumni Profile
            AlumniProfile::create([
                'alumni_id' => $alumni->id,
                'current_status' => 'unemployed',
                'whatsapp' => $request->phone,
            ]);

            DB::commit();

            return $this->success($alumni, 'Registrasi alumni berhasil. Menunggu verifikasi admin.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Gagal melakukan registrasi: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Statistik Tracer Study Alumni (Admin & Super Admin)
     */
    public function tracerStudy(Request $request): JsonResponse
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        if ($user->role === 'super_admin') {
            $schoolId = $request->input('school_id', School::first()?->id);
        }

        if (!$schoolId) {
            return $this->error('Sekolah tidak ditemukan', 400);
        }

        // Get alumni IDs for this school
        $alumniIds = Alumni::where('school_id', $schoolId)->pluck('id');

        // Status distribution
        $statusCounts = AlumniProfile::whereIn('alumni_id', $alumniIds)
            ->select('current_status', DB::raw('count(*) as total'))
            ->groupBy('current_status')
            ->get()
            ->pluck('total', 'current_status');

        // Ensure status keys exist in output
        $statuses = ['working', 'studying', 'entrepreneur', 'unemployed'];
        $stats = [];
        foreach ($statuses as $status) {
            $stats[$status] = $statusCounts->get($status, 0);
        }

        $totalAlumni = Alumni::where('school_id', $schoolId)->count();
        $totalTracer = AlumniProfile::whereIn('alumni_id', $alumniIds)->count();

        $data = [
            'total_alumni' => $totalAlumni,
            'total_responded' => $totalTracer,
            'status_distribution' => $stats,
        ];

        return $this->success($data, 'Statistik tracer study berhasil diambil');
    }
}
