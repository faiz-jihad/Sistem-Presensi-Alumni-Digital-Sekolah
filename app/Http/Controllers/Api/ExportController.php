<?php

namespace App\Http\Controllers\Api;

<<<<<<< Updated upstream
use App\Models\Alumni;
use App\Models\School;
use App\Models\StudentClass;
use App\Exports\AlumniExport;
use App\Exports\DailyAttendanceExport;
use App\Exports\MonthlyAttendanceExport;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
=======
use App\Services\ReportService;
use App\Exports\DailyAttendanceExport;
use App\Exports\MonthlyAttendanceExport;
use App\Exports\AlumniExport;
use App\Models\Alumni;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
>>>>>>> Stashed changes

class ExportController extends BaseController
{
    public function __construct(
<<<<<<< Updated upstream
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
=======
        private readonly ReportService $reportService
    ) {}

    /**
     * Ekspor laporan presensi harian/bulanan ke Excel
     */
    public function exportAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'type' => 'nullable|in:daily,monthly',
            'date' => 'nullable|date',
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2020|max:2050',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $classId = $request->class_id;
        $class = StudentClass::findOrFail($classId);
        $schoolId = $class->school_id;
        $type = $request->type ?? 'daily';

        try {
            if ($type === 'daily') {
                $date = $request->date ?? Carbon::today()->toDateString();
                $report = $this->reportService->getDailyReport($date, $classId, $schoolId);
                
                $filename = "rekap_harian_{$class->name}_{$date}.xlsx";
                return Excel::download(
                    new DailyAttendanceExport($report['students'], "Harian {$class->name}"),
                    $filename
                );
            } else {
                $month = (int) ($request->month ?? now()->month);
                $year = (int) ($request->year ?? now()->year);
                $report = $this->reportService->getMonthlyReport($month, $year, $classId, $schoolId);

                $monthName = Carbon::createFromDate($year, $month, 1)->format('M');
                $filename = "rekap_bulanan_{$class->name}_{$monthName}_{$year}.xlsx";
                return Excel::download(
                    new MonthlyAttendanceExport($report['students'], "Bulanan {$class->name}"),
                    $filename
                );
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
>>>>>>> Stashed changes
        }
    }

    /**
<<<<<<< Updated upstream
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
=======
     * Ekspor data alumni ke Excel
     */
    public function exportAlumni(Request $request)
    {
        try {
            $query = Alumni::with(['profile']);
            
            if ($request->has('school_id')) {
                $query->where('school_id', $request->school_id);
            }
            if ($request->has('graduation_year')) {
                $query->where('graduation_year', $request->graduation_year);
            }

            $alumni = $query->get()->map(function ($al) {
                $status = $al->profile?->current_status;
                $statusLabel = match($status) {
                    'working' => 'Bekerja',
                    'studying' => 'Kuliah',
                    'entrepreneur' => 'Wirausaha',
                    default => 'Belum Bekerja / Lainnya'
                };

                $detail = '-';
                if ($status === 'working') {
                    $detail = ($al->profile->company_name ?? '-') . ' (' . ($al->profile->job_position ?? '-') . ')';
                } elseif ($status === 'studying') {
                    $detail = ($al->profile->university_name ?? '-') . ' (' . ($al->profile->study_program ?? '-') . ')';
                } elseif ($status === 'entrepreneur') {
                    $detail = $al->profile->business_name ?? '-';
                }

                return [
                    'name' => $al->name,
                    'nisn' => $al->nisn,
                    'graduation_year' => $al->graduation_year,
                    'class_name' => $al->class_name,
                    'major' => $al->major,
                    'status' => $statusLabel,
                    'detail' => $detail,
                ];
            })->toArray();

            return Excel::download(
                new AlumniExport($alumni, "Data Alumni"),
                'export_alumni.xlsx'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
>>>>>>> Stashed changes
    }
}
