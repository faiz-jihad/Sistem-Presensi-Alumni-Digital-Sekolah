<?php

namespace App\Http\Controllers\Api;

use App\Models\Alumni;
use App\Models\AlumniProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlumniProfileController extends BaseController
{
    /**
     * Tampilkan profil alumni yang sedang login
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'alumni') {
            return $this->forbidden('Hanya alumni yang memiliki data profil alumni.');
        }

        $alumni = Alumni::with('profile')->where('user_id', $user->id)->first();

        if (!$alumni) {
            return $this->error('Profil alumni tidak ditemukan.', 404);
        }

        return $this->success([
            'alumni' => $alumni,
            'profile' => $alumni->profile,
        ], 'Profil alumni berhasil dimuat.');
    }

    /**
     * Perbarui profil alumni yang sedang login
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'alumni') {
            return $this->forbidden('Hanya alumni yang dapat memperbarui profil alumni.');
        }

        $alumni = Alumni::where('user_id', $user->id)->first();

        if (!$alumni) {
            return $this->error('Data alumni tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'current_status' => 'required|in:working,studying,entrepreneur,unemployed',
            'university_name' => 'nullable|required_if:current_status,studying|string|max:255',
            'study_program' => 'nullable|required_if:current_status,studying|string|max:255',
            'company_name' => 'nullable|required_if:current_status,working|string|max:255',
            'job_position' => 'nullable|required_if:current_status,working|string|max:255',
            'business_name' => 'nullable|required_if:current_status,entrepreneur|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'whatsapp' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $profile = AlumniProfile::updateOrCreate(
                ['alumni_id' => $alumni->id],
                $request->only([
                    'current_status',
                    'university_name',
                    'study_program',
                    'company_name',
                    'job_position',
                    'business_name',
                    'city',
                    'province',
                    'whatsapp',
                    'linkedin_url',
                ])
            );

            // Update user phone number if provided
            if ($request->has('whatsapp') && !empty($request->whatsapp)) {
                $user->update(['phone' => $request->whatsapp]);
            }

            return $this->success([
                'alumni' => $alumni->load('profile'),
            ], 'Profil alumni berhasil diperbarui.');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
