<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    private function attendanceStatusValue(mixed $status): string
    {
        if ($status instanceof \BackedEnum) {
            return (string) $status->value;
        }

        return (string) $status;
    }

    /**
     * Rekap Harian Kehadiran Kelas
     */
    public function getDailyReport(string $date, int $classId, int $schoolId, ?int $studentId = null): array
    {
        $class = StudentClass::with('school')->where('id', $classId)->where('school_id', $schoolId)->first();
        if (!$class) {
            throw new \Exception("Kelas tidak ditemukan.");
        }

        // Ambil semua siswa di kelas ini beserta kehadiran mereka untuk tanggal tersebut
        $students = Student::where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->when($studentId, fn($query) => $query->where('id', $studentId))
            ->orderBy('name')
            ->get();

        $attendances = StudentAttendance::where('class_id', $classId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        $reportData = [];
        $summary = [
            'present' => 0,
            'late' => 0,
            'permission' => 0,
            'sick' => 0,
            'absent' => 0,
            'not_recorded' => 0,
        ];

        foreach ($students as $student) {

            $att = $attendances->get($student->id);

            $status = $att
                ? $this->attendanceStatusValue($att->status)
                : 'not_recorded';

            if (array_key_exists($status, $summary)) {
                $summary[$status]++;
            }

            $reportData[] = [
                'student_id'     => $student->id,
                'name'           => $student->name,
                'nis'            => $student->nis,
                'status'         => $status,
                'check_in_time'  => $att?->check_in_time,
                'check_out_time' => $att?->check_out_time,
                'note'           => $att?->note,
            ];
        }

        return [
        'class' => [
        'id' => $class->id,
        'name' => $class->name,
        'grade' => $class->grade,
        'major' => $class->major,
        ],

        'school_name'    => $class->school?->name ?? '-',
        'school_address' => $class->school?->address ?? '-',
        'school_phone'   => $class->school?->phone ?? '-',
        'school_email'   => $class->school?->email ?? '-',

        'date' => $date,
        'summary' => $summary,
        'students' => $reportData,
    ];
    }

    /**
     * Rekap Bulanan Kehadiran Kelas
     */
    public function getMonthlyReport(int $month, int $year, int $classId, int $schoolId, ?int $studentId = null): array
    {
        $class = StudentClass::with('school')->where('id', $classId)->where('school_id', $schoolId)->first();
        if (!$class) {
            throw new \Exception("Kelas tidak ditemukan.");
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $students = Student::where('class_id', $classId)
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->when($studentId, fn($query) => $query->where('id', $studentId))
            ->orderBy('name')
            ->get();

        // Ambil semua data kehadiran untuk kelas ini pada bulan tersebut
        $attendances = StudentAttendance::where('class_id', $classId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('student_id');

        $reportData = [];
        $totalDaysInMonth = Carbon::parse($startDate)->daysInMonth;

        foreach ($students as $student) {
            $studentAtts = $attendances->get($student->id) ?? collect();

            $counts = [
                'present' => 0,
                'late' => 0,
                'permission' => 0,
                'sick' => 0,
                'absent' => 0,
            ];

            foreach ($studentAtts as $att) {
                $status = $this->attendanceStatusValue($att->status);
                if (array_key_exists($status, $counts)) {
                    $counts[$status]++;
                }
            }

            $totalPresence = $counts['present'] + $counts['late'];
            $totalRecorded = $studentAtts->count();

            // Hitung persentase kehadiran: (Hadir + Terlambat) / Total Hari Rekam Presensi
            $percentage = $totalRecorded > 0 ? round(($totalPresence / $totalRecorded) * 100, 2) : 0;

            $reportData[] = [
                'student_id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'summary' => $counts,
                'total_recorded_days' => $totalRecorded,
                'attendance_percentage' => $percentage,
            ];
        }

        return [
        'class' => [
        'id' => $class->id,
        'name' => $class->name,
        'grade' => $class->grade,
        'major' => $class->major,
        ],

        'school_name'    => $class->school?->name ?? '-',
        'school_address' => $class->school?->address ?? '-',
        'school_phone'   => $class->school?->phone ?? '-',
        'school_email'   => $class->school?->email ?? '-',

        'month' => $month,
        'year' => $year,
        'total_students' => $students->count(),
        'students' => $reportData,
    ];
    }

    /**
     * Kirim rekap kehadiran harian ke orang tua siswa melalui WhatsApp
     */
    public function sendDailyRecapToParents(string $date, ?int $schoolId = null): int
    {
        $query = Student::with(['school', 'class', 'parent'])
            ->where('status', 'active');
            
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        
        $students = $query->get();

        $sentCount = 0;

        foreach ($students as $student) {
            // Ambil nomor telp ortu dari data siswa terlebih dahulu, lalu fallback ke relasi parent
            $phone = $student->parent_phone
                ?? optional($student->parent)->phone
                ?? null;
            if (!$phone) {
                continue;
            }

            // Ambil kehadiran untuk hari ini
            $attendance = StudentAttendance::where('student_id', $student->id)
                ->where('date', $date)
                ->first();

            $statusLabel = 'Alpha';
            $checkIn = '-';
            $note = '-';

            if ($attendance) {
                $status = $this->attendanceStatusValue($attendance->status);
                $statusLabel = match ($status) {
                    'present'    => 'Hadir',
                    'late'       => 'Terlambat',
                    'permission' => 'Izin',
                    'sick'       => 'Sakit',
                    'absent'     => 'Alpha',
                    default      => $status,
                };
                $checkIn = $attendance->check_in_time ? Carbon::parse($attendance->check_in_time)->format('H:i') : '-';
                $note = $attendance->note ?? '-';
            }

            $formattedDate = Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y');

            $message = "SIMPAD Info:\n"
                . "Laporan Kehadiran Harian\n\n"
                . "Nama Siswa: {$student->name}\n"
                . "Kelas: {$student->class->name}\n"
                . "Tanggal: {$formattedDate}\n"
                . "Status: *{$statusLabel}*\n"
                . "Jam Masuk: {$checkIn}\n"
                . "Catatan: {$note}\n\n"
                . "Terima kasih.\n"
                . "SIMPAD";

            dispatch(new \App\Jobs\SendWhatsAppNotification($phone, $message));
            $sentCount++;
        }

        return $sentCount;
    }

    /**
     * Kirim rekap kehadiran bulanan ke orang tua siswa melalui WhatsApp
     */
    public function sendMonthlyRecapToParents(int $month, int $year, ?int $schoolId = null): int
    {
        $query = Student::with(['school', 'class', 'parent'])
            ->where('status', 'active');
            
        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }
        
        $students = $query->get();

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $sentCount = 0;

        foreach ($students as $student) {
            $phone = $student->parent_phone
                ?? optional($student->parent)->phone
                ?? null;
            if (!$phone) {
                continue;
            }

            $attendances = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $counts = [
                'present' => 0,
                'late' => 0,
                'permission' => 0,
                'sick' => 0,
                'absent' => 0,
            ];

            foreach ($attendances as $att) {
                $status = $this->attendanceStatusValue($att->status);
                if (array_key_exists($status, $counts)) {
                    $counts[$status]++;
                }
            }

            $totalPresence = $counts['present'] + $counts['late'];
            $totalRecorded = $attendances->count();
            $percentage = $totalRecorded > 0 ? round(($totalPresence / $totalRecorded) * 100, 2) : 0;

            $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->isoFormat('MMMM Y');

            $message = "SIMPAD Info:\n"
                . "Laporan Bulanan Kehadiran - {$monthName}\n\n"
                . "Nama Siswa: {$student->name}\n"
                . "Kelas: {$student->class->name}\n"
                . "Periode: {$monthName}\n\n"
                . "Ringkasan:\n"
                . "- Hadir: {$counts['present']} hari\n"
                . "- Terlambat: {$counts['late']} hari\n"
                . "- Sakit: {$counts['sick']} hari\n"
                . "- Izin: {$counts['permission']} hari\n"
                . "- Alpha: {$counts['absent']} hari\n"
                . "- Persentase Kehadiran: *{$percentage}%*\n\n"
                . "Terima kasih.\n"
                . "SIMPAD";

            dispatch(new \App\Jobs\SendWhatsAppNotification($phone, $message));
            $sentCount++;
        }

        return $sentCount;
    }
}
