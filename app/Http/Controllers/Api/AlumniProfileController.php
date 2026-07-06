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
<<<<<<< Updated upstream
     * Ambil data profil alumni login
=======
     * Tampilkan profil alumni yang sedang login
>>>>>>> Stashed changes
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
<<<<<<< Updated upstream
        
        $alumni = Alumni::with('school')->where('user_id', $user->id)->first();
        if (!$alumni) {
            return $this->error('Data alumni tidak ditemukan untuk akun ini.', 404);
        }

        $profile = AlumniProfile::where('alumni_id', $alumni->id)->first();

        $data = [
            'alumni' => $alumni,
            'profile' => $profile,
        ];

        return $this->success($data, 'Profil alumni berhasil diambil');
    }

    /**
     * Update data profil alumni login
=======

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
>>>>>>> Stashed changes
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

<<<<<<< Updated upstream
        $alumni = Alumni::where('user_id', $user->id)->first();
        if (!$alumni) {
            return $this->error('Data alumni tidak ditemukan untuk akun ini.', 404);
        }

        $profile = AlumniProfile::where('alumni_id', $alumni->id)->first();
        if (!$profile) {
            $profile = new AlumniProfile(['alumni_id' => $alumni->id]);
=======
        if ($user->role !== 'alumni') {
            return $this->forbidden('Hanya alumni yang dapat memperbarui profil alumni.');
        }

        $alumni = Alumni::where('user_id', $user->id)->first();

        if (!$alumni) {
            return $this->error('Data alumni tidak ditemukan.', 404);
>>>>>>> Stashed changes
        }

        $validator = Validator::make($request->all(), [
            'current_status' => 'required|in:working,studying,entrepreneur,unemployed',
<<<<<<< Updated upstream
            'university_name' => 'nullable|string|max:255',
            'study_program' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'job_position' => 'nullable|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
=======
            'university_name' => 'nullable|required_if:current_status,studying|string|max:255',
            'study_program' => 'nullable|required_if:current_status,studying|string|max:255',
            'company_name' => 'nullable|required_if:current_status,working|string|max:255',
            'job_position' => 'nullable|required_if:current_status,working|string|max:255',
            'business_name' => 'nullable|required_if:current_status,entrepreneur|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
>>>>>>> Stashed changes
            'whatsapp' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

<<<<<<< Updated upstream
        $profile->fill($request->all());
        $profile->save();

        // Optional update basic info
        $alumniData = [];
        if ($request->has('name')) {
            $alumniData['name'] = $request->name;
        }
        if ($request->has('phone')) {
            $alumniData['phone'] = $request->phone;
        }
        if (!empty($alumniData)) {
            $alumni->update($alumniData);
        }

        return $this->success([
            'alumni' => $alumni->fresh(),
            'profile' => $profile->fresh(),
        ], 'Profil alumni berhasil diperbarui');
=======
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
>>>>>>> Stashed changes
    }
}
