<?php

namespace App\Services;

use App\Models\Alumni;
use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AlumniProfileService
{
    /**
     * Memperbarui profil alumni dan menyinkronkan nomor HP user
     */
    public function updateProfile(Alumni $alumni, User $user, array $data): Alumni
    {
        return DB::transaction(function () use ($alumni, $user, $data) {
            $profileData = array_intersect_key($data, array_flip([
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
                ]));
            $profileData['profile_completed_at'] = now();

            AlumniProfile::updateOrCreate(
                ['alumni_id' => $alumni->id],
                $profileData
            );

            // Update nomor HP user jika whatsapp diisi
            if (!empty($data['whatsapp'])) {
                $user->update(['phone' => $data['whatsapp']]);
            }

            return $alumni->load('profile');
        });
    }
}
