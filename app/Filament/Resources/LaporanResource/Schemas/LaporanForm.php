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
                            ])
                            ->default('alumni_report')
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Select::make('file_type')
                            ->label('Format File')
                            ->options([
                                'xlsx' => 'Excel (.xlsx)',
                                'pdf' => 'PDF (.pdf)',
                            ])
                            ->default('xlsx')
                            ->required(),
                    ])
                    ->columns(3),

                Section::make('Filter Laporan Alumni')
                    ->schema([
                        // Ubah dari TextInput ke Select
                        Select::make('filters.graduation_year')
                            ->label('Tahun Lulus')
                            ->options(array_combine(
                                range(date('Y'), 2000),
                                range(date('Y'), 2000)
                            ))
                            ->placeholder('Semua Tahun')
                            ->nullable(),

                        Select::make('filters.verification_status')
                            ->label('Status Verifikasi')
                            ->options([
                                'pending' => 'Menunggu Verifikasi',
                                'verified' => 'Terverifikasi',
                                'rejected' => 'Ditolak',
                            ])
                            ->placeholder('Semua Status')
                            ->nullable(),
                            
                        // Tambahan filter untuk kelengkapan
                        Select::make('filters.gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ])
                            ->placeholder('Semua Jenis Kelamin')
                            ->nullable(),
                    ])
                    ->columns(3),
            ]);
    }
}