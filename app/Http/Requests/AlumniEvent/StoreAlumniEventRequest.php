<?php

namespace App\Http\Requests\AlumniEvent;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlumniEventRequest extends FormRequest
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
            'banner_image' => 'nullable|image|max:2048', // max 2MB
        ];

        if ($this->user()->role === 'super_admin') {
            $rules['school_id'] = 'nullable|exists:schools,id';
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
            'school_id.exists'      => 'Sekolah yang dipilih tidak valid.',
        ];
    }
}
