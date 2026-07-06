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

    public function getData(): array
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Attendance summary for today
        $todayAttendances = StudentAttendance::where('date', $today->toDateString());
        $totalToday = $todayAttendances->count();
        $presentToday = (clone $todayAttendances)->whereIn('status', ['present', 'late'])->count();
        $presentPercent = $totalToday > 0 ? round(($presentToday / $totalToday) * 100) : 0;

        // Attendance summary for this week
        $weekStart = $today->copy()->startOfWeek();
        $weekAttendances = StudentAttendance::whereBetween('date', [$weekStart->toDateString(), $today->toDateString()]);
        $totalWeek = $weekAttendances->count();
        $presentWeek = (clone $weekAttendances)->whereIn('status', ['present', 'late'])->count();
        $weekPercent = $totalWeek > 0 ? round(($presentWeek / $totalWeek) * 100) : 0;

        // Grade distribution (pie chart data)
        $attendanceByStatus = StudentAttendance::where('date', $today->toDateString())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'user_name' => $user?->name ?? 'Admin',
            'user_role' => $user?->role ?? 'admin',
            'today_formatted' => $today->translatedFormat('l, d F Y'),
            'greeting' => $this->getGreeting(),
            'total_students' => Student::count(),
            'total_teachers' => Teacher::count(),
            'total_classes' => StudentClass::count(),
            'total_alumni' => Alumni::count(),
            'present_today' => $presentToday,
            'total_today' => $totalToday,
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
