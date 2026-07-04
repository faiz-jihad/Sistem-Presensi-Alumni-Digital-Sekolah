<?php

namespace App\Console\Commands;

use App\Services\ReportService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendMonthlyAttendanceRecap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-monthly-recap {--month= : Month number (1-12)} {--year= : Year (e.g. 2026)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send monthly attendance summary to parents via WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle(ReportService $reportService)
    {
        $month = $this->option('month') ?: Carbon::now()->month;
        $year = $this->option('year') ?: Carbon::now()->year;

        $this->info("Memulai pengiriman rekap bulanan untuk periode {$month}-{$year}...");

        try {
            $sent = $reportService->sendMonthlyRecapToParents((int) $month, (int) $year);
            $this->info("Berhasil menjadwalkan {$sent} pesan rekap bulanan ke antrean.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Gagal mengirim rekap bulanan: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
