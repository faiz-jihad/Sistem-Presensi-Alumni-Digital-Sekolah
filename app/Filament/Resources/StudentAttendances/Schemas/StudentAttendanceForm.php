<?php

namespace App\Filament\Resources\StudentAttendances\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;

class StudentAttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->required()
                    ->searchable(),
                Select::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'name')
                    ->required()
                    ->searchable(),
                Select::make('student_id')
                    ->label('Siswa')
                    ->relationship('student', 'name')
                    ->required()
                    ->searchable(),
                Select::make('teacher_id')
                    ->label('Guru')
                    ->relationship('teacher', 'name')
                    ->nullable()
                    ->searchable(),
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required(),
                TimePicker::make('check_in_time')
                    ->label('Waktu Masuk')
                    ->nullable(),
                TimePicker::make('check_out_time')
                    ->label('Waktu Keluar')
                    ->nullable(),
                Select::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'present'    => 'Hadir',
                        'late'       => 'Terlambat',
                        'permission' => 'Izin',
                        'sick'       => 'Sakit',
                        'absent'     => 'Alpha',
                    ])
                    ->required(),
                Textarea::make('note')
                    ->label('Catatan')
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options([
                        'pending'  => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->nullable(),
            ]);
    }
}
