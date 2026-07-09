<?php

namespace App\Services;

use App\Models\User;
use App\Models\Alumni;
use App\Models\AlumniProfile;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class AlumniService
{
    /**
     * Registrasi alumni baru dengan database transaction
     */
    public function register(array $data): array
    {
        // 1. Cek paket sekolah untuk fitur alumni
        $school = School::find($data['school_id']);
        if ($school && $school->package_id) {
            $package = $school->package;
            if ($package && !$package->has_alumni) {
                throw new \Exception('Sekolah pilihan Anda tidak berlangganan paket yang mendukung fitur alumni.', 403);
            }
        }

        return DB::transaction(function () use ($data) {
            // 2. Buat user baru
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'phone'     => $data['phone'] ?? null,
                'password'  => Hash::make($data['password']),
                'role'      => 'alumni',
                'school_id' => $data['school_id'],
                'status'    => 'active',
            ]);

            // 3. Buat data alumni
            $alumni = Alumni::create([
                'school_id'           => $data['school_id'],
                'user_id'             => $user->id,
                'nisn'                => $data['nisn'],
                'name'                => $data['name'],
                'graduation_year'     => $data['graduation_year'],
                'class_name'          => $data['class_name'],
                'major'               => $data['major'],
                'verification_status' => 'pending',
            ]);

            // 4. Buat profil alumni default
            AlumniProfile::create([
                'alumni_id'      => $alumni->id,
                'current_status' => 'unemployed',
            ]);

            // 5. Kirim notifikasi database ke admin/super_admin
            $admins = User::role(['admin', 'super_admin'])->get();
            if ($admins->isNotEmpty()) {
                Notification::make()
                    ->title('Registrasi Alumni Baru')
                    ->body("Alumni **{$alumni->name}** (Lulusan {$alumni->graduation_year}) baru saja mendaftar. Menunggu verifikasi admin!")
                    ->info()
                    ->sendToDatabase($admins);
            }

            return [
                'user'   => $user,
                'alumni' => $alumni,
            ];
        });
    }

    /**
     * Ambil statistik tracer study
     */
    public function getTracerStudyStats(?int $schoolId): array
    {
        $query = Alumni::query();
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $totalAlumni = $query->count();
        $verifiedAlumni = (clone $query)->where('verification_status', 'verified')->count();
        $pendingAlumni = (clone $query)->where('verification_status', 'pending')->count();

        $stats = [
            'working'      => 0,
            'studying'     => 0,
            'entrepreneur' => 0,
            'unemployed'   => 0,
        ];

        $profilesQuery = AlumniProfile::whereHas('alumni', function ($q) use ($schoolId) {
            if ($schoolId) {
                $q->where('school_id', $schoolId);
            }
        });

        $currentStatuses = $profilesQuery->select('current_status', DB::raw('count(*) as total'))
            ->groupBy('current_status')
            ->pluck('total', 'current_status')
            ->toArray();

        foreach ($stats as $key => $val) {
            $stats[$key] = $currentStatuses[$key] ?? 0;
        }

        return [
            'total_alumni' => $totalAlumni,
            'verified'     => $verifiedAlumni,
            'pending'      => $pendingAlumni,
            'tracer_study' => [
                'working'      => $stats['working'],
                'studying'     => $stats['studying'],
                'entrepreneur' => $stats['entrepreneur'],
                'unemployed'   => $stats['unemployed'],
            ]
        ];
    }
}
