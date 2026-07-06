<?php

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManualAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role, ['teacher', 'admin', 'super_admin'], true);
    }

    public function rules(): array
    {
        return [
            'session_id'                => ['required', 'integer', 'exists:presensi_sessions,id'],
            'attendances'               => ['required', 'array', 'min:1'],
            'attendances.*.student_id'  => ['required', 'integer', 'exists:students,id'],
            'attendances.*.status'      => ['required', Rule::enum(AttendanceStatus::class)],
            'attendances.*.note'        => ['nullable', 'string', 'max:500'],
            'attendances.*.check_in_time' => ['nullable', 'date_format:H:i:s'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_id.required'               => 'ID sesi presensi wajib diisi.',
            'session_id.exists'                 => 'Sesi presensi tidak ditemukan.',
            'attendances.required'              => 'Data presensi tidak boleh kosong.',
            'attendances.*.student_id.required' => 'ID siswa wajib diisi.',
            'attendances.*.student_id.exists'   => 'Siswa tidak ditemukan.',
            'attendances.*.status.required'     => 'Status kehadiran wajib diisi.',
        ];
    }
}
