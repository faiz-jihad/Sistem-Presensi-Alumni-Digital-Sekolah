<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Langkah 1: Pilih Kelas')
                        ->description('Tentukan sekolah dan kelas target')
                        ->icon('heroicon-m-academic-cap')
                        ->schema([
                            Select::make('school_id')
                                ->label('Sekolah')
                                ->relationship('school', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('class_id')
                                ->label('Kelas')
                                ->relationship('class', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                    Step::make('Langkah 2: Pilih Pelajaran')
                        ->description('Pilih guru pengampu dan mata pelajaran')
                        ->icon('heroicon-m-user-group')
                        ->schema([
                            Select::make('subject_id')
                                ->label('Mata Pelajaran')
                                ->relationship('subject', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('teacher_id')
                                ->label('Guru Pengampu')
                                ->relationship('teacher', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                    Step::make('Langkah 3: Hari & Jam')
                        ->description('Tentukan waktu pelaksanaan pelajaran')
                        ->icon('heroicon-m-clock')
                        ->schema([
                            Select::make('semester_id')
                                ->label('Semester')
                                ->relationship('semester', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
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
                                ->native(false)
                                ->required(),
                            Select::make('class_hour_id')
                                ->label('Jam Pelajaran')
                                ->relationship('classHour', 'code')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->unique(ignorable: fn ($record) => $record, modifyRuleUsing: function (\Illuminate\Validation\Rules\Unique $rule, $get) {
                                    return $rule->where('class_id', $get('class_id'))
                                        ->where('day', $get('day'))
                                        ->where('semester_id', $get('semester_id'));
                                })
                                ->validationMessages([
                                    'unique' => 'Jadwal untuk kelas, hari, jam, dan semester ini sudah terdaftar.',
                                ]),
                        ]),
                    Step::make('Langkah 4: Konfigurasi Tambahan')
                        ->description('Detail penunjang kelas')
                        ->icon('heroicon-m-cog-6-tooth')
                        ->schema([
                            TextInput::make('room')
                                ->label('Ruangan / Kelas')
                                ->placeholder('Contoh: Ruang Laboratorium, Kelas XII RPL 1')
                                ->maxLength(255),
                            DatePicker::make('effective_start_date')
                                ->label('Tanggal Mulai Berlaku'),
                            DatePicker::make('effective_end_date')
                                ->label('Tanggal Selesai Berlaku'),
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(true),
                            Toggle::make('allow_early_open')
                                ->label('Izinkan Buka Kelas Lebih Awal')
                                ->helperText('Guru boleh membuka presensi sebelum jam pelajaran dimulai')
                                ->default(false),
                        ]),
                ])
                ->columnSpanFull()
            ]);
    }
}
