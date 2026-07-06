<?php

namespace App\Http\Controllers\Api;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Filament\Notifications\Notification;

class AlumniVerificationController extends BaseController
{
    /**
     * Daftar alumni menunggu verifikasi (pending).
     * Akses: admin (sekolahnya sendiri), super_admin (semua sekolah).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Alumni::with(['school:id,name', 'user:id,email', 'profile'])
            ->where('verification_status', 'pending')
            ->orderBy('created_at', 'asc');

        // Admin hanya melihat alumni dari sekolahnya sendiri
        if ($user->role === 'admin' && $user->school_id) {
            $query->where('school_id', $user->school_id);
        }

        $alumni = $query->paginate(15);

        return $this->success([
            'total'  => $alumni->total(),
            'data'   => $alumni->items(),
            'meta'   => [
                'current_page' => $alumni->currentPage(),
                'last_page'    => $alumni->lastPage(),
                'per_page'     => $alumni->perPage(),
            ],
        ], 'Daftar alumni menunggu verifikasi.');
    }

    /**
     * Daftar semua alumni dengan filter status verifikasi.
     * Akses: admin, super_admin.
     */
    public function list(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Alumni::with(['school:id,name', 'user:id,email', 'profile'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status verifikasi
        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        // Admin hanya melihat sekolahnya sendiri
        if ($user->role === 'admin' && $user->school_id) {
            $query->where('school_id', $user->school_id);
        }

        // Filter sekolah khusus super_admin
        if ($user->role === 'super_admin' && $request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        $alumni = $query->paginate(15);

        return $this->success([
            'total'  => $alumni->total(),
            'data'   => $alumni->items(),
            'meta'   => [
                'current_page' => $alumni->currentPage(),
                'last_page'    => $alumni->lastPage(),
                'per_page'     => $alumni->perPage(),
            ],
        ], 'Daftar alumni berhasil dimuat.');
    }

    /**
     * Detail satu data alumni.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user  = $request->user();
        $alumni = Alumni::with(['school:id,name', 'user:id,name,email', 'verifiedBy:id,name', 'profile'])->find($id);

        if (!$alumni) {
            return $this->notFound('Data alumni tidak ditemukan.');
        }

        // Admin hanya bisa melihat alumni di sekolahnya
        if ($user->role === 'admin' && $user->school_id && $alumni->school_id !== $user->school_id) {
            return $this->forbidden('Anda tidak memiliki akses ke data alumni ini.');
        }

        return $this->success($alumni, 'Detail alumni berhasil dimuat.');
    }

    /**
     * Setujui (verifikasi) akun alumni.
     * Akses: admin, super_admin.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return $this->notFound('Data alumni tidak ditemukan.');
        }

        if ($alumni->verification_status !== 'pending') {
            return $this->error(
                'Alumni ini sudah ' . ($alumni->verification_status === 'verified' ? 'terverifikasi' : 'ditolak') . '.',
                422
            );
        }

        // Admin hanya boleh memverifikasi alumni di sekolahnya
        if ($admin->role === 'admin' && $admin->school_id && $alumni->school_id !== $admin->school_id) {
            return $this->forbidden('Anda tidak memiliki akses untuk memverifikasi alumni ini.');
        }

        $alumni->update([
            'verification_status' => 'verified',
            'verified_by'         => $admin->id,
            'verified_at'         => now(),
            'verification_notes'  => null,
        ]);

        // Kirim notifikasi Filament ke alumni jika punya akun
        if ($alumni->user) {
            Notification::make()
                ->title('Akun Anda Telah Diverifikasi ✅')
                ->body("Selamat! Data alumni Anda telah disetujui oleh admin. Anda kini dapat mengakses semua fitur alumni.")
                ->success()
                ->sendToDatabase($alumni->user);
        }

        return $this->success([
            'id'                  => $alumni->id,
            'name'                => $alumni->name,
            'verification_status' => 'verified',
            'verified_at'         => $alumni->verified_at,
            'verified_by'         => $admin->name,
        ], 'Alumni berhasil diverifikasi.');
    }

    /**
     * Tolak akun alumni dengan alasan.
     * Akses: admin, super_admin.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $admin = $request->user();
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return $this->notFound('Data alumni tidak ditemukan.');
        }

        if ($alumni->verification_status !== 'pending') {
            return $this->error(
                'Alumni ini sudah ' . ($alumni->verification_status === 'verified' ? 'terverifikasi' : 'ditolak') . '.',
                422
            );
        }

        // Admin hanya boleh menolak alumni di sekolahnya
        if ($admin->role === 'admin' && $admin->school_id && $alumni->school_id !== $admin->school_id) {
            return $this->forbidden('Anda tidak memiliki akses untuk menolak alumni ini.');
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $alumni->update([
            'verification_status' => 'rejected',
            'verified_by'         => $admin->id,
            'verified_at'         => now(),
            'verification_notes'  => $request->reason ?? 'Tidak ada alasan yang diberikan.',
        ]);

        // Kirim notifikasi ke alumni
        if ($alumni->user) {
            Notification::make()
                ->title('Pendaftaran Alumni Ditolak ❌')
                ->body("Maaf, data alumni Anda ditolak. Alasan: " . ($request->reason ?? 'Tidak ada alasan yang diberikan.') . ". Silakan hubungi admin sekolah untuk informasi lebih lanjut.")
                ->danger()
                ->sendToDatabase($alumni->user);
        }

        return $this->success([
            'id'                   => $alumni->id,
            'name'                 => $alumni->name,
            'verification_status'  => 'rejected',
            'verification_notes'   => $alumni->verification_notes,
            'verified_at'          => $alumni->verified_at,
            'verified_by'          => $admin->name,
        ], 'Data alumni berhasil ditolak.');
    }

    /**
     * Reset status alumni yang ditolak kembali ke pending.
     * Akses: admin, super_admin.
     */
    public function resetToPending(Request $request, int $id): JsonResponse
    {
        $admin  = $request->user();
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return $this->notFound('Data alumni tidak ditemukan.');
        }

        if ($alumni->verification_status !== 'rejected') {
            return $this->error('Hanya alumni yang ditolak yang dapat direset ke pending.', 422);
        }

        // Admin hanya boleh mereset alumni di sekolahnya
        if ($admin->role === 'admin' && $admin->school_id && $alumni->school_id !== $admin->school_id) {
            return $this->forbidden('Anda tidak memiliki akses untuk mereset alumni ini.');
        }

        $alumni->update([
            'verification_status' => 'pending',
            'verified_by'         => null,
            'verified_at'         => null,
            'verification_notes'  => null,
        ]);

        // Notifikasi ke alumni bahwa data mereka bisa diajukan ulang
        if ($alumni->user) {
            Notification::make()
                ->title('Status Anda Telah Direset 🔄')
                ->body('Data alumni Anda telah direset ke status Menunggu Verifikasi. Silakan lengkapi data Anda kembali.')
                ->info()
                ->sendToDatabase($alumni->user);
        }

        return $this->success([
            'id'                  => $alumni->id,
            'name'                => $alumni->name,
            'verification_status' => 'pending',
        ], 'Status alumni berhasil direset ke pending.');
    }

    /**
     * Statistik ringkasan verifikasi alumni.
     * Akses: admin, super_admin.
     */
    public function stats(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Alumni::query();

        // Admin hanya melihat statistik sekolahnya
        if ($user->role === 'admin' && $user->school_id) {
            $query->where('school_id', $user->school_id);
        }

        $total    = (clone $query)->count();
        $pending  = (clone $query)->where('verification_status', 'pending')->count();
        $verified = (clone $query)->where('verification_status', 'verified')->count();
        $rejected = (clone $query)->where('verification_status', 'rejected')->count();

        return $this->success([
            'total'    => $total,
            'pending'  => $pending,
            'verified' => $verified,
            'rejected' => $rejected,
        ], 'Statistik verifikasi alumni berhasil dimuat.');
    }
}
