<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlumniExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    public function __construct(
        private array $data,
        private string $title,
        private string $schoolName = '',
        private string $schoolAddress = '',
        private string $schoolPhone = '',
        private ?string $graduationYear = null,
        private ?string $verificationStatus = null,
    ) {}

    public function startCell(): string
    {
        return 'A6';
    }

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
        // ── Kop Sekolah ──────────────────────────────────────────────────
        $sheet->setCellValue('A1', 'LAPORAN DATA ALUMNI');
        $sheet->setCellValue('A2', 'Nama Sekolah: ' . ($this->schoolName ?: '-'));
        $sheet->setCellValue('A3', 'Alamat: ' . ($this->schoolAddress ?: '-'));
        $sheet->setCellValue('A4', 'No. Telepon: ' . ($this->schoolPhone ?: '-'));

        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        $sheet->mergeCells('A4:J4');

        if ($this->graduationYear) {
            $sheet->setCellValue('A5', 'Tahun Lulus: ' . $this->graduationYear);
            $sheet->mergeCells('A5:J5');
        }

        // Styling kop & Penyelarasan Rata Tengah
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:J5')->getFont()->setBold(true);
        $sheet->getStyle('A1:J5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981'] // Green for Alumni
                ]
            ],
        ];
    }
}

