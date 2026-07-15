<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class SubjectImportTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            ['MTK', 'Matematika', 'MTK', 'general', 4, 'active', 'Mata pelajaran wajib untuk semua jurusan.'],
            ['PW01', 'Pemrograman Web', 'Pemweb', 'specialized', 6, 'active', 'Pemrograman berbasis web menggunakan HTML, CSS, JS.'],
            ['MLOK', 'Bahasa Daerah', 'B.Daerah', 'local', 2, 'active', 'Muatan lokal bahasa daerah setempat.'],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Mapel *',
            'Nama Mata Pelajaran *',
            'Singkatan',
            'Kelompok (general/specialized/local/extracurricular) *',
            'Beban Jam JP *',
            'Status (active/inactive) *',
            'Deskripsi',
        ];
    }

    public function title(): string
    {
        return 'Template Import Mata Pelajaran';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F59E0B'],
                ],
            ],
        ];
    }
}
