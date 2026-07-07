<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'date'            => $this->date,
            'status'          => $this->status?->value,
            'status_label'    => $this->status?->label(),
            'attendance_method' => $this->attendance_method ?? 'manual',
            'start_time'      => $this->start_time,
            'end_time'        => $this->end_time,
            'opened_at'       => $this->opened_at?->toTimeString(),
            'closed_at'       => $this->closed_at?->toTimeString(),
            'material_topic'  => $this->material_topic,
            'notes'           => $this->notes,
            'class' => $this->whenLoaded('class', fn () => $this->class ? [
                'id' => $this->class->id,
                'name' => $this->class->name,
            ] : null),
            'schedule'        => $this->whenLoaded('schedule', fn () => $this->schedule ? [
                'id'      => $this->schedule->id,
                'class'   => [
                    'id'   => $this->schedule->class?->id,
                    'name' => $this->schedule->class?->name,
                ],
                'subject' => [
                    'id'   => $this->schedule->subject?->id,
                    'name' => $this->schedule->subject?->name,
                ],
                'class_hour' => [
                    'start_time' => $this->schedule->classHour?->start_time,
                    'end_time'   => $this->schedule->classHour?->end_time,
                ],
                'day'              => $this->schedule->day?->value,
                'day_label'        => $this->schedule->day?->label(),
                'allow_early_open' => $this->schedule->allow_early_open,
            ] : null),
            'teacher'         => $this->whenLoaded('teacher', fn () => [
                'id'   => $this->teacher->id,
                'name' => $this->teacher->name,
            ]),
            'opened_by'       => $this->whenLoaded('openedBy', fn () => [
                'id'   => $this->openedBy?->id,
                'name' => $this->openedBy?->name,
            ]),
            'closed_by'       => $this->whenLoaded('closedBy', fn () => [
                'id'   => $this->closedBy?->id,
                'name' => $this->closedBy?->name,
            ]),
            'attendance_records' => $this->whenLoaded('studentAttendances', fn () =>
                AttendanceRecordResource::collection($this->studentAttendances)
            ),
            'students' => $this->when(
                $this->relationLoaded('studentAttendances')
                    && (
                        (
                            $this->relationLoaded('class')
                            && $this->class?->relationLoaded('students')
                        )
                        || (
                            $this->relationLoaded('schedule')
                            && $this->schedule?->relationLoaded('class')
                            && $this->schedule?->class?->relationLoaded('students')
                        )
                    ),
                function () {
                    $records = $this->studentAttendances->keyBy('student_id');
                    $students = $this->class?->students
                        ?? $this->schedule?->class?->students
                        ?? collect();

                    return $students->map(function ($student) use ($records) {
                        $record = $records->get($student->id);

                        return [
                            'id' => $student->id,
                            'name' => $student->name,
                            'nis' => $student->nis,
                            'status' => $record?->status?->value,
                            'status_label' => $record?->status?->label(),
                            'check_in_time' => $record?->check_in_time,
                            'scanned_at' => $record?->scanned_at?->toDateTimeString(),
                        ];
                    })->values();
                }
            ),
            'total_records'   => $this->whenLoaded('studentAttendances', fn () =>
                $this->studentAttendances->count()
            ),
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}
