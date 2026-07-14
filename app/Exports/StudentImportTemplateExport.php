<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentImportTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        // Contoh baris data agar admin paham format
        return [
            ['12345', '1234567890', 'Ahmad Fajar Ramadhan', 'Laki-laki', '2006-08-15', 'X RPL 1', 'active', 'Budi Santoso', '628123456789', 'ahmad.fajar@sekolah.sch.id', 'password123'],
            ['12346', '1234567891', 'Siti Nurhaliza', 'Perempuan', '2007-03-22', 'X AKL 1', 'active', 'Rina Wati', '628987654321', 'siti.nurhaliza@sekolah.sch.id', 'password123'],
        ];
    }

    public function headings(): array
    {
        return [
            'NIS *',
            'NISN',
            'Nama Lengkap Siswa *',
            'Jenis Kelamin (Laki-laki/Perempuan) *',
            'Tanggal Lahir (YYYY-MM-DD) *',
            'Kelas (Nama Kelas)',
            'Status (active/inactive/graduated) *',
            'Nama Orang Tua',
            'No WA Orang Tua',
            'Email Akun Siswa',
            'Kata Sandi',
        ];
    }

    public function title(): string
    {
        return 'Template Import Siswa';
    }

    public function styles(Worksheet $sheet)
    {
        // Instruksi di baris 1–3
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA SISWA — SIMPAD');
        $sheet->setCellValue('A2', 'Keterangan: Kolom bertanda (*) wajib diisi. Baris contoh di bawah dapat dihapus sebelum upload.');
        $sheet->setCellValue('A3', 'Kolom "Kelas" diisi dengan nama kelas persis seperti di sistem (contoh: X RPL 1).');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2:A3')->getFont()->setItalic(true)->setSize(10);

        // Style baris header (row 4 karena HeadingRow pakai row pertama, tapi kami mulai data dari row 2 otomatis)
        return [
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ],
        ];
    }
}
