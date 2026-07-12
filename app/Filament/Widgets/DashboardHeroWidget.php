<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentClass;
use App\Models\StudentAttendance;
use App\Models\Alumni;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class DashboardHeroWidget extends Widget
{
    protected string $view = 'filament.widgets.dashboard-hero-widget';

    protected static ?int $sort = -1;

    protected int | string | array $columnSpan = 'full';

    public ?string $classId = '';

    public function getClasses(): array
    {
        $user = auth()->user();
        $query = StudentClass::orderBy('name');
        if ($user->role !== 'super_admin' && $user->school_id) {
            $query->where('school_id', $user->school_id);
        }
        if ($user->role === 'teacher') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher) {
                $query->whereHas('schedules', fn($q) => $q->where('teacher_id', $teacher->id));
            }
        }
        return $query->pluck('name', 'id')->toArray();
    }

    public function getData(): array
    {
        $user = auth()->user();
        $today = Carbon::today();
        $schoolId = $user->role !== 'super_admin' ? $user->school_id : null;

        // Base queries
        $attendanceQuery = StudentAttendance::query();
        $studentQuery = Student::query();
        $teacherQuery = Teacher::query();
        $classQuery = StudentClass::query();
        $alumniQuery = Alumni::query();

        if ($schoolId) {
            $attendanceQuery->where('school_id', $schoolId);
            $studentQuery->where('school_id', $schoolId);
            $teacherQuery->where('school_id', $schoolId);
            $classQuery->where('school_id', $schoolId);
            $alumniQuery->where('school_id', $schoolId);
        }

        // Apply class filter if selected
        if (!empty($this->classId)) {
            $studentQuery->where('class_id', $this->classId);
            $attendanceQuery->whereHas('student', fn($q) => $q->where('class_id', $this->classId));
            $teacherQuery->whereHas('schedules', fn($q) => $q->where('class_id', $this->classId));
        }

        // Student counts (used for expected denominator if no records exist)
        $studentCount = $studentQuery->count();

        // Attendance summary for today
        $todayAttendances = (clone $attendanceQuery)->where('date', $today->toDateString());
        $totalToday = $todayAttendances->count();
        $presentToday = (clone $todayAttendances)->whereIn('status', ['present', 'late'])->count();
        
        // Show expected total student count as denominator if no attendance is logged yet
        $totalTodayDisplay = $totalToday > 0 ? $totalToday : $studentCount;
        $presentPercent = $totalTodayDisplay > 0 ? round(($presentToday / $totalTodayDisplay) * 100) : 0;

        // Attendance summary for this week
        $weekStart = $today->copy()->startOfWeek();
        $weekAttendances = (clone $attendanceQuery)->whereBetween('date', [$weekStart->toDateString(), $today->toDateString()]);
        $totalWeek = $weekAttendances->count();
        $presentWeek = (clone $weekAttendances)->whereIn('status', ['present', 'late'])->count();
        $weekPercent = $totalWeek > 0 ? round(($presentWeek / $totalWeek) * 100) : 0;

        // Grade distribution (pie chart data)
        $attendanceByStatus = (clone $attendanceQuery)->where('date', $today->toDateString())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'user_name' => $user?->name ?? 'Admin',
            'user_role' => $user?->role ?? 'admin',
            'today_formatted' => $today->translatedFormat('l, d F Y'),
            'greeting' => $this->getGreeting(),
            'total_students' => $studentCount,
            'total_teachers' => $teacherQuery->count(),
            'total_classes' => $classQuery->count(),
            'total_alumni' => $alumniQuery->count(),
            'present_today' => $presentToday,
            'total_today' => $totalTodayDisplay,
            'present_percent' => $presentPercent,
            'week_percent' => $weekPercent,
            'attendance_by_status' => $attendanceByStatus,
        ];
    }

    private function getGreeting(): string
    {
        $hour = (int) now()->format('H');
        if ($hour < 11) return 'Selamat Pagi';
        if ($hour < 15) return 'Selamat Siang';
        if ($hour < 18) return 'Selamat Sore';
        return 'Selamat Malam';
    }
}
