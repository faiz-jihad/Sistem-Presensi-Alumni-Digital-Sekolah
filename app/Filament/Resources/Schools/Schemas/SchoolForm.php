<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Sekolah')
                    ->required(),
                TextInput::make('npsn')
                    ->label('NPSN')
                    ->required()
                    ->maxLength(8)
                    ->unique(ignoreRecord: true),
                Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email(),
                TextInput::make('website')
                    ->label('Website')
                    ->url(),
                FileUpload::make('logo')
                    ->label('Logo Sekolah')
                    ->image()
                    ->directory('schools/logos'),
                TextInput::make('principal_name')
                    ->label('Nama Kepala Sekolah'),
                Select::make('level')
                    ->label('Jenjang')
                    ->options([
                        'sd' => 'SD',
                        'smp' => 'SMP',
                        'sma' => 'SMA',
                        'smk' => 'SMK',
                        'ma' => 'MA'
                    ])
                    ->default('smk')
                    ->required(),
                Select::make('accreditation')
                    ->label('Akreditasi')
                    ->options([
                        'a' => 'A',
                        'b' => 'B',
                        'c' => 'C',
                        'not_accredited' => 'Tidak Terakreditasi'
                    ]),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif'
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
