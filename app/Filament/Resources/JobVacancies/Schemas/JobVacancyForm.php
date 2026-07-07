<?php

namespace App\Filament\Resources\JobVacancies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

class JobVacancyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn () => auth()->user()->school_id)
                    ->disabled(fn () => auth()->user()->role !== 'super_admin')
                    ->dehydrated(),
                Hidden::make('posted_by')
                    ->default(fn () => auth()->id()),
                TextInput::make('title')
                    ->label('Judul Pekerjaan')
                    ->required(),
                TextInput::make('company_name')
                    ->label('Nama Perusahaan')
                    ->required(),
                FileUpload::make('company_logo')
                    ->label('Logo Perusahaan')
                    ->directory('company-logos')
                    ->disk('public')
                    ->image(),
                RichEditor::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull()
                    ->required(),

                RichEditor::make('requirements')
                    ->label('Persyaratan')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('link')
                    ->label('Tautan (LinkedIn, Web, dll)')
                    ->url()
                    ->columnSpanFull()
                    ->nullable(),
                TextInput::make('location')
                    ->label('Lokasi')
                    ->required(),
                TextInput::make('salary_min')
                    ->label('Gaji Minimum')
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('salary_max')
                    ->label('Gaji Maksimum')
                    ->numeric()
                    ->prefix('Rp'),
                DatePicker::make('deadline')
                    ->label('Tenggat Waktu Pendaftaran')
                    ->minDate(now()),
                Select::make('job_type')
                    ->label('Tipe Pekerjaan')
                    ->options([
                        'full_time' => 'Full Time',
                        'part_time' => 'Part Time',
                        'freelance' => 'Freelance',
                        'internship' => 'Magang',
                    ])
                    ->required(),
                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'technology' => 'Teknologi',
                        'education' => 'Pendidikan',
                        'health' => 'Kesehatan',
                        'business' => 'Bisnis',
                        'creative' => 'Kreatif',
                        'engineering' => 'Teknik',
                        'others' => 'Lainnya',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),
            ]);
    }
}
