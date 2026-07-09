<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'school_id'           => $this->school_id,
            'user_id'             => $this->user_id,
            'nisn'                => $this->nisn,
            'name'                => $this->name,
            'graduation_year'     => $this->graduation_year,
            'class_name'          => $this->class_name,
            'major'               => $this->major,
            'verification_status' => $this->verification_status,
            'verified_by'         => $this->verified_by,
            'verified_at'         => $this->verified_at ? $this->verified_at->toIso8601String() : null,
            'school'              => new SchoolResource($this->whenLoaded('school')),
            'user'                => new UserResource($this->whenLoaded('user')),
            'profile'             => $this->whenLoaded('profile'), // custom profile relation
            'created_at'          => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'          => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
