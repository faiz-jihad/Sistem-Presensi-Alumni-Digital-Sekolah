<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyAttendanceExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(
        private array $data,
        private string $title
    ) {}

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
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981']
                ]
            ],
        ];
    }
}
