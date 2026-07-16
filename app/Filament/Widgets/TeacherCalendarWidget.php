<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Teacher;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class TeacherCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.teacher-calendar-widget';

    protected int | string | array $columnSpan = 'full';

    public int $month;
    public int $year;

    public static function canView(): bool
    {
        return auth()->user()->role === 'teacher';
    }

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function previousMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::createFromDate($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function getData(): array
    {
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return [
                'days' => [],
                'month_name' => '',
                'year' => $this->year,
                'teacher_name' => 'Guru',
            ];
        }

        // Fetch teacher teaching schedules
        $teachingSchedules = Schedule::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->with(['class', 'subject', 'classHour'])
            ->get();

        // Fetch corresponding break schedules
        $teacherSemesterIds = $teachingSchedules->pluck('semester_id')->unique()->filter()->values();
        
        if ($teacherSemesterIds->isEmpty()) {
            $teacherSemesterIds = \App\Models\Semester::where('is_active', true)
                ->whereHas('academicYear', function ($q) use ($teacher) {
                    $q->where('school_id', $teacher->school_id);
                })
                ->pluck('id');
        }

        $breakSchedules = Schedule::where('school_id', $teacher->school_id)
            ->whereNull('teacher_id')
            ->whereIn('semester_id', $teacherSemesterIds)
            ->where('is_active', true)
            ->with(['classHour'])
            ->get();

        // Combine teaching and break schedules
        $schedules = $teachingSchedules->concat($breakSchedules);

        // Group schedules by day value (e.g. 'monday', 'tuesday')
        $schedulesByDay = [];
        foreach ($schedules as $schedule) {
            $dayValue = $schedule->day->value; // DayOfWeek enum value
            $schedulesByDay[$dayValue][] = $schedule;
        }

        // Generate calendar grid
        $startOfMonth = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();
        
        // Month name in Indonesian
        $monthName = $startOfMonth->translatedFormat('F');

        $daysInMonth = [];
        
        // Day of week for start of month (1 = Mon, 7 = Sun)
        $startDayOfWeek = $startOfMonth->dayOfWeekIso;

        // Add blank days for padding before start of month
        for ($i = 1; $i < $startDayOfWeek; $i++) {
            $daysInMonth[] = [
                'date' => null,
                'is_today' => false,
                'schedules' => [],
            ];
        }

        // Add actual days
        for ($day = 1; $day <= $endOfMonth->day; $day++) {
            $currentDate = Carbon::createFromDate($this->year, $this->month, $day);
            $dayOfWeekName = strtolower($currentDate->format('l')); // 'monday', 'tuesday', etc.
            
            $daysInMonth[] = [
                'date' => $day,
                'is_today' => $currentDate->isToday(),
                'schedules' => $schedulesByDay[$dayOfWeekName] ?? [],
            ];
        }

        return [
            'days' => $daysInMonth,
            'month_name' => $monthName,
            'year' => $this->year,
            'teacher_name' => $teacher->name,
        ];
    }
}
