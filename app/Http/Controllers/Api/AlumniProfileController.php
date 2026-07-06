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
     * Ambil data profil alumni login
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        
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
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $alumni = Alumni::where('user_id', $user->id)->first();
        if (!$alumni) {
            return $this->error('Data alumni tidak ditemukan untuk akun ini.', 404);
        }

        $profile = AlumniProfile::where('alumni_id', $alumni->id)->first();
        if (!$profile) {
            $profile = new AlumniProfile(['alumni_id' => $alumni->id]);
        }

        $validator = Validator::make($request->all(), [
            'current_status' => 'required|in:working,studying,entrepreneur,unemployed',
            'university_name' => 'nullable|string|max:255',
            'study_program' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'job_position' => 'nullable|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

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
    }
}
