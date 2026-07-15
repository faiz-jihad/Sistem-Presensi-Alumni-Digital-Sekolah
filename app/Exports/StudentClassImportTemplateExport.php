<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class StudentClassImportTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            ['XII RPL 1', '12', '2026/2027', 'RPL', '198706052010011001', 36, 'Ruang 101', 'active'],
            ['XI MIPA 2', '11', '2026/2027', 'MIPA', '', 36, 'Ruang 204', 'active'],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Kelas *',
            'Tingkat / Kelas (1-13) *',
            'Tahun Ajaran *',
            'Jurusan / Peminatan',
            'NIP Wali Kelas',
            'Kapasitas (Siswa)',
            'Nama / Nomor Ruang Kelas',
            'Status (active/inactive) *',
        ];
    }

    public function title(): string
    {
        return 'Template Import Kelas';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
