<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobVacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'school_id'      => $this->school_id,
            'posted_by'      => $this->posted_by,
            'title'          => $this->title,
            'company_name'   => $this->company_name,
            'company_logo'   => $this->company_logo ? asset('storage/' . $this->company_logo) : null,
            'location'       => $this->location,
            'job_type'       => $this->job_type,
            'job_type_label' => $this->job_type ? __($this->job_type) : null,
            'salary_range'   => $this->salary_range,
            'description'    => $this->description,
            'requirements'   => $this->requirements,
            'contact_email'  => $this->contact_email,
            'contact_phone'  => $this->contact_phone,
            'is_active'      => (bool) $this->is_active,
            'school'         => new SchoolResource($this->whenLoaded('school')),
            'posted_by_user' => new UserResource($this->whenLoaded('postedBy')),
            'created_at'     => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at'     => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
