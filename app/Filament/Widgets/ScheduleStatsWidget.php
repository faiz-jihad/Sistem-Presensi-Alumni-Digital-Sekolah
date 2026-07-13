<?php

namespace App\Filament\Widgets;

use App\Models\School;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\Subject;
use Filament\Widgets\Widget;

class ScheduleStatsWidget extends Widget
{
    protected string $view = 'filament.widgets.schedule-stats-widget';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin']);
    }

    public function getData(): array
    {
        $user = auth()->user();
        $schoolId = $user->role !== 'super_admin' ? $user->school_id : null;

        $schoolQuery = School::query();
        $classQuery = StudentClass::query();
        $teacherQuery = Teacher::query();
        $subjectQuery = Subject::query();

        if ($schoolId) {
            $schoolQuery->where('id', $schoolId);
            $classQuery->where('school_id', $schoolId);
            $teacherQuery->where('school_id', $schoolId);
            $subjectQuery->where('school_id', $schoolId);
        }

        return [
            'total_schools' => $schoolQuery->count(),
            'total_classes' => $classQuery->count(),
            'total_teachers' => $teacherQuery->count(),
            'total_subjects' => $subjectQuery->count(),
            'is_super_admin' => $user->role === 'super_admin',
        ];
    }
}
