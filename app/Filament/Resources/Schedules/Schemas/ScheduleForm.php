<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Utama Jadwal')
                    ->components([
                        Grid::make(2)
                            ->components([
                                Select::make('school_id')
                                    ->relationship('school', 'name')
                                    ->label('Sekolah')
                                    ->required()
                                    ->visible(fn () => auth()->user()->role === 'super_admin')
                                    ->default(fn () => auth()->user()->school_id)
                                    ->live()
                                    ->afterStateUpdated(function (callable $set) {
                                        $set('class_id', null);
                                        $set('subject_id', null);
                                        $set('teacher_id', null);
                                        $set('semester_id', null);
                                        $set('class_hour_package_id', null);
                                        $set('class_hour_id', null);
                                    }),
                                Select::make('semester_id')
                                    ->label('Semester / Tahun Ajaran')
                                    ->options(function (callable $get) {
                                        $schoolId = $get('school_id') ?? auth()->user()->school_id;
                                        if (!$schoolId) return [];
                                        return \App\Models\Semester::whereHas('academicYear', function ($q) use ($schoolId) {
                                            $q->where('school_id', $schoolId);
                                        })->get()->mapWithKeys(function ($sem) {
                                            return [$sem->id => "{$sem->academicYear->name} - {$sem->name}"];
                                        });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->placeholder('Pilih Semester'),
                                Select::make('class_id')
                                    ->label('Kelas')
                                    ->options(function (callable $get) {
                                        $schoolId = $get('school_id') ?? auth()->user()->school_id;
                                        if (!$schoolId) return [];
                                        return \App\Models\StudentClass::where('school_id', $schoolId)->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->placeholder('Pilih Kelas'),
                                Select::make('subject_id')
                                    ->label('Mata Pelajaran')
                                    ->options(function (callable $get) {
                                        $schoolId = $get('school_id') ?? auth()->user()->school_id;
                                        if (!$schoolId) return [];
                                        return \App\Models\Subject::where('school_id', $schoolId)->where('status', 'active')->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->placeholder('Pilih Mata Pelajaran'),
                                Select::make('teacher_id')
                                    ->label('Guru Pengampu')
                                    ->options(function (callable $get) {
                                        $schoolId = $get('school_id') ?? auth()->user()->school_id;
                                        if (!$schoolId) return [];
                                        return \App\Models\Teacher::where('school_id', $schoolId)->where('status', 'active')->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->native(false)
                                    ->placeholder('Pilih Guru'),
                            ]),
                    ]),

                Section::make('Pengaturan Waktu & Lokasi')
                    ->components([
                        Grid::make(2)
                            ->components([
                                Select::make('class_hour_package_id')
                                    ->label('Paket Jam Pelajaran')
                                    ->options(function (callable $get) {
                                        $schoolId = $get('school_id') ?? auth()->user()->school_id;
                                        if (!$schoolId) return [];
                                        return \App\Models\ClassHourPackage::where('school_id', $schoolId)->where('status', 'active')->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->placeholder('Pilih Paket Jam')
                                    ->afterStateUpdated(fn (callable $set) => $set('class_hour_id', null)),
                                Select::make('class_hour_id')
                                    ->label('Jam Pelajaran')
                                    ->options(function (callable $get) {
                                        $packageId = $get('class_hour_package_id');
                                        if (!$packageId) return [];
                                        return \App\Models\ClassHour::where('class_hour_package_id', $packageId)
                                            ->where('status', 'active')
                                            ->orderBy('order')
                                            ->get()
                                            ->mapWithKeys(function ($hour) {
                                                $type = $hour->is_break ? ' (Istirahat)' : '';
                                                return [$hour->id => "Jam Ke-{$hour->order} ({$hour->code}): " . substr($hour->start_time, 0, 5) . " - " . substr($hour->end_time, 0, 5) . $type];
                                            });
                                    })
                                    ->required()
                                    ->disabled(fn (callable $get) => !$get('class_hour_package_id'))
                                    ->native(false)
                                    ->placeholder('Pilih Jam Pelajaran'),
                                Select::make('day')
                                    ->label('Hari')
                                    ->options([
                                        'monday' => 'Senin',
                                        'tuesday' => 'Selasa',
                                        'wednesday' => 'Rabu',
                                        'thursday' => 'Kamis',
                                        'friday' => 'Jumat',
                                        'saturday' => 'Sabtu',
                                        'sunday' => 'Minggu',
                                    ])
                                    ->required()
                                    ->native(false),
                                TextInput::make('room')
                                    ->label('Ruangan')
                                    ->placeholder('Contoh: Ruang Lab, R-102'),
                                DatePicker::make('effective_start_date')
                                    ->label('Tanggal Mulai Berlaku'),
                                DatePicker::make('effective_end_date')
                                    ->label('Tanggal Akhir Berlaku'),
                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                                Toggle::make('allow_early_open')
                                    ->label('Izinkan Buka Sesi Lebih Awal')
                                    ->default(false),
                            ]),
                    ]),
            ]);
    }
}
