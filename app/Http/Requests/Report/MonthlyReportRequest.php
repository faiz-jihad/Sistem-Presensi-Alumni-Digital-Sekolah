<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Dikelola oleh Policy
    }

    public function rules(): array
    {
        return [
            'class_id' => 'required|exists:classes,id',
            'month'    => 'required|integer|between:1,12',
            'year'     => 'required|integer|min:2020|max:2050',
        ];
    }

    public function messages(): array
    {
        return [
            'class_id.required' => 'Kelas wajib dipilih.',
            'class_id.exists'   => 'Kelas yang dipilih tidak valid.',
            'month.required'    => 'Bulan wajib dipilih.',
            'month.between'     => 'Bulan harus bernilai antara 1 sampai 12.',
            'year.required'     => 'Tahun wajib diisi.',
            'year.min'          => 'Tahun minimal 2020.',
            'year.max'          => 'Tahun maksimal 2050.',
        ];
    }
}
