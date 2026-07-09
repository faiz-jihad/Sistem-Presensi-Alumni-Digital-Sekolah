<?php

namespace App\Http\Requests\AlumniEvent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlumniEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Dikelola oleh Policy
    }

    public function rules(): array
    {
        $rules = [
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'event_date'   => 'required|date',
            'location'     => 'required|string|max:255',
            'banner_image' => 'nullable|image|max:2048',
            'is_active'    => 'nullable|boolean',
        ];

        if (in_array($this->user()->role, ['super_admin', 'admin'], true)) {
            $rules['approval_status'] = 'nullable|in:pending,approved,rejected';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Judul kegiatan wajib diisi.',
            'description.required'  => 'Deskripsi kegiatan wajib diisi.',
            'event_date.required'   => 'Tanggal kegiatan wajib diisi.',
            'event_date.date'       => 'Format tanggal tidak valid.',
            'location.required'     => 'Lokasi kegiatan wajib diisi.',
            'banner_image.image'    => 'Berkas banner harus berupa gambar.',
            'banner_image.max'      => 'Ukuran gambar banner maksimal 2MB.',
            'approval_status.in'    => 'Status persetujuan tidak valid.',
        ];
    }
}
