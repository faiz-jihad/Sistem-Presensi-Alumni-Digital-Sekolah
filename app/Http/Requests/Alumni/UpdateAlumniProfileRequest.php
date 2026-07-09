<?php

namespace App\Http\Requests\Alumni;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlumniProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Dikelola oleh Policy
    }

    public function rules(): array
    {
        return [
            'current_status'  => 'required|in:working,studying,entrepreneur,unemployed,studying_working',
            'university_name' => 'nullable|required_if:current_status,studying,studying_working|string|max:255',
            'study_program'   => 'nullable|required_if:current_status,studying,studying_working|string|max:255',
            'company_name'    => 'nullable|required_if:current_status,working,studying_working|string|max:255',
            'job_position'    => 'nullable|required_if:current_status,working,studying_working|string|max:255',
            'business_name'   => 'nullable|required_if:current_status,entrepreneur|string|max:255',
            'city'            => 'nullable|string|max:100',
            'province'        => 'nullable|string|max:100',
            'whatsapp'        => 'nullable|string|max:20',
            'linkedin_url'    => 'nullable|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'current_status.required'      => 'Status saat ini wajib diisi.',
            'current_status.in'            => 'Status saat ini tidak valid.',
            'university_name.required_if'  => 'Nama universitas wajib diisi jika status studi aktif.',
            'study_program.required_if'    => 'Program studi wajib diisi jika status studi aktif.',
            'company_name.required_if'     => 'Nama perusahaan wajib diisi jika status bekerja aktif.',
            'job_position.required_if'     => 'Posisi pekerjaan wajib diisi jika status bekerja aktif.',
            'business_name.required_if'    => 'Nama usaha wajib diisi jika status wirausaha aktif.',
            'linkedin_url.url'             => 'Format URL LinkedIn tidak valid.',
        ];
    }
}
