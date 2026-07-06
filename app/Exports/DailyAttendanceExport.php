<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyAttendanceExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
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
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB']
                ]
            ],
        ];
    }
}
