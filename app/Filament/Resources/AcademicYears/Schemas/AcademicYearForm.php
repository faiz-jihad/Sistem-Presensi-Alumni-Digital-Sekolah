<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('Tahun Pelajaran')
                    ->placeholder('Contoh: 2023/2024')
                    ->required(),
                TextInput::make('start_year')
                    ->label('Tahun Mulai')
                    ->required()
                    ->numeric(),
                TextInput::make('end_year')
                    ->label('Tahun Selesai')
                    ->required()
                    ->numeric(),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->required(),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
