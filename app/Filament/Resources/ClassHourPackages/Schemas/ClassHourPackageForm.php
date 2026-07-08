<?php

namespace App\Filament\Resources\ClassHourPackages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class ClassHourPackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Paket Jam Pelajaran')
                    ->components([
                        Select::make('school_id')
                            ->relationship('school', 'name')
                            ->label('Sekolah')
                            ->required()
                            ->visible(fn () => auth()->user()->role === 'super_admin')
                            ->default(fn () => auth()->user()->school_id),
                        TextInput::make('name')
                            ->label('Nama Paket')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Paket Pagi, Paket Siang'),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                    ])
            ]);
    }
}
