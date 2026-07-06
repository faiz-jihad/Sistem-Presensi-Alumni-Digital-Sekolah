<?php

namespace App\Http\Controllers\Api;

<<<<<<< Updated upstream
use App\Models\Alumni;
use App\Models\AlumniProfile;
use App\Models\User;
use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
=======
use App\Models\User;
use App\Models\Alumni;
use App\Models\AlumniProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
>>>>>>> Stashed changes
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AlumniController extends BaseController
{
    /**
<<<<<<< Updated upstream
     * Registrasi alumni baru
=======
     * Registrasi alumni baru (verification_status = pending)
>>>>>>> Stashed changes
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
<<<<<<< Updated upstream
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
=======
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'school_id' => 'required|exists:schools,id',
            'nisn' => 'required|string|max:20',
            'graduation_year' => 'required|integer|min:1990|max:2050',
            'class_name' => 'required|string|max:50',
            'major' => 'required|string|max:100',
>>>>>>> Stashed changes
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

<<<<<<< Updated upstream
        DB::beginTransaction();

        try {
            // 1. Create User
=======
        try {
>>>>>>> Stashed changes
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'alumni',
                'school_id' => $request->school_id,
<<<<<<< Updated upstream
                'status' => 'inactive', // inactive until verified by admin
            ]);

            // 2. Create Alumni Record
=======
                'status' => 'active',
            ]);

>>>>>>> Stashed changes
            $alumni = Alumni::create([
                'school_id' => $request->school_id,
                'user_id' => $user->id,
                'nisn' => $request->nisn,
                'name' => $request->name,
<<<<<<< Updated upstream
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
=======
                'graduation_year' => $request->graduation_year,
                'class_name' => $request->class_name,
                'major' => $request->major,
                'verification_status' => 'pending',
            ]);

            AlumniProfile::create([
                'alumni_id' => $alumni->id,
                'current_status' => 'unemployed', // default status
            ]);

            return $this->success([
                'user' => $user,
                'alumni' => $alumni,
            ], 'Registrasi alumni berhasil. Menunggu verifikasi admin.', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
>>>>>>> Stashed changes
        }
    }

    /**
<<<<<<< Updated upstream
     * Statistik Tracer Study Alumni (Admin & Super Admin)
=======
     * Ambil data statistik Tracer Study untuk admin
>>>>>>> Stashed changes
     */
    public function tracerStudy(Request $request): JsonResponse
    {
        $user = $request->user();
<<<<<<< Updated upstream
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
=======

        if (!in_array($user->role, ['admin', 'super_admin'])) {
            return $this->forbidden('Hanya admin yang dapat melihat tracer study.');
        }

        $schoolId = $user->school_id;

        try {
            $query = Alumni::query();
            if ($schoolId) {
                $query->where('school_id', $schoolId);
            }

            $totalAlumni = $query->count();
            $verifiedAlumni = (clone $query)->where('verification_status', 'verified')->count();
            $pendingAlumni = (clone $query)->where('verification_status', 'pending')->count();

            // Tracer study status counts (working, studying, entrepreneur, unemployed)
            $stats = [
                'working' => 0,
                'studying' => 0,
                'entrepreneur' => 0,
                'unemployed' => 0,
            ];

            $profilesQuery = AlumniProfile::whereHas('alumni', function ($q) use ($schoolId) {
                if ($schoolId) {
                    $q->where('school_id', $schoolId);
                }
            });

            $currentStatuses = $profilesQuery->select('current_status', \DB::raw('count(*) as total'))
                ->groupBy('current_status')
                ->pluck('total', 'current_status')
                ->toArray();

            foreach ($stats as $key => $val) {
                $stats[$key] = $currentStatuses[$key] ?? 0;
            }

            return $this->success([
                'total_alumni' => $totalAlumni,
                'verified' => $verifiedAlumni,
                'pending' => $pendingAlumni,
                'tracer_study' => [
                    'working' => $stats['working'],
                    'studying' => $stats['studying'],
                    'entrepreneur' => $stats['entrepreneur'],
                    'unemployed' => $stats['unemployed'],
                ]
            ], 'Statistik tracer study berhasil dimuat.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
>>>>>>> Stashed changes
    }
}
