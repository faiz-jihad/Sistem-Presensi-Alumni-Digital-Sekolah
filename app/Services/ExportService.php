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

                if ($attendanceType === 'daily') {
                    $date = $filters['date'] ?? now()->toDateString();
                    $reportData = $this->reportService->getDailyReport($date, $classId, $schoolId);

                    if ($export->file_type === 'xlsx') {
                        Excel::store(
                            new DailyAttendanceExport($reportData['students'], "Harian {$className}"),
                            $filePath,
                            'public'
                        );
                    } else {
                        $reportData['school_name'] = $schoolName;
                        $pdf = Pdf::loadView('pdf.daily-attendance', $reportData);
                        Storage::disk('public')->put($filePath, $pdf->output());
                    }
                } else {
                    $month = (int) ($filters['month'] ?? now()->month);
                    $year = (int) ($filters['year'] ?? now()->year);
                    $reportData = $this->reportService->getMonthlyReport($month, $year, $classId, $schoolId);

                    if ($export->file_type === 'xlsx') {
                        Excel::store(
                            new MonthlyAttendanceExport($reportData['students'], "Bulanan {$className}"),
                            $filePath,
                            'public'
                        );
                    } else {
                        $reportData['school_name'] = $schoolName;
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
                        new AlumniExport($alumnis, "Data Alumni"),
                        $filePath,
                        'public'
                    );
                } else {
                    $pdfData = [
                        'school_name' => $schoolName,
                        'graduation_year' => $graduationYear,
                        'verification_status' => $verificationStatus,
                        'alumni_list' => $alumnis,
                    ];
                    $pdf = Pdf::loadView('pdf.alumni', $pdfData);
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

    public function generateAlumniReport(array $filters, string $fileType, int $schoolId)
    {
        // Query alumni dengan filter
        $query = Alumni::query()
            ->with(['school', 'user', 'verifiedBy'])
            ->where('school_id', $schoolId);

        // Log filter yang diterima
        \Log::info('Filters received:', $filters);

        // Terapkan filter - PASTIKAN menggunakan kondisi yang benar
        // Filter Tahun Lulus - hanya jika ada nilai dan tidak kosong
        if (!empty($filters['graduation_year']) && $filters['graduation_year'] !== '') {
            $query->where('graduation_year', $filters['graduation_year']);
            \Log::info('Filter tahun: ' . $filters['graduation_year']);
        }

        // Filter Status Verifikasi
        if (!empty($filters['verification_status']) && $filters['verification_status'] !== '') {
            $query->where('verification_status', $filters['verification_status']);
            \Log::info('Filter status: ' . $filters['verification_status']);
        }

        // Filter Gender (tambahan)
        if (!empty($filters['gender']) && $filters['gender'] !== '') {
            $query->where('gender', $filters['gender']);
            \Log::info('Filter gender: ' . $filters['gender']);
        }

        // Ambil data alumni
        $alumnis = $query->orderBy('name')->get();

        // Log total data
        \Log::info('Total alumni ditemukan: ' . $alumnis->count());
        \Log::info('SQL yang dijalankan: ' . $query->toSql());
        \Log::info('Bindings: ', $query->getBindings());

        // Jika tidak ada data, throw exception atau return empty
        if ($alumnis->isEmpty()) {
            \Log::warning('Tidak ada alumni ditemukan dengan filter yang diberikan');
        }

        // Generate file berdasarkan tipe
        if ($fileType === 'pdf') {
            return $this->generatePDF($alumnis);
        } else {
            return $this->generateExcel($alumnis);
        }
    }

    private function generateExcel($alumnis)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'A1' => 'No',
            'B1' => 'NISN',
            'C1' => 'Nama Lengkap',
            'D1' => 'Jenis Kelamin',
            'E1' => 'Sekolah',
            'F1' => 'Kelas Lulus',
            'G1' => 'Jurusan',
            'H1' => 'Tahun Lulus',
            'I1' => 'Status Verifikasi',
            'J1' => 'Terverifikasi Pada',
            'K1' => 'Email',
            'L1' => 'No HP',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style header
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->getStyle('A1:L1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Data
        $row = 2;
        $no = 1;
        foreach ($alumnis as $alumni) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $alumni->nisn);
            $sheet->setCellValue('C' . $row, $alumni->name);
            $sheet->setCellValue('D' . $row, $alumni->gender === 'male' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('E' . $row, $alumni->school->name ?? '-');
            $sheet->setCellValue('F' . $row, $alumni->class_name);
            $sheet->setCellValue('G' . $row, $alumni->major ?? '-');
            $sheet->setCellValue('H' . $row, $alumni->graduation_year);
            
            // Status verifikasi
            $status = match($alumni->verification_status) {
                'pending' => 'Menunggu Verifikasi',
                'verified' => 'Terverifikasi',
                'rejected' => 'Ditolak',
                default => $alumni->verification_status,
            };
            $sheet->setCellValue('I' . $row, $status);
            
            $sheet->setCellValue('J' . $row, $alumni->verified_at ? $alumni->verified_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValue('K' . $row, $alumni->email ?? '-');
            $sheet->setCellValue('L' . $row, $alumni->phone ?? '-');

            $row++;
            $no++;
        }

        // Auto size columns
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Simpan file
        $fileName = 'laporan_alumni_' . date('Ymd_His') . '.xlsx';
        $filePath = 'exports/' . $fileName;
        
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/public/' . $filePath));

        \Log::info('File Excel saved at: ' . $filePath);

        return $filePath;
    }

    private function generatePDF($alumnis)
    {
        $data = [
            'alumnis' => $alumnis,
            'title' => 'Laporan Data Alumni',
            'date' => now()->format('d/m/Y H:i'),
            'total' => $alumnis->count()
        ];

        $pdf = Pdf::loadView('exports.alumni-report', $data);
        
        $fileName = 'laporan_alumni_' . date('Ymd_His') . '.pdf';
        $filePath = 'exports/' . $fileName;
        
        $pdf->save(storage_path('app/public/' . $filePath));

        \Log::info('File PDF saved at: ' . $filePath);

        return $filePath;
    }
}