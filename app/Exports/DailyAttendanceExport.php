<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyAttendanceExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    public function __construct(
        private array $data,
        private string $title,
        private $school,
        private string $className,
        private string $date
    ) {}

    public function startCell(): string
    {
        return 'A10';
    }

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
        if ($this->school) {
            if (is_string($this->school)) {
                $schoolModel = \App\Models\School::where('name', $this->school)->first() ?? \App\Models\School::first();
                $schoolName = $this->school;
                $schoolAddress = $schoolModel->address ?? 'Alamat Sekolah';
                $schoolPhone = $schoolModel->phone ?? '-';
                $schoolEmail = $schoolModel->email ?? '-';
            } else {
                $schoolName = $this->school->name ?? 'NAMA SEKOLAH';
                $schoolAddress = $this->school->address ?? 'Alamat Sekolah';
                $schoolPhone = $this->school->phone ?? '-';
                $schoolEmail = $this->school->email ?? '-';
            }
        } else {
            $schoolModel = \App\Models\School::first();
            $schoolName = $schoolModel->name ?? 'Sekolah';
            $schoolAddress = $schoolModel->address ?? 'Alamat Sekolah';
            $schoolPhone = $schoolModel->phone ?? '-';
            $schoolEmail = $schoolModel->email ?? '-';
        }

        $endColumn = 'F';

        // Merge cells for Kop rows
        $sheet->mergeCells("A1:{$endColumn}1");
        $sheet->mergeCells("A2:{$endColumn}2");
        $sheet->mergeCells("A3:{$endColumn}3");
        $sheet->mergeCells("A4:{$endColumn}4");
        $sheet->mergeCells("A5:{$endColumn}5");
        $sheet->mergeCells("A7:{$endColumn}7");
        $sheet->mergeCells("A8:{$endColumn}8");

        // Write values for official letterhead (Kop Surat)
        $sheet->setCellValue('A1', 'DINAS PENDIDIKAN DAN KEBUDAYAAN / YAYASAN PENGELOLA');
        $sheet->setCellValue('A2', strtoupper($schoolName));
        $sheet->setCellValue('A3', $schoolAddress);
        $sheet->setCellValue('A4', 'Telp: ' . $schoolPhone . '  |  Email: ' . $schoolEmail);
        
        // Row 5 is empty, but double bordered
        $sheet->setCellValue('A5', '');

        // Row 6 is empty spacer
        $sheet->setCellValue('A6', '');

        // Title of report
        $sheet->setCellValue('A7', 'LAPORAN REKAPITULASI PRESENSI HARIAN');
        
        // Filter info
        $sheet->setCellValue('A8', "Kelas: {$this->className}   |   Tanggal: {$this->date}   |   Tanggal Cetak: " . \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y HH:mm'));

        // Center Align all header text
        $sheet->getStyle("A1:{$endColumn}8")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Styling Dinas / Yayasan
        $sheet->getStyle('A1')->getFont()->setSize(9)->setBold(true);
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('4B5563'); // Slate gray

        // Styling Nama Sekolah (Premium Black, Larger)
        $sheet->getStyle('A2')->getFont()->setSize(16)->setBold(true);
        $sheet->getStyle('A2')->getFont()->getColor()->setRGB('000000'); // Black

        // Styling Alamat & Kontak
        $sheet->getStyle('A3:A4')->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle('A3:A4')->getFont()->getColor()->setRGB('4B5563');

        // Thick double border line under header (Row 5 bottom) to simulate a real Kop line
        $sheet->getStyle("A5:{$endColumn}5")->getBorders()->getBottom()->setBorderStyle(
            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE
        );
        $sheet->getStyle("A5:{$endColumn}5")->getBorders()->getBottom()->getColor()->setRGB('1F2937');

        // Styling Laporan Title
        $sheet->getStyle('A7')->getFont()->setSize(14)->setBold(true);
        $sheet->getStyle('A7')->getFont()->getColor()->setRGB('1F2937');

        // Styling Filter Info
        $sheet->getStyle('A8')->getFont()->setSize(10)->setBold(true);
        $sheet->getStyle('A8')->getFont()->getColor()->setRGB('4B5563');

        // Rata kiri untuk kolom tabel (No, Nama, NIS, dsb.) beserta datanya
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A10:{$endColumn}{$highestRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        return [
            10 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'] // Blue
                ]
            ],
        ];
    }
}
