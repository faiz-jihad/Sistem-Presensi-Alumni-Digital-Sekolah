<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Schedule;
use App\Enums\DayOfWeek;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class SendTeacherScheduleReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-teacher-schedule-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat jadwal mengajar ke guru 15 menit sebelum kelas dimulai';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetTime = now()->addMinutes(15)->format('H:i');
        $today = now()->toDateString();
        
        try {
            $dayOfWeek = DayOfWeek::fromCarbon(now());
        } catch (\ValueError $e) {
            $this->error("Hari hari ini tidak valid dalam sistem.");
            return 1;
        }

        $schedules = Schedule::query()
            ->where('is_active', true)
            ->where('day', $dayOfWeek)
            ->where(function ($query) use ($today) {
                $query->whereNull('effective_start_date')
                      ->orWhere('effective_start_date', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('effective_end_date')
                      ->orWhere('effective_end_date', '>=', $today);
            })
            ->whereHas('classHour', function ($query) use ($targetTime) {
                $query->where('start_time', 'like', $targetTime . '%');
            })
            ->with(['teacher.user', 'class', 'subject', 'classHour'])
            ->get();

        if ($schedules->isEmpty()) {
            $this->info("Tidak ada jadwal mengajar yang dimulai pada pukul " . now()->addMinutes(15)->format('H:i:s'));
            return 0;
        }

        foreach ($schedules as $schedule) {
            $teacher = $schedule->teacher;
            if ($teacher && $teacher->user) {
                $subjectName = $schedule->subject?->name ?? 'Mata Pelajaran';
                $className = $schedule->class?->name ?? 'Kelas';
                $startTime = $schedule->classHour ? Carbon::parse($schedule->classHour->start_time)->format('H:i') : '00:00';
                
                Notification::make()
                    ->title('Pengingat Jadwal Mengajar 🗓️')
                    ->body("Yth. Bapak/Ibu Guru, kelas Anda untuk mata pelajaran **{$subjectName}** di kelas **{$className}** akan dimulai dalam 15 menit (pukul **{$startTime}**). Harap mempersiapkan diri untuk melakukan presensi.")
                    ->info()
                    ->sendToDatabase($teacher->user);

                $this->info("Mengirim pengingat ke guru: {$teacher->name} untuk kelas {$className} pukul {$startTime}");
            }
        }

        return 0;
    }
}
