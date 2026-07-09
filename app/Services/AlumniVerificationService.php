<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class AlumniVerificationService
{
    /**
     * Mengambil daftar alumni berdasarkan filter dan sekolah
     */
    public function listAlumni(User $user, array $filters): LengthAwarePaginator
    {
        $query = Alumni::with(['school:id,name', 'user:id,email', 'profile'])
            ->orderBy('created_at', 'desc');

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('verification_status', $filters['status']);
        }

        // Admin hanya melihat sekolahnya sendiri
        if ($user->role === 'admin' && $user->school_id) {
            $query->where('school_id', $user->school_id);
        }

        // Filter sekolah khusus super_admin
        if ($user->role === 'super_admin' && !empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->paginate(15);
    }

    /**
     * Menyetujui pendaftaran alumni
     */
    public function approveAlumni(Alumni $alumni, User $admin): Alumni
    {
        if ($alumni->verification_status !== 'pending') {
            throw new \Exception('Alumni ini sudah ' . ($alumni->verification_status === 'verified' ? 'terverifikasi' : 'ditolak') . '.', 422);
        }

        return DB::transaction(function () use ($alumni, $admin) {
            $alumni->update([
                'verification_status' => 'verified',
                'verified_by'         => $admin->id,
                'verified_at'         => now(),
                'verification_notes'  => null,
            ]);

            if ($alumni->user) {
                Notification::make()
                    ->title('Akun Anda Telah Diverifikasi ✅')
                    ->body("Selamat! Data alumni Anda telah disetujui oleh admin. Anda kini dapat mengakses semua fitur alumni.")
                    ->success()
                    ->sendToDatabase($alumni->user);
            }

            return $alumni;
        });
    }

    /**
     * Menolak pendaftaran alumni
     */
    public function rejectAlumni(Alumni $alumni, User $admin, ?string $reason): Alumni
    {
        if ($alumni->verification_status !== 'pending') {
            throw new \Exception('Alumni ini sudah ' . ($alumni->verification_status === 'verified' ? 'terverifikasi' : 'ditolak') . '.', 422);
        }

        return DB::transaction(function () use ($alumni, $admin, $reason) {
            $alumni->update([
                'verification_status' => 'rejected',
                'verified_by'         => $admin->id,
                'verified_at'         => now(),
                'verification_notes'  => $reason ?? 'Tidak ada alasan yang diberikan.',
            ]);

            if ($alumni->user) {
                Notification::make()
                    ->title('Pendaftaran Alumni Ditolak ❌')
                    ->body("Maaf, data alumni Anda ditolak. Alasan: " . ($reason ?? 'Tidak ada alasan yang diberikan.') . ". Silakan hubungi admin sekolah untuk informasi lebih lanjut.")
                    ->danger()
                    ->sendToDatabase($alumni->user);
            }

            return $alumni;
        });
    }

    /**
     * Reset status verifikasi kembali ke pending
     */
    public function resetAlumniToPending(Alumni $alumni, User $admin): Alumni
    {
        if ($alumni->verification_status !== 'rejected') {
            throw new \Exception('Hanya alumni yang ditolak yang dapat direset ke pending.', 422);
        }

        return DB::transaction(function () use ($alumni) {
            $alumni->update([
                'verification_status' => 'pending',
                'verified_by'         => null,
                'verified_at'         => null,
                'verification_notes'  => null,
            ]);

            if ($alumni->user) {
                Notification::make()
                    ->title('Status Anda Telah Direset 🔄')
                    ->body('Data alumni Anda telah direset ke status Menunggu Verifikasi. Silakan lengkapi data Anda kembali.')
                    ->info()
                    ->sendToDatabase($alumni->user);
            }

            return $alumni;
        });
    }

    /**
     * Mengambil statistik verifikasi
     */
    public function getVerificationStats(User $user): array
    {
        $query = Alumni::query();

        if ($user->role === 'admin' && $user->school_id) {
            $query->where('school_id', $user->school_id);
        }

        $total    = (clone $query)->count();
        $pending  = (clone $query)->where('verification_status', 'pending')->count();
        $verified = (clone $query)->where('verification_status', 'verified')->count();
        $rejected = (clone $query)->where('verification_status', 'rejected')->count();

        return [
            'total'    => $total,
            'pending'  => $pending,
            'verified' => $verified,
            'rejected' => $rejected,
        ];
    }
}
