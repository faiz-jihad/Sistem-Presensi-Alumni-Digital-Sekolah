<?php

namespace App\Filament\Resources\LaporanResource\Schemas;

use App\Models\School;
use App\Models\StudentClass;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LaporanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Laporan')
                    ->schema([
                        Select::make('school_id')
                            ->label('Sekolah')
                            ->relationship('school', 'name')
                            ->default(fn () => auth()->user()->school_id)
                            ->disabled(fn () => auth()->user()->role !== 'super_admin')
                            ->dehydrated()
                            ->required(),

                        Select::make('type')
                            ->label('Tipe Laporan')
                            ->options([
                                'alumni_report' => 'Laporan Data Alumni',
                                'student_report' => 'Laporan Data Siswa',
                                'teacher_report' => 'Laporan Data Guru',
                            ])
                            ->default('alumni_report')
                            ->live()
                            ->required(),

                        Select::make('file_type')
                            ->label('Format Berkas')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'pdf' => 'PDF (.pdf)',
                            ])
                            ->default('xlsx')
                            ->required(),
                    ])
                    ->columns(3),

                Section::make('Filter Laporan')
                    ->schema([
                        Select::make('filters.graduation_year')
                            ->label('Tahun Lulus')
                            ->options(array_combine(
                                range(date('Y'), 2000),
                                range(date('Y'), 2000)
                            ))
                            ->placeholder('Semua Tahun')
                            ->nullable()
                            ->visible(fn (callable $get) => $get('type') === 'alumni_report'),

                        Select::make('filters.verification_status')
                            ->label('Status Verifikasi')
                            ->options([
                                'pending' => 'Menunggu Verifikasi',
                                'verified' => 'Terverifikasi',
                                'rejected' => 'Ditolak',
                            ])
                            ->placeholder('Semua Status')
                            ->nullable()
                            ->visible(fn (callable $get) => $get('type') === 'alumni_report'),

                        Select::make('filters.class_id')
                            ->label('Kelas')
                            ->options(fn (callable $get) => StudentClass::where('school_id', $get('school_id'))->orderBy('name')->pluck('name', 'id'))
                            ->placeholder('Semua Kelas')
                            ->nullable()
                            ->visible(fn (callable $get) => $get('type') === 'student_report'),

                        Select::make('filters.status')
                            ->label('Status')
                            ->options(fn (callable $get) => $get('type') === 'student_report'
                                ? [
                                    'active' => 'Aktif',
                                    'inactive' => 'Tidak Aktif',
                                    'graduated' => 'Lulus',
                                  ]
                                : [
                                    'active' => 'Aktif',
                                    'inactive' => 'Tidak Aktif',
                                    'retired' => 'Pensiun',
                                  ]
                            )
                            ->placeholder('Semua Status')
                            ->nullable()
                            ->visible(fn (callable $get) => in_array($get('type'), ['student_report', 'teacher_report'])),
                    ])
                    ->columns(2),
            ]);
    }
}