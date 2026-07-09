<?php

namespace App\Http\Requests\Alumni;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAlumniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Siapapun bisa mendaftar sebagai alumni
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users,email',
            'phone'           => 'nullable|string|max:20',
            'password'        => 'required|string|min:6',
            'school_id'       => 'required|exists:schools,id',
            'nisn'            => 'required|string|max:20',
            'graduation_year' => 'required|integer|min:1990|max:2050',
            'class_name'      => 'required|string|max:50',
            'major'           => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'            => 'Nama wajib diisi.',
            'email.required'           => 'Email wajib diisi.',
            'email.email'              => 'Format email tidak valid.',
            'email.unique'             => 'Email sudah terdaftar.',
            'password.required'        => 'Kata sandi wajib diisi.',
            'password.min'             => 'Kata sandi minimal berisi 6 karakter.',
            'school_id.required'       => 'Sekolah wajib dipilih.',
            'school_id.exists'         => 'Sekolah yang dipilih tidak valid.',
            'nisn.required'            => 'NISN wajib diisi.',
            'graduation_year.required' => 'Tahun kelulusan wajib diisi.',
            'class_name.required'      => 'Nama kelas wajib diisi.',
            'major.required'           => 'Jurusan wajib diisi.',
        ];
    }
}
