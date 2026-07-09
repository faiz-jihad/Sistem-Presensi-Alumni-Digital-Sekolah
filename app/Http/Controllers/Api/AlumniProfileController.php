<?php

namespace App\Http\Controllers\Api;

use App\Models\Alumni;
use App\Http\Requests\Alumni\UpdateAlumniProfileRequest;
use App\Http\Resources\AlumniResource;
use App\Services\AlumniProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AlumniProfileController extends BaseController
{
    public function __construct(
        private readonly AlumniProfileService $profileService
    ) {}

    /**
     * Tampilkan profil alumni yang sedang login
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        // Otorisasi role via Policy/Gate
        if ($user->role !== 'alumni') {
            return $this->forbidden('Hanya alumni yang memiliki data profil alumni.');
        }

        $alumni = Alumni::with(['profile', 'user', 'school'])->where('user_id', $user->id)->first();

        if (!$alumni) {
            return $this->error('Profil alumni tidak ditemukan.', 404);
        }

        if (!Gate::forUser($user)->allows('viewProfile', $alumni)) {
            return $this->forbidden();
        }

        return $this->success(
            new AlumniResource($alumni),
            'Profil alumni berhasil dimuat.'
        );
    }

    /**
     * Perbarui profil alumni yang sedang login
     */
    public function update(UpdateAlumniProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'alumni') {
            return $this->forbidden('Hanya alumni yang dapat memperbarui profil alumni.');
        }

        $alumni = Alumni::where('user_id', $user->id)->first();

        if (!$alumni) {
            return $this->error('Data alumni tidak ditemukan.', 404);
        }

        if (!Gate::forUser($user)->allows('updateProfile', $alumni)) {
            return $this->forbidden();
        }

        try {
            $updatedAlumni = $this->profileService->updateProfile($alumni, $user, $request->validated());

            return $this->success(
                new AlumniResource($updatedAlumni),
                'Profil alumni berhasil diperbarui.'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
