<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyAttendanceExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    public function __construct(
        private array $data,
        private string $title,
        private string $schoolName,
        private string $className,
        private string $period
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
            $rows[] = [
                $no++,
                $student['name'],
                $student['nis'],
                $student['summary']['present'] ?? 0,
                $student['summary']['late'] ?? 0,
                $student['summary']['sick'] ?? 0,
                $student['summary']['permission'] ?? 0,
                $student['summary']['absent'] ?? 0,
                $student['attendance_percentage'] . '%',
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
            'Hadir (Hari)',
            'Terlambat (Hari)',
            'Sakit (Hari)',
            'Izin (Hari)',
            'Alpha (Hari)',
            'Persentase Kehadiran',
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        // Tulis Kop Asal Sekolah
        $sheet->setCellValue('A1', 'LAPORAN REKAPITULASI PRESENSI BULANAN');
        $sheet->setCellValue('A2', 'Asal Sekolah: ' . $this->schoolName);
        $sheet->setCellValue('A3', 'Kelas: ' . $this->className);
        $sheet->setCellValue('A4', 'Periode: ' . $this->period);

        // Gabungkan sel untuk Kop agar berada di tengah
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');
        $sheet->mergeCells('A4:I4');

        // Styling Kop & Penyelarasan Rata Tengah
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:I4')->getFont()->setBold(true);
        $sheet->getStyle('A1:I4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981']
                ]
            ],
        ];
    }
}
