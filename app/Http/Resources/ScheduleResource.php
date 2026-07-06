<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'class'       => [
                'id'   => $this->class?->id,
                'name' => $this->class?->name,
            ],
            'subject'     => [
                'id'   => $this->subject?->id,
                'name' => $this->subject?->name,
                'code' => $this->subject?->code,
            ],
            'teacher'     => [
                'id'   => $this->teacher?->id,
                'name' => $this->teacher?->name,
            ],
            'class_hour'  => [
                'id'         => $this->classHour?->id,
                'code'       => $this->classHour?->code,
                'start_time' => $this->classHour?->start_time,
                'end_time'   => $this->classHour?->end_time,
            ],
            'day'              => $this->day?->value,
            'day_label'        => $this->day?->label(),
            'room'             => $this->room,
            'is_active'        => $this->is_active,
            'allow_early_open' => $this->allow_early_open,
            'semester'         => [
                'id'   => $this->semester?->id,
                'name' => $this->semester?->name,
            ],
        ];
    }
}
