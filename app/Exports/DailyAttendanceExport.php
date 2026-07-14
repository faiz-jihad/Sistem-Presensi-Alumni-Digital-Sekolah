<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyAttendanceExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    public function __construct(
        private array $data,
        private string $title,
        private string $schoolName,
        private string $className,
        private string $date
    ) {}

    public function startCell(): string
    {
        return 'A6';
    }

    public function array(): array
    {
        $rows = [];
        $no = 1;

        foreach ($this->data as $student) {
            $status = match ($student['status']) {
                'present' => 'Hadir',
                'late' => 'Terlambat',
                'permission' => 'Izin',
                'sick' => 'Sakit',
                'absent' => 'Alpha',
                default => 'Belum Diisi'
            };

            $rows[] = [
                $no++,
                $student['name'],
                $student['nis'],
                $status,
                $student['check_in_time'] ?: '-',
                $student['note'] ?: '-',
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'NIS',
            'Status Kehadiran',
            'Jam Masuk',
            'Catatan',
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        // Tulis Kop Asal Sekolah
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PRESENSI HARIAN');
        $sheet->setCellValue('A2', 'Asal Sekolah: ' . $this->schoolName);
        $sheet->setCellValue('A3', 'Kelas: ' . $this->className);
        $sheet->setCellValue('A4', 'Tanggal: ' . $this->date);

        // Gabungkan sel untuk Kop agar berada di tengah
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');
        $sheet->mergeCells('A4:F4');

        // Styling Kop & Penyelarasan Rata Tengah
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:F4')->getFont()->setBold(true);
        $sheet->getStyle('A1:F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB']
                ]
            ],
        ];
    }
}
