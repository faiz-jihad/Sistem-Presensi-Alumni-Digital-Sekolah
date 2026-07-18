<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrayerAttendanceResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'student_name' => $this->student?->name ?? '-',
            'student_number' => $this->student?->nis,
            'class_id' => $this->class_id,
            'class_name' => $this->studentClass?->name,
            'prayer_type' => $this->prayer_type,
            'attendance_date' => $this->attendance_date?->toDateString(),
            'scheduled_at' => $this->scheduled_at,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'status' => $this->status,
            'verified_by' => $this->verified_by,
            'verifier_name' => $this->verifier?->name,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'teacher_note' => $this->teacher_note,
        ];
    }
}
