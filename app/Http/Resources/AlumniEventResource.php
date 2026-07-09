<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlumniEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'school_id'       => $this->school_id,
            'posted_by'       => $this->posted_by,
            'title'           => $this->title,
            'description'     => $this->description,
            'event_date'      => $this->event_date ? $this->event_date->toDateString() : null,
            'location'        => $this->location,
            'banner_image'    => $this->banner_image ? asset('storage/' . $this->banner_image) : null,
            'approval_status' => $this->approval_status,
            'is_active'       => (bool) $this->is_active,
            'school'          => new SchoolResource($this->whenLoaded('school')),
            'posted_by_user'  => new UserResource($this->whenLoaded('postedBy')),
            'created_at'      => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'      => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
