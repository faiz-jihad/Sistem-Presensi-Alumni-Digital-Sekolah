<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'student'         => $this->whenLoaded('student', fn () => [
                'id'     => $this->student->id,
                'name'   => $this->student->name,
                'nis'    => $this->student->nis,
                'gender' => $this->student->gender,
            ]),
            'status'          => $this->status?->value,
            'status_label'    => $this->status?->label(),
            'status_color'    => $this->status?->color(),
            'check_in_time'   => $this->check_in_time,
            'note'            => $this->note,
            'date'            => $this->date,
            'verification_status' => $this->verification_status,
            'verified_at'     => $this->verified_at?->toDateTimeString(),
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}
