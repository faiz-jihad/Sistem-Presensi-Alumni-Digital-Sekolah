<?php

namespace App\Http\Requests\Alumni;

use Illuminate\Foundation\Http\FormRequest;

class VerifyAlumniRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Dikelola oleh Policy
    }

    public function rules(): array
    {
        return [
            'reason' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.max' => 'Alasan penolakan maksimal berisi 500 karakter.',
        ];
    }
}
