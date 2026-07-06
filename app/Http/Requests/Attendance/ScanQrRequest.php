<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class ScanQrRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Siswa yang scan QR
        return $this->user() && $this->user()->role === 'student';
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'min:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token QR wajib diisi.',
            'token.min'      => 'Token QR tidak valid.',
        ];
    }
}
