<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlumniExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(
        private array $data,
        private string $title
    ) {}

    public function array(): array
    {
        $rows = [];
        $no = 1;

        foreach ($this->data as $alumni) {
            $gender = ($alumni['gender'] ?? '') === 'male' ? 'Laki-laki' : 'Perempuan';
            $status = match ($alumni['verification_status'] ?? '') {
                'pending' => 'Menunggu Verifikasi',
                'verified' => 'Terverifikasi',
                'rejected' => 'Ditolak',
                default => $alumni['verification_status'] ?? '-'
            };

            $rows[] = [
                $no++,
                $alumni['name'] ?? '-',
                $alumni['nisn'] ?? '-',
                $gender,
                $alumni['graduation_year'] ?? '-',
                $alumni['class_name'] ?? '-',
                $alumni['major'] ?? '-',
                $alumni['email'] ?? '-',
                $alumni['phone'] ?? '-',
                $status,
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Alumni',
            'NISN',
            'Jenis Kelamin',
            'Tahun Lulus',
            'Kelas',
            'Jurusan',
            'Email',
            'No HP',
            'Status Verifikasi',
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
                    'startColor' => ['rgb' => '10B981'] // Green for Alumni
                ]
            ],
        ];
    }
}
