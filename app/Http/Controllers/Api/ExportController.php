<?php

namespace App\Http\Controllers\Api;

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

class ExportController extends BaseController
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    public function attendance(Request $request)
    {
        return $this->exportAttendance($request);
    }

    public function alumni(Request $request)
    {
        return $this->exportAlumni($request);
    }

    /**
     * Ekspor laporan presensi harian/bulanan ke Excel
     */
    public function exportAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'student_id' => 'nullable|exists:students,id',
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
            $studentId = $request->student_id;

            if ($type === 'daily') {
                $date = $request->date ?? Carbon::today()->toDateString();
                $report = $this->reportService->getDailyReport($date, $classId, $schoolId, $studentId);
                
                $filename = "rekap_harian_{$class->name}_{$date}.xlsx";
                return Excel::download(
                    new DailyAttendanceExport(
                        $report['students'],
                        "Harian {$class->name}",
                        $report['school_name'] ?? $class->school?->name ?? 'Sekolah',
                        $class->name,
                        Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y')
                    ),
                    $filename
                );
            } else {
                $month = (int) ($request->month ?? now()->month);
                $year = (int) ($request->year ?? now()->year);
                $report = $this->reportService->getMonthlyReport($month, $year, $classId, $schoolId, $studentId);

                $monthName = Carbon::createFromDate($year, $month, 1)->format('M');
                $filename = "rekap_bulanan_{$class->name}_{$monthName}_{$year}.xlsx";
                return Excel::download(
                    new MonthlyAttendanceExport(
                        $report['students'],
                        "Bulanan {$class->name}",
                        $report['school_name'] ?? $class->school?->name ?? 'Sekolah',
                        $class->name,
                        Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM Y')
                    ),
                    $filename
                );
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
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

            $schoolId = $request->input('school_id') ?? auth()->user()?->school_id;
            $school = null;
            if ($schoolId) {
                $school = \App\Models\School::find($schoolId);
            }
            $graduationYear = $request->input('graduation_year');

            return Excel::download(
                new AlumniExport(
                    $alumni,
                    "Data Alumni",
                    $school,
                    $graduationYear,
                    null
                ),
                'export_alumni.xlsx'
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }
}
