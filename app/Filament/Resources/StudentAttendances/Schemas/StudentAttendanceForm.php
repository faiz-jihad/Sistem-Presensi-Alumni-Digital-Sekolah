<?php

namespace App\Filament\Resources\StudentAttendances\Schemas;

use App\Models\PresensiSession;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class StudentAttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()->toDateString()),
                Select::make('school_id')
                    ->label('Sekolah')
                    ->options(School::query()->orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(function (Get $get) {
                        $schoolId = $get('school_id');

                        return SchoolClass::query()
                            ->when($schoolId, fn($query) => $query->where('school_id', $schoolId))
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('student_id')
                    ->label('Siswa')
                    ->options(function (Get $get) {
                        $schoolId = $get('school_id');
                        $classId = $get('class_id');

                        return Student::query()
                            ->when($schoolId, fn($query) => $query->where('school_id', $schoolId))
                            ->when($classId, fn($query) => $query->where('class_id', $classId))
                            ->orderBy('name')
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'present'    => 'Hadir',
                        'late'       => 'Terlambat',
                        'permission' => 'Izin',
                        'sick'       => 'Sakit',
                        'absent'     => 'Alpha',
                    ])
                    ->required()
                    ->default('present'),
                Select::make('teacher_id')
                    ->label('Guru')
                    ->options(Teacher::query()->orderBy('name')->pluck('name', 'id'))
                    ->nullable()
                    ->searchable()
                    ->preload(),
                TimePicker::make('check_in_time')
                    ->label('Jam Masuk')
                    ->nullable()
                    ->default(now()->format('H:i')),
                Textarea::make('note')
                    ->label('Catatan')
                    ->nullable()
                    ->columnSpanFull(),
                Select::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options([
                        'pending'  => 'Menunggu Verifikasi',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->nullable()
                    ->default('pending'),
            ]);
    }
}
