<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class GenerateQrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['teacher', 'admin', 'super_admin'], true);
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'integer', 'exists:presensi_sessions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.required' => 'ID sesi presensi wajib diisi.',
            'session_id.exists'   => 'Sesi presensi tidak ditemukan.',
        ];
    }
}
