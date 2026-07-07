<?php

namespace App\Http\Controllers\Api;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReportController extends BaseController
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    private function denyIfCannotAccessClass(Request $request): ?JsonResponse
    {
        $user = $request->user();

        if ($user->role !== 'teacher') {
            return null;
        }

        $teacherId = \App\Models\Teacher::where('user_id', $user->id)->value('id');
        if (!$teacherId) {
            return $this->error("Data guru tidak ditemukan untuk akun ini.", 404);
        }

        $hasClassAccess = \App\Models\SchoolClass::where('id', $request->class_id)
            ->where('homeroom_teacher_id', $teacherId)
            ->exists();

        if (!$hasClassAccess) {
            return $this->forbidden("Anda tidak memiliki hak akses ke kelas ini.");
        }

        return null;
    }

    /**
     * Rekap Harian Kehadiran Kelas
     */
    public function daily(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin', 'teacher'])) {
            return $this->forbidden("Hanya admin atau guru yang dapat melihat rekap.");
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        if ($forbidden = $this->denyIfCannotAccessClass($request)) {
            return $forbidden;
        }

        $date = $request->date ?? Carbon::today()->toDateString();
        
        // Dapatkan school_id dari relasi user (atau default ke school_id kelas jika admin/super_admin)
        $schoolId = $user->school_id;
        if (!$schoolId) {
            $schoolId = \DB::table('classes')->where('id', $request->class_id)->value('school_id');
        }

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
    public function monthly(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin', 'teacher'])) {
            return $this->forbidden("Hanya admin atau guru yang dapat melihat rekap.");
        }

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2050',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        if ($forbidden = $this->denyIfCannotAccessClass($request)) {
            return $forbidden;
        }

        $schoolId = $user->school_id;
        if (!$schoolId) {
            $schoolId = \DB::table('classes')->where('id', $request->class_id)->value('school_id');
        }

        if (!$schoolId) {
            return $this->error("Sekolah tidak valid.", 400);
        }

        try {
            $report = $this->reportService->getMonthlyReport(
                $request->month,
                $request->year,
                $request->class_id,
                $schoolId
            );
            return $this->success($report, "Rekap bulanan berhasil dimuat.");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Kirim Rekap Harian Ke Orang Tua Via WhatsApp
     */
    public function sendDailyRecap(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin', 'teacher'])) {
            return $this->forbidden("Hanya admin atau guru yang dapat mengirim rekap.");
        }

        $date = $request->input('date') ?: Carbon::today()->toDateString();

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
    public function sendMonthlyRecap(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!in_array($user->role, ['admin', 'super_admin'])) {
            return $this->forbidden("Hanya admin yang dapat mengirim rekap bulanan.");
        }

        $validator = Validator::make($request->all(), [
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2050',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
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
