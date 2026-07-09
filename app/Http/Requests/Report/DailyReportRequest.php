<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class DailyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Dikelola oleh Policy
    }

    public function rules(): array
    {
        return [
            'class_id' => 'required|exists:classes,id',
            'date'     => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Kelas wajib dipilih.',
            'class_id.exists'   => 'Kelas yang dipilih tidak valid.',
            'date.date'         => 'Format tanggal tidak valid.',
        ];
    }
}
