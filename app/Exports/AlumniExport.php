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
    private bool $isTracerMode = false;

    public function __construct(
        private array $data,
        private string $title,
        private $school = null,
        private ?string $graduationYear = null,
        private ?string $verificationStatus = null
    ) {
        if (!empty($this->data) && (isset($this->data[0]['detail']) || isset($this->data[0]['status']))) {
            $this->isTracerMode = true;
        }
    }

    public function startCell(): string
    {
        return $this->school ? 'A10' : 'A1';
    }



    public function array(): array
    {
        $rows = [];
        $no = 1;

        foreach ($this->data as $alumni) {
            if ($this->isTracerMode) {
                $rows[] = [
                    $no++,
                    $alumni['name'] ?? '-',
                    $alumni['nisn'] ?? '-',
                    $alumni['graduation_year'] ?? '-',
                    $alumni['class_name'] ?? '-',
                    $alumni['major'] ?? '-',
                    $alumni['status'] ?? '-',
                    $alumni['detail'] ?? '-',
                ];
            } else {
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
        }

        return $rows;
    }

    public function headings(): array
    {
        if ($this->isTracerMode) {
            return [
                'No',
                'Nama Alumni',
                'NISN',
                'Tahun Lulus',
                'Kelas',
                'Jurusan',
                'Status Pekerjaan',
                'Detail Status',
            ];
        }

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
        if ($this->school) {
            if (is_string($this->school)) {
                $schoolName = $this->school;
                $schoolAddress = 'Alamat Sekolah';
                $schoolPhone = '-';
                $schoolEmail = '-';
            } else {
                $schoolName = $this->school->name ?? 'NAMA SEKOLAH';
                $schoolAddress = $this->school->address ?? 'Alamat Sekolah';
                $schoolPhone = $this->school->phone ?? '-';
                $schoolEmail = $this->school->email ?? '-';
            }

            $endColumn = $this->isTracerMode ? 'H' : 'J';

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
            $sheet->setCellValue('A7', 'LAPORAN DATA ALUMNI');

            // Filter info
            $tahunLulus = $this->graduationYear ?: 'Semua Tahun';
            if ($this->isTracerMode) {
                $statusLabel = 'Tracer Study (Pekerjaan/Kuliah)';
            } else {
                $statusLabel = match ($this->verificationStatus) {
                    'verified' => 'Terverifikasi',
                    'pending' => 'Menunggu Verifikasi',
                    'rejected' => 'Ditolak',
                    default => 'Semua Status'
                };
            }
            $tanggalCetak = \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y HH:mm');
            $sheet->setCellValue('A8', "Tahun Lulus: {$tahunLulus}   |   Status: {$statusLabel}   |   Tanggal Cetak: {$tanggalCetak}");

            // Center Align all header text
            $sheet->getStyle("A1:{$endColumn}8")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Styling Dinas / Yayasan
            $sheet->getStyle('A1')->getFont()->setSize(9)->setBold(true);
            $sheet->getStyle('A1')->getFont()->getColor()->setRGB('4B5563'); // Slate gray

            // Styling Nama Sekolah (Premium Blue, Larger)
            $sheet->getStyle('A2')->getFont()->setSize(16)->setBold(true);
            $sheet->getStyle('A2')->getFont()->getColor()->setRGB('2563EB'); // Brand blue

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

            $headerRow = 10;

            // Rata kiri untuk kolom tabel (No, Nama, NISN, dsb.) beserta datanya
            $highestRow = $sheet->getHighestRow();
            $sheet->getStyle("A10:{$endColumn}{$highestRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        } else {
            $headerRow = 1;
        }

        return [
            $headerRow => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2563EB'] // Blue for Alumni
                ]
            ],
        ];
    }
}

