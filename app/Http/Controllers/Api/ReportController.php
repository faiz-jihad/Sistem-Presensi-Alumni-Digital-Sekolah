<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Report\DailyReportRequest;
use App\Http\Requests\Report\MonthlyReportRequest;
use App\Http\Requests\Report\SendDailyRecapRequest;
use App\Http\Requests\Report\SendMonthlyRecapRequest;
use App\Models\SchoolClass;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class ReportController extends BaseController
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Rekap Harian Kehadiran Kelas
     */
    public function daily(DailyReportRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin', 'teacher'], true)) {
            return $this->forbidden("Hanya admin atau guru yang dapat melihat rekap.");
        }

        $class = SchoolClass::findOrFail($request->class_id);
        Gate::authorize('view', $class);

        $date = $request->date ?? Carbon::today()->toDateString();
        
        $schoolId = $user->school_id ?? $class->school_id;

        if (!$schoolId) {
            return $this->error("Sekolah tidak valid.", 400);
        }

        try {
            $report = $this->reportService->getDailyReport($date, $request->class_id, $schoolId);
            return $this->success($report, "Rekap harian berhasil dimuat.");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Rekap Bulanan Kehadiran Kelas
     */
    public function monthly(MonthlyReportRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin', 'teacher'], true)) {
            return $this->forbidden("Hanya admin atau guru yang dapat melihat rekap.");
        }

        $class = SchoolClass::findOrFail($request->class_id);
        Gate::authorize('view', $class);

        $schoolId = $user->school_id ?? $class->school_id;

        if (!$schoolId) {
            return $this->error("Sekolah tidak valid.", 400);
        }

        try {
            $report = $this->reportService->getMonthlyReport(
                (int) $request->month,
                (int) $request->year,
                (int) $request->class_id,
                (int) $schoolId
            );
            return $this->success($report, "Rekap bulanan berhasil dimuat.");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Kirim Rekap Harian Ke Orang Tua Via WhatsApp
     */
    public function sendDailyRecap(SendDailyRecapRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin', 'teacher'], true)) {
            return $this->forbidden("Hanya admin atau guru yang dapat mengirim rekap.");
        }

        $date = $request->date ?? Carbon::today()->toDateString();

        try {
            $sent = $this->reportService->sendDailyRecapToParents($date);
            return $this->success([
                'messages_sent' => $sent
            ], "Berhasil memproses pengiriman {$sent} pesan rekap harian.");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Kirim Rekap Bulanan Ke Orang Tua Via WhatsApp
     */
    public function sendMonthlyRecap(SendMonthlyRecapRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin'], true)) {
            return $this->forbidden("Hanya admin yang dapat mengirim rekap bulanan.");
        }

        try {
            $sent = $this->reportService->sendMonthlyRecapToParents((int) $request->month, (int) $request->year);
            return $this->success([
                'messages_sent' => $sent
            ], "Berhasil memproses pengiriman {$sent} pesan rekap bulanan.");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
