<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDailyAttendanceRecap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-recap {--date= : The date for the recap (YYYY-MM-DD), defaults to today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily attendance summary to parents via WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle(ReportService $reportService)
    {
        $date = $this->option('date') ?: Carbon::today()->toDateString();

        $this->info("Memulai pengiriman rekap harian untuk tanggal {$date}...");

        try {
            $sent = $reportService->sendDailyRecapToParents($date);
            $this->info("Berhasil menjadwalkan {$sent} pesan rekap harian ke antrean.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Gagal mengirim rekap harian: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
