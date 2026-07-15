<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class TeacherImportTemplateExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A4';
    }

    public function array(): array
    {
        return [
            ['198706052010011001', 'Budi Santoso, S.Pd.', 'budi.santoso@sekolah.sch.id', 'password123', 'Laki-laki', '081234567890', 'Matematika', 'pns', 'active', '2010-01-05', 'S1 Pendidikan Matematika', 'Universitas Negeri Jakarta'],
            ['199002112015042002', 'Rina Wati, S.Kom.', 'rina.wati@sekolah.sch.id', 'password123', 'Perempuan', '082345678901', 'Pemrograman Web', 'honorer', 'active', '2015-04-11', 'S1 Teknik Informatika', 'Universitas Brawijaya'],
        ];
    }

    public function headings(): array
    {
        return [
            'NIP * (18 digit)',
            'Nama Lengkap Guru *',
            'Email Login *',
            'Kata Sandi',
            'Jenis Kelamin (Laki-laki/Perempuan)',
            'No Telepon',
            'Mata Pelajaran Utama',
            'Status Kepegawaian (pns/pppk/honorer/gtt/ptt/kontrak)',
            'Status (active/inactive/retired) *',
            'Tanggal Mulai Bertugas (YYYY-MM-DD)',
            'Tingkat Pendidikan',
            'Universitas',
        ];
    }

    public function title(): string
    {
        return 'Template Import Guru';
    }

    public function styles(Worksheet $sheet)
    {
        // Deskripsi di baris 1–3
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA GURU — SIMPAD');
        $sheet->setCellValue('A2', 'Keterangan: Kolom bertanda (*) wajib diisi. Baris contoh di bawah dapat dihapus sebelum upload.');
        $sheet->setCellValue('A3', 'NIP harus berisi angka sepanjang maksimal 18 digit (tanpa spasi/titik).');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2:A3')->getFont()->setItalic(true)->setSize(10);

        return [
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'],
                ],
            ],
        ];
    }
}
