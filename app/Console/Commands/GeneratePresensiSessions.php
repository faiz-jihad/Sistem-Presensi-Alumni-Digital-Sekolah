<?php

namespace App\Console\Commands;

use App\Models\PresensiSession;
use App\Models\Schedule;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GeneratePresensiSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-presensi-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily presence sessions automatically based on active schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $dayOfWeek = strtolower($today->format('l')); // monday, tuesday, etc.
        $dateStr = $today->toDateString();

        $this->info("Memulai pembuatan sesi presensi otomatis untuk hari {$dayOfWeek} tanggal {$dateStr}...");

        // Ambil jadwal aktif untuk hari ini
        $schedules = Schedule::where('day', $dayOfWeek)
            ->where('is_active', true)
            ->with('classHour')
            ->get();

        if ($schedules->isEmpty()) {
            $this->warn("Tidak ada jadwal aktif ditemukan untuk hari ini.");
            return Command::SUCCESS;
        }

        $createdCount = 0;
        $skippedCount = 0;

        foreach ($schedules as $schedule) {
            // Check if already exists
            $exists = PresensiSession::where('schedule_id', $schedule->id)
                ->where('date', $dateStr)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            PresensiSession::create([
                'school_id' => $schedule->school_id,
                'schedule_id' => $schedule->id,
                'teacher_id' => $schedule->teacher_id,
                'date' => $dateStr,
                'start_time' => $schedule->classHour->start_time ?? '07:00:00',
                'end_time' => $schedule->classHour->end_time ?? '08:00:00',
                'status' => 'scheduled',
            ]);

            $createdCount++;
        }

        $this->info("Proses selesai. Berhasil membuat {$createdCount} sesi baru, melewatkan {$skippedCount} sesi yang sudah ada.");
        return Command::SUCCESS;
    }
}
