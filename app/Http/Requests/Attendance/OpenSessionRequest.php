<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class OpenSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['teacher', 'admin', 'super_admin'], true);
    }

    public function rules(): array
    {
        return [
            'schedule_id' => ['required_without:class_id', 'integer', 'exists:schedules,id'],
            'class_id' => ['required_without:schedule_id', 'integer', 'exists:classes,id'],
            'date' => ['sometimes', 'nullable', 'date'],
            'tanggal' => ['sometimes', 'nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'schedule_id.required' => 'Jadwal pelajaran wajib dipilih.',
            'schedule_id.exists'   => 'Jadwal pelajaran tidak ditemukan.',
        ];
    }
}
