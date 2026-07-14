<?php

namespace App\Services;

use App\Models\Export;
use App\Models\Alumni;
use App\Models\School;
use App\Models\StudentClass;
use App\Exports\DailyAttendanceExport;
use App\Exports\MonthlyAttendanceExport;
use App\Exports\AlumniExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExportService
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function generate(Export $export): void
    {
        $export->update(['status' => 'processing']);

        try {
            $filters = $export->filters ?? [];
            $schoolId = $export->school_id;
            $school = School::find($schoolId);
            $schoolName = $school ? $school->name : 'Sekolah';

            $fileDir = 'exports';
            // Ensure folder exists inside public disk
            if (!Storage::disk('public')->exists($fileDir)) {
                Storage::disk('public')->makeDirectory($fileDir);
            }

            $timestamp = time();
            $fileName = "laporan_{$export->type}_{$export->id}_{$timestamp}.{$export->file_type}";
            $filePath = "{$fileDir}/{$fileName}";

            if ($export->type === 'attendance_report') {
                $attendanceType = $filters['type'] ?? 'daily';
                $classId = $filters['class_id'] ?? null;
                $class = StudentClass::find($classId);
                $className = $class ? $class->name : 'Kelas';

                if (!$classId) {
                    throw new \Exception("Kelas harus ditentukan untuk laporan presensi.");
                }

                $studentId = $filters['student_id'] ?? null;

                if ($attendanceType === 'daily') {
                    $date = $filters['date'] ?? now()->toDateString();
                    $reportData = $this->reportService->getDailyReport($date, $classId, $schoolId, $studentId);

                    if ($export->file_type === 'xlsx') {
                        Excel::store(
                            new DailyAttendanceExport(
                                $reportData['students'],
                                "Harian {$className}",
                                $reportData['school_name'] ?? $schoolName,
                                $className,
                                Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y')
                            ),
                            $filePath,
                            'public'
                        );
                    } else {
                        $reportData = array_merge($reportData, [
                        'date' => $date,

                        'school_name' => $school?->name,
                        'school_address' => $school?->address,
                        'school_phone' => $school?->phone,
                        'school_email' => $school?->email,
                        'school_logo' => $school?->logo,
                    ]);

                    $pdf = Pdf::loadView('pdf.daily-attendance', $reportData);

                    Storage::disk('public')->put(
                        $filePath,
                        $pdf->output()
                    );
                    }
                } else {
                    $month = (int) ($filters['month'] ?? now()->month);
                    $year = (int) ($filters['year'] ?? now()->year);
                    $reportData = $this->reportService->getMonthlyReport($month, $year, $classId, $schoolId, $studentId);

                    if ($export->file_type === 'xlsx') {
                        Excel::store(
                            new MonthlyAttendanceExport(
                                $reportData['students'],
                                "Bulanan {$className}",
                                $reportData['school_name'] ?? $schoolName,
                                $className,
                                Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM Y')
                            ),
                            $filePath,
                            'public'
                        );
                    } else {
                        $reportData = array_merge($reportData, [
                            'school_name'    => $school?->name,
                            'school_address' => $school?->address,
                            'school_phone'   => $school?->phone,
                            'school_email'   => $school?->email,
                            'school_logo'    => $school?->logo,
                        ]);

                        $pdf = Pdf::loadView('pdf.monthly-attendance', $reportData);
                        Storage::disk('public')->put($filePath, $pdf->output());
                    }
                }
            } elseif ($export->type === 'alumni_report') {
                $graduationYear = $filters['graduation_year'] ?? null;
                $verificationStatus = $filters['verification_status'] ?? null;

                $query = Alumni::where('school_id', $schoolId);

                if (!empty($graduationYear)) {
                    $query->where('graduation_year', $graduationYear);
                }

                if (!empty($verificationStatus)) {
                    $query->where('verification_status', $verificationStatus);
                }

                $alumnis = $query->orderBy('name')->get()->toArray();

                if ($export->file_type === 'xlsx') {
                    Excel::store(
                        new AlumniExport(
                            $alumnis,
                            "Data Alumni",
                            $school,
                            $graduationYear,
                            $verificationStatus
                        ),
                        $filePath,
                        'public'
                    );
                } else {
                    $pdf = Pdf::loadView('pdf.alumni', [
                        'alumni_list'         => $alumnis,

                        'school_name'         => $school?->name,
                        'school_address'      => $school?->address,
                        'school_phone'        => $school?->phone,
                        'school_email'        => $school?->email,
                        'school_logo'         => $school?->logo,

                        'graduation_year'     => $graduationYear,
                        'verification_status' => $verificationStatus,
                    ]);

                    Storage::disk('public')->put(
                        $filePath,
                        $pdf->output()
                    );
                }
            } elseif ($export->type === 'student_report') {
                $classId = $filters['class_id'] ?? null;
                $class = StudentClass::find($classId);
                $className = $class ? $class->name : null;

                $status = $filters['status'] ?? null;
                $statusLabel = match ($status) {
                    'active' => 'Aktif',
                    'inactive' => 'Tidak Aktif',
                    'graduated' => 'Lulus',
                    default => 'Semua Status'
                };

                $query = \App\Models\Student::with(['class', 'parent'])->where('school_id', $schoolId);

                if (!empty($classId)) {
                    $query->where('class_id', $classId);
                }

                if (!empty($status)) {
                    $query->where('status', $status);
                }

                $students = $query->orderBy('name')->get()->map(function ($st) use ($schoolId) {
                    $studentUser = \App\Models\User::where('role', 'student')
                        ->where('school_id', $schoolId)
                        ->where(function ($q) use ($st) {
                            $q->where('email', $st->nis)
                              ->orWhere('name', $st->name);
                        })->first();

                    return [
                        'nis' => $st->nis,
                        'nisn' => $st->nisn,
                        'name' => $st->name,
                        'gender' => $st->gender,
                        'birth_date' => $st->birth_date,
                        'class_name' => $st->class?->name ?? '-',
                        'parent_phone' => $st->parent_phone ?? '-',
                        'email' => $studentUser?->email ?? '-',
                        'status' => $st->status,
                    ];
                })->toArray();

                if ($export->file_type === 'xlsx') {
                    Excel::store(
                        new \App\Exports\StudentExport(
                            $students,
                            "Data Siswa",
                            $school,
                            $className,
                            $statusLabel
                        ),
                        $filePath,
                        'public'
                    );
                } else {
                    $pdf = Pdf::loadView('pdf.student', [
                        'student_list' => $students,
                        'school_name' => $school?->name,
                        'school_address' => $school?->address,
                        'school_phone' => $school?->phone,
                        'school_email' => $school?->email,
                        'school_logo' => $school?->logo,
                        'class_name' => $className,
                        'status_label' => $statusLabel,
                    ]);
                    Storage::disk('public')->put($filePath, $pdf->output());
                }
            } elseif ($export->type === 'teacher_report') {
                $status = $filters['status'] ?? null;
                $statusLabel = match ($status) {
                    'active' => 'Aktif',
                    'inactive' => 'Tidak Aktif',
                    'retired' => 'Pensiun',
                    default => 'Semua Status'
                };

                $query = \App\Models\Teacher::with(['user'])->where('school_id', $schoolId);

                if (!empty($status)) {
                    $query->where('status', $status);
                }

                $teachers = $query->orderBy('name')->get()->map(function ($tc) {
                    return [
                        'nip' => $tc->nip,
                        'name' => $tc->name,
                        'gender' => $tc->gender,
                        'phone' => $tc->phone,
                        'field_of_study' => $tc->field_of_study,
                        'employment_status' => $tc->employment_status,
                        'education_level' => $tc->education_level,
                        'university' => $tc->university,
                        'email' => $tc->user?->email ?? '-',
                        'status' => $tc->status,
                    ];
                })->toArray();

                if ($export->file_type === 'xlsx') {
                    Excel::store(
                        new \App\Exports\TeacherExport(
                            $teachers,
                            "Data Guru",
                            $school,
                            $statusLabel
                        ),
                        $filePath,
                        'public'
                    );
                } else {
                    $pdf = Pdf::loadView('pdf.teacher', [
                        'teacher_list' => $teachers,
                        'school_name' => $school?->name,
                        'school_address' => $school?->address,
                        'school_phone' => $school?->phone,
                        'school_email' => $school?->email,
                        'school_logo' => $school?->logo,
                        'status_label' => $statusLabel,
                    ]);
                    Storage::disk('public')->put($filePath, $pdf->output());
                }
            } else {
                throw new \Exception("Tipe laporan tidak didukung.");
            }

            // Get file size from public storage
            $fileSize = Storage::disk('public')->size($filePath);

            $export->update([
                'status' => 'completed',
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'completed_at' => now(),
            ]);

        } catch (\Throwable $e) {
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage() . "\n" . $e->getTraceAsString(),
            ]);
        }
    }
}