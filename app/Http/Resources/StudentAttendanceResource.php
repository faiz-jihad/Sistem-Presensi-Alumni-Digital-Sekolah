<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'date'                 => $this->date,
            'status'               => $this->status,
            'status_label'         => match ($this->status) {
                'present'    => 'Hadir',
                'late'       => 'Terlambat',
                'permission' => 'Izin',
                'sick'       => 'Sakit',
                'absent'     => 'Alpha',
                default      => $this->status,
            },
            'check_in_time'        => $this->check_in_time,
            'check_out_time'       => $this->check_out_time,
            'note'                 => $this->note,
            'attachment'           => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'verification_status'  => $this->verification_status,
            'verified_at'          => $this->verified_at?->toDateTimeString(),
            'presensi_session_id'  => $this->presensi_session_id,
            'session'              => $this->whenLoaded('presensiSession', fn () => [
                'id'         => $this->presensiSession->id,
                'date'       => $this->presensiSession->date,
                'status'     => $this->presensiSession->status,
                'start_time' => $this->presensiSession->start_time,
                'end_time'   => $this->presensiSession->end_time,
            ]),
            'student'              => $this->whenLoaded('student', fn () => [
                'id'   => $this->student->id,
                'name' => $this->student->name,
                'nis'  => $this->student->nis,
            ]),
            'class'                => $this->whenLoaded('class', fn () => [
                'id'    => $this->class->id,
                'name'  => $this->class->name,
                'grade' => $this->class->grade,
            ]),
            'teacher'              => $this->whenLoaded('teacher', fn () => [
                'id'   => $this->teacher->id,
                'name' => $this->teacher->name,
            ]),
            'created_at'           => $this->created_at?->toDateTimeString(),
            'updated_at'           => $this->updated_at?->toDateTimeString(),
        ];
    }
}
