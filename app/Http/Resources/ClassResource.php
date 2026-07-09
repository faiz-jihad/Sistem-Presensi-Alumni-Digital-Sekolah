<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'school_id'           => $this->school_id,
            'homeroom_teacher_id' => $this->homeroom_teacher_id,
            'name'                => $this->name,
            'grade'               => $this->grade,
            'major'               => $this->major,
            'students_count'      => $this->whenCounted('students'),
            'homeroom_teacher'    => new TeacherResource($this->whenLoaded('homeroomTeacher')),
            'students'            => StudentResource::collection($this->whenLoaded('students')),
            'created_at'          => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'          => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
