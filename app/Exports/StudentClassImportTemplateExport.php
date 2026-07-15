<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class StudentClassImportTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A4';
    }

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
        // Deskripsi di baris 1–3
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA KELAS — SIMPAD');
        $sheet->setCellValue('A2', 'Keterangan: Kolom bertanda (*) wajib diisi. Baris contoh di bawah dapat dihapus sebelum upload.');
        $sheet->setCellValue('A3', 'Tingkat harus diisi angka 1 sampai 13 (contoh: 12). Tahun Ajaran diisi sesuai tahun ajaran aktif (contoh: 2026/2027).');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2:A3')->getFont()->setItalic(true)->setSize(10);

        return [
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
