<?php

namespace App\Filament\Widgets;

use App\Enums\DayOfWeek;
use App\Enums\SessionStatus;
use App\Models\PresensiSession;
use App\Models\Schedule;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class RealtimeAttendanceOverviewWidget extends Widget
{
    protected string $view = 'filament.widgets.realtime-attendance-overview';

    protected static ?int $sort = 2; // Renders right below stats

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = '15s';

    public function getData(): array
    {
        $today     = Carbon::today();
        $todayDay  = DayOfWeek::fromCarbon($today);
        $todayDate = $today->toDateString();
        $now       = Carbon::now();

        $query = Schedule::with([
            'class',
            'subject',
            'teacher',
            'classHour',
            'presensiSessions' => fn ($q) => $q->where('date', $todayDate),
        ])
            ->where('is_active', true)
            ->where('day', $todayDay->value);

        // If the logged in user is a teacher, filter to only show their schedules
        $user = auth()->user();
        if ($user && $user->hasRole('teacher')) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $schedules = $query->get();

        $stats = [
            'total'     => $schedules->count(),
            'open'      => 0,
            'closed'    => 0,
            'unopened'  => 0,
            'missed'    => 0,
        ];

        $classes = $schedules->map(function ($schedule) use ($todayDate, $now, &$stats) {
            $session   = $schedule->presensiSessions->first();
            $classHour = $schedule->classHour;

            $startTime = $classHour?->start_time;
            $endTime   = $classHour?->end_time;

            $startCarbon = $startTime ? Carbon::parse($todayDate . ' ' . $startTime) : null;
            $endCarbon   = $endTime   ? Carbon::parse($todayDate . ' ' . $endTime)   : null;

            // Resolve status
            $status = 'unopened'; // default belum dibuka
            $statusLabel = 'Belum Dibuka';
            $color = 'warning';

            if ($session) {
                if ($session->status === SessionStatus::Open) {
                    $status = 'open';
                    $statusLabel = 'Sedang Berlangsung';
                    $color = 'success';
                    $stats['open']++;
                } elseif ($session->status === SessionStatus::Closed) {
                    $status = 'closed';
                    $statusLabel = 'Selesai';
                    $color = 'info';
                    $stats['closed']++;
                }
            } else {
                if ($endCarbon && $now->greaterThan($endCarbon)) {
                    $status = 'missed';
                    $statusLabel = 'Terlewat / Alpha';
                    $color = 'danger';
                    $stats['missed']++;
                } else {
                    $stats['unopened']++;
                }
            }

            return [
                'id'           => $schedule->id,
                'class_name'   => $schedule->class?->name ?? '-',
                'subject_name' => $schedule->subject?->name ?? '-',
                'teacher_name' => $schedule->teacher?->name ?? '-',
                'time_range'   => $startTime && $endTime ? substr($startTime, 0, 5) . ' - ' . substr($endTime, 0, 5) : '-',
                'status'       => $status,
                'status_label' => $statusLabel,
                'color'        => $color,
                'opened_at'    => $session?->opened_at ? $session->opened_at->format('H:i') : null,
                'closed_at'    => $session?->closed_at ? $session->closed_at->format('H:i') : null,
            ];
        });

        // Urutkan biar yang sedang berlangsung (open) ada paling atas
        $sortedClasses = $classes->sortBy(function ($item) {
            return match ($item['status']) {
                'open'     => 0,
                'unopened' => 1,
                'closed'   => 2,
                'missed'   => 3,
                default    => 4,
            };
        })->values()->toArray();

        return [
            'classes' => $sortedClasses,
            'stats'   => $stats,
        ];
    }
}
