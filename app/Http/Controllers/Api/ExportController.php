<?php

namespace App\Http\Controllers\Api;

use App\Models\Alumni;
use App\Models\School;
use App\Models\StudentClass;
use App\Exports\AlumniExport;
use App\Exports\DailyAttendanceExport;
use App\Exports\MonthlyAttendanceExport;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends BaseController
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Export presensi ke Excel
     */
    public function attendance(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        if ($user->role === 'super_admin') {
            $schoolId = $request->input('school_id', School::first()?->id);
        }

        if (!$schoolId) {
            return $this->error('Sekolah tidak ditemukan', 400);
        }

        $classId = $request->input('class_id');
        if (!$classId) {
            return $this->error('class_id harus disertakan', 400);
        }

        $class = StudentClass::where('id', $classId)->where('school_id', $schoolId)->first();
        if (!$class) {
            return $this->error('Kelas tidak ditemukan untuk sekolah ini', 404);
        }

        $type = $request->input('type', 'daily');

        if ($type === 'daily') {
            $date = $request->input('date', now()->toDateString());
            try {
                $reportData = $this->reportService->getDailyReport($date, $classId, $schoolId);
                return Excel::download(
                    new DailyAttendanceExport($reportData['students'], "Harian {$class->name}"),
                    "rekap_harian_{$class->name}_{$date}.xlsx"
                );
            } catch (\Exception $e) {
                return $this->error($e->getMessage(), 400);
            }
        } else {
            $month = (int) $request->input('month', now()->month);
            $year = (int) $request->input('year', now()->year);
            try {
                $reportData = $this->reportService->getMonthlyReport($month, $year, $classId, $schoolId);
                return Excel::download(
                    new MonthlyAttendanceExport($reportData['students'], "Bulanan {$class->name}"),
                    "rekap_bulanan_{$class->name}_{$month}_{$year}.xlsx"
                );
            } catch (\Exception $e) {
                return $this->error($e->getMessage(), 400);
            }
        }
    }

    /**
     * Export alumni ke Excel
     */
    public function alumni(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->school_id;

        if ($user->role === 'super_admin') {
            $schoolId = $request->input('school_id', School::first()?->id);
        }

        if (!$schoolId) {
            return $this->error('Sekolah tidak ditemukan', 400);
        }

        $graduationYear = $request->input('graduation_year');
        $verificationStatus = $request->input('verification_status');

        $query = Alumni::where('school_id', $schoolId);

        if (!empty($graduationYear)) {
            $query->where('graduation_year', $graduationYear);
        }

        if (!empty($verificationStatus)) {
            $query->where('verification_status', $verificationStatus);
        }

        $alumnis = $query->orderBy('name')->get()->toArray();

        return Excel::download(
            new AlumniExport($alumnis, "Data Alumni"),
            "data_alumni_export.xlsx"
        );
    }
}
