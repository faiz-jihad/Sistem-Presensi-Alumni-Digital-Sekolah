<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Alumni;
use App\Models\AlumniProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AlumniController extends BaseController
{
    /**
     * Registrasi alumni baru (verification_status = pending)
     */
    public function register(Request $request): JsonResponse
    {
        $school = \App\Models\School::find($request->school_id);
        if ($school && $school->package_id) {
            $package = $school->package;
            if ($package && !$package->has_alumni) {
                return $this->error('Sekolah pilihan Anda tidak berlangganan paket yang mendukung fitur alumni.', 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'school_id' => 'required|exists:schools,id',
            'nisn' => 'required|string|max:20',
            'graduation_year' => 'required|integer|min:1990|max:2050',
            'class_name' => 'required|string|max:50',
            'major' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'alumni',
                'school_id' => $request->school_id,
                'status' => 'active',
            ]);

            $alumni = Alumni::create([
                'school_id' => $request->school_id,
                'user_id' => $user->id,
                'nisn' => $request->nisn,
                'name' => $request->name,
                'graduation_year' => $request->graduation_year,
                'class_name' => $request->class_name,
                'major' => $request->major,
                'verification_status' => 'pending',
            ]);

            AlumniProfile::create([
                'alumni_id' => $alumni->id,
                'current_status' => 'unemployed', // default status
            ]);

            $admins = \App\Models\User::role(['admin', 'super_admin'])->get();
            if ($admins->isNotEmpty()) {
                \Filament\Notifications\Notification::make()
                    ->title('Registrasi Alumni Baru')
                    ->body("Alumni **{$request->name}** (Lulusan {$request->graduation_year}) baru saja mendaftar. Menunggu verifikasi admin!")
                    ->info()
                    ->sendToDatabase($admins);
            }

            return $this->success([
                'user' => $user,
                'alumni' => $alumni,
            ], 'Registrasi alumni berhasil. Menunggu verifikasi admin.', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Ambil data statistik Tracer Study untuk admin
     */
    public function tracerStudy(Request $request): JsonResponse
    {
        $user = $request->user();

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
    }
}
