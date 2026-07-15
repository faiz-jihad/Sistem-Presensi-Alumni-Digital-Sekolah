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
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'],
                ],
            ],
        ];
    }
}
