<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Nama Sekolah')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: SMK Negeri 1 Jakarta')
                    ->live(debounce: 500),
                    
                TextInput::make('npsn')
                    ->label('NPSN')
                    ->required()
                    ->maxLength(8)
                    ->unique(ignoreRecord: true)
                    ->helperText('Nomor Pokok Sekolah Nasional (8 digit)')
                    ->placeholder('Contoh: 20234567'),
                    
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Masukkan alamat lengkap sekolah'),
                    
                TextInput::make('phone')
                    ->label('Telepon')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('Contoh: 021-1234567'),
                    
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->placeholder('contoh@sekolah.com')
                    ->live(debounce: 500),
                    
                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->maxLength(255)
                    ->placeholder('https://sekolah.sch.id')
                    ->live(debounce: 500),
                    
                TextInput::make('principal_name')
                    ->label('Nama Kepala Sekolah')
                    ->maxLength(255)
                    ->placeholder('Masukkan nama kepala sekolah'),
                    
                FileUpload::make('logo')
                    ->label('Logo Sekolah')
                    ->image()
                    ->imageEditor()
                    ->maxSize(2048)  // 2MB
                    ->directory('schools/logos')
                    ->visibility('public')
                    ->helperText('Upload logo sekolah (maksimal 2MB)'),
                    
                Select::make('level')
                    ->label('Jenjang')
                    ->required()
                    ->options([
                        'sd' => 'SD',
                        'smp' => 'SMP',
                        'sma' => 'SMA',
                        'smk' => 'SMK',
                        'ma' => 'MA',
                    ])
                    ->default('smk')
                    ->native(false)
                    ->searchable(),
                    
                Select::make('accreditation')
                    ->label('Akreditasi')
                    ->options([
                        'a' => 'A (Unggul)',
                        'b' => 'B (Baik)',
                        'c' => 'C (Cukup)',
                        'not_accredited' => 'Belum Terakreditasi',
                    ])
                    ->native(false)
                    ->placeholder('Pilih akreditasi'),
                    
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ])
                    ->default('active')
                    ->native(false),
            ]);
    }
}