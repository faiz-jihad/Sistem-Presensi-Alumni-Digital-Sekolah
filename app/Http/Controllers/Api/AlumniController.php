<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Alumni\RegisterAlumniRequest;
use App\Http\Resources\AlumniResource;
use App\Services\AlumniService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AlumniController extends BaseController
{
    public function __construct(
        private readonly AlumniService $alumniService
    ) {}

    /**
     * Registrasi alumni baru (verification_status = pending)
     */
    public function register(RegisterAlumniRequest $request): JsonResponse
    {
        try {
            $result = $this->alumniService->register($request->validated());

            return $this->success([
                'user'   => $result['user'],
                'alumni' => new AlumniResource($result['alumni']),
            ], 'Registrasi alumni berhasil. Menunggu verifikasi admin.', 201);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $status = in_array($code, [400, 403, 404, 422], true) ? $code : 500;
            return $this->error($e->getMessage(), $status);
        }
    }

    /**
     * Ambil data statistik Tracer Study untuk admin
     */
    public function tracerStudy(Request $request): JsonResponse
    {
        $user = $request->user();

        // Cek otorisasi via Role/Policy (dalam hal ini admin/super_admin)
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            return $this->forbidden('Hanya admin yang dapat melihat tracer study.');
        }

        try {
            $stats = $this->alumniService->getTracerStudyStats($user->school_id);
            return $this->success($stats, 'Statistik tracer study berhasil dimuat.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
