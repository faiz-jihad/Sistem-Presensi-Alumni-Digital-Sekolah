<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'class_id'       => $this->class_id,
            'parent_user_id' => $this->parent_user_id,
            'nis'            => $this->nis,
            'nisn'           => $this->nisn,
            'name'           => $this->name,
            'gender'         => $this->gender,
            'birth_date'     => $this->birth_date,
            'status'         => $this->status,
            'parent'         => new UserResource($this->whenLoaded('parent')),
            'parent_name'    => $this->parent?->name,
            'parent_phone'   => $this->parent?->phone,
            // Format status kehadiran jika dilewatkan secara temporer/load
            'attendance_status' => $this->when(isset($this->attendance_status), $this->attendance_status),
            'attendance_note'   => $this->when(isset($this->attendance_note), $this->attendance_note),
            'created_at'     => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'     => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
