<?php

namespace App\Filament\Resources\Semesters\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SemesterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

            Select::make('academic_year_id')
                ->label('Tahun Ajaran')
                ->relationship('academicYear', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('type')
                ->label('Jenis Semester')
                ->required()
                ->options([
                    'odd' => 'Semester Ganjil',
                    'even' => 'Semester Genap',
                ])
                ->native(false),

            TextInput::make('name')
                ->label('Nama Semester')
                ->placeholder('Contoh: Semester Ganjil 2026/2027')
                ->required(),

            DatePicker::make('start_date')
                ->label('Tanggal Mulai')
                ->required(),

            DatePicker::make('end_date')
                ->label('Tanggal Selesai')
                ->required(),

            Toggle::make('is_active')
                ->label('Semester Aktif')
                ->default(false),
            ]);
    }
}