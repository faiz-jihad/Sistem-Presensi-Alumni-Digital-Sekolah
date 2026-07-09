<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class SendDailyRecapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Dikelola oleh Policy/Gate
    }

    public function rules(): array
    {
        return [
            'date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'date.date' => 'Format tanggal tidak valid.',
        ];
    }
}
