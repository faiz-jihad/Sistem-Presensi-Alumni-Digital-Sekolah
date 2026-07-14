<?php

namespace App\Observers;

use App\Models\Schedule;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ScheduleObserver
{
    public function saved(Schedule $schedule): void
    {
        try {
            $teacher = $schedule->teacher;

            if (! $teacher?->user) {
                return;
            }

            $subjectName = $schedule->subject?->name ?? 'Mata Pelajaran';
            $className = $schedule->class?->name ?? 'Kelas';
            $day = $this->dayLabel($schedule->day);

            Notification::make()
                ->title('Jadwal Mengajar Diperbarui')
                ->body("Anda memiliki jadwal mengajar baru/diperbarui: {$subjectName} di kelas {$className} untuk hari {$day}.")
                ->info()
                ->sendToDatabase($teacher->user);
        } catch (\Throwable $exception) {
            Log::error('Gagal mengirim notifikasi perubahan jadwal.', [
                'schedule_id' => $schedule->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function dayLabel(mixed $day): string
    {
        if (is_object($day) && method_exists($day, 'label')) {
            return (string) $day->label();
        }

        if ($day instanceof \BackedEnum) {
            return (string) $day->value;
        }

        return filled($day) ? (string) $day : '';
    }
}
