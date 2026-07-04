<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PresensiSession;
use App\Models\Schedule;
use App\Models\ClassHour;
use Carbon\Carbon;

class PresensiSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat sesi presensi untuk semua jadwal yang aktif hari ini
        $today = Carbon::today()->toDateString();
        $schedules = Schedule::where('is_active', true)->get();

        foreach ($schedules as $schedule) {
            // Pastikan ada class hour terkait
            $classHour = $schedule->classHour;
            if (! $classHour) {
                continue;
            }

            PresensiSession::firstOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'date'        => $today,
                ],
                [
                    'school_id'  => $schedule->school_id,
                    'teacher_id' => $schedule->teacher_id,
                    'start_time' => $classHour->start_time,
                    'end_time'   => $classHour->end_time,
                    'status'     => 'open',
                ]
            );
        }
    }
}
