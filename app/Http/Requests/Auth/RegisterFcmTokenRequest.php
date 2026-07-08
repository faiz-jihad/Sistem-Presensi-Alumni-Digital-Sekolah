<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFcmTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'device_type' => ['required', 'string', 'in:android,ios,web'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token FCM wajib diisi.',
            'device_type.required' => 'Tipe perangkat wajib diisi.',
            'device_type.in' => 'Tipe perangkat tidak valid.',
        ];
    }
}
