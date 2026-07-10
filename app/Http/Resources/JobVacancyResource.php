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
            'job_type_label' => $this->job_type_label,
            'category'       => $this->category,
            'category_label' => $this->category_label,

            // Kirim salary yang sebenarnya
            'salary_min'     => $this->salary_min,
            'salary_max'     => $this->salary_max,
            'salary_formatted' => $this->salary_formatted,

            // Kirim deadline
            'deadline'       => $this->deadline?->toDateString(),

            'description'    => $this->description,
            'requirements'   => $this->requirements,
            'link'           => $this->link,
            'is_active'      => $this->is_active,

            'school'         => new SchoolResource($this->whenLoaded('school')),
            'posted_by_user' => new UserResource($this->whenLoaded('postedBy')),

            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
        ];
    }
}
