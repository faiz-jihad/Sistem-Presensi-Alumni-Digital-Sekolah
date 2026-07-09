<?php

namespace App\Http\Controllers\Api;

use App\Models\Alumni;
use App\Http\Requests\Alumni\VerifyAlumniRequest;
use App\Http\Resources\AlumniResource;
use App\Services\AlumniVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AlumniVerificationController extends BaseController
{
    public function __construct(
        private readonly AlumniVerificationService $verificationService
    ) {}

    /**
     * Daftar alumni menunggu verifikasi (pending).
     * Akses: admin (sekolahnya sendiri), super_admin (semua sekolah).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = ['status' => 'pending'];
            $alumni = $this->verificationService->listAlumni($request->user(), $filters);

            return $this->success([
                'total'  => $alumni->total(),
                'data'   => AlumniResource::collection($alumni->items()),
                'meta'   => [
                    'current_page' => $alumni->currentPage(),
                    'last_page'    => $alumni->lastPage(),
                    'per_page'     => $alumni->perPage(),
                ],
            ], 'Daftar alumni menunggu verifikasi.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Daftar semua alumni dengan filter status verifikasi.
     * Akses: admin, super_admin.
     */
    public function list(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'school_id']);
            $alumni = $this->verificationService->listAlumni($request->user(), $filters);

            return $this->success([
                'total'  => $alumni->total(),
                'data'   => AlumniResource::collection($alumni->items()),
                'meta'   => [
                    'current_page' => $alumni->currentPage(),
                    'last_page'    => $alumni->lastPage(),
                    'per_page'     => $alumni->perPage(),
                ],
            ], 'Daftar alumni berhasil dimuat.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Detail satu data alumni.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $alumni = Alumni::with(['school:id,name', 'user:id,name,email', 'verifiedBy:id,name', 'profile'])->find($id);

        if (!$alumni) {
            return $this->notFound('Data alumni tidak ditemukan.');
        }

        // Otorisasi via Policy
        if (!Gate::forUser($request->user())->allows('view', $alumni)) {
            return $this->forbidden('Anda tidak memiliki akses ke data alumni ini.');
        }

        return $this->success(
            new AlumniResource($alumni),
            'Detail alumni berhasil dimuat.'
        );
    }

    /**
     * Setujui (verifikasi) akun alumni.
     * Akses: admin, super_admin.
     */
    public function approve(Request $request, int $id): JsonResponse
    {
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return $this->notFound('Data alumni tidak ditemukan.');
        }

        // Otorisasi via Policy
        if (!Gate::forUser($request->user())->allows('verify', $alumni)) {
            return $this->forbidden('Anda tidak memiliki akses untuk memverifikasi alumni ini.');
        }

        try {
            $verifiedAlumni = $this->verificationService->approveAlumni($alumni, $request->user());

            return $this->success([
                'id'                  => $verifiedAlumni->id,
                'name'                => $verifiedAlumni->name,
                'verification_status' => 'verified',
                'verified_at'         => $verifiedAlumni->verified_at,
                'verified_by'         => $request->user()->name,
            ], 'Alumni berhasil diverifikasi.');
        } catch (\Exception $e) {
            $code = $e->getCode();
            $status = in_array($code, [400, 403, 404, 422], true) ? $code : 500;
            return $this->error($e->getMessage(), $status);
        }
    }

    /**
     * Tolak akun alumni dengan alasan.
     * Akses: admin, super_admin.
     */
    public function reject(VerifyAlumniRequest $request, int $id): JsonResponse
    {
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return $this->notFound('Data alumni tidak ditemukan.');
        }

        // Otorisasi via Policy
        if (!Gate::forUser($request->user())->allows('verify', $alumni)) {
            return $this->forbidden('Anda tidak memiliki akses untuk menolak alumni ini.');
        }

        try {
            $rejectedAlumni = $this->verificationService->rejectAlumni($alumni, $request->user(), $request->validated('reason'));

            return $this->success([
                'id'                   => $rejectedAlumni->id,
                'name'                 => $rejectedAlumni->name,
                'verification_status'  => 'rejected',
                'verification_notes'   => $rejectedAlumni->verification_notes,
                'verified_at'          => $rejectedAlumni->verified_at,
                'verified_by'          => $request->user()->name,
            ], 'Data alumni berhasil ditolak.');
        } catch (\Exception $e) {
            $code = $e->getCode();
            $status = in_array($code, [400, 403, 404, 422], true) ? $code : 500;
            return $this->error($e->getMessage(), $status);
        }
    }

    /**
     * Reset status alumni yang ditolak kembali ke pending.
     * Akses: admin, super_admin.
     */
    public function resetToPending(Request $request, int $id): JsonResponse
    {
        $alumni = Alumni::with('user')->find($id);

        if (!$alumni) {
            return $this->notFound('Data alumni tidak ditemukan.');
        }

        // Otorisasi via Policy
        if (!Gate::forUser($request->user())->allows('verify', $alumni)) {
            return $this->forbidden('Anda tidak memiliki akses untuk mereset alumni ini.');
        }

        try {
            $resetAlumni = $this->verificationService->resetAlumniToPending($alumni, $request->user());

            return $this->success([
                'id'                  => $resetAlumni->id,
                'name'                => $resetAlumni->name,
                'verification_status' => 'pending',
            ], 'Status alumni berhasil direset ke pending.');
        } catch (\Exception $e) {
            $code = $e->getCode();
            $status = in_array($code, [400, 403, 404, 422], true) ? $code : 500;
            return $this->error($e->getMessage(), $status);
        }
    }

    /**
     * Statistik ringkasan verifikasi alumni.
     * Akses: admin, super_admin.
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            return $this->forbidden('Hanya admin yang dapat melihat statistik.');
        }

        try {
            $stats = $this->verificationService->getVerificationStats($user);
            return $this->success($stats, 'Statistik verifikasi alumni berhasil dimuat.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
