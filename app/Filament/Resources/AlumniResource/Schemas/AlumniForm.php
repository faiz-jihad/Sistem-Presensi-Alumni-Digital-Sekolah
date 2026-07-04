<?php

namespace App\Filament\Resources\AlumniResource\Schemas;

use App\Models\School;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AlumniForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Data Alumni')
                    ->schema([

                        Select::make('school_id')
                            ->label('Sekolah')
                            ->relationship('school', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        TextInput::make('name')
                            ->label('Nama Alumni')
                            ->required()
                            ->maxLength(255),

                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->required()
                            ->options([
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ]),

                        TextInput::make('graduation_year')
                            ->label('Tahun Lulus')
                            ->numeric()
                            ->required(),

                        TextInput::make('class_name')
                            ->label('Kelas')
                            ->required(),

                        TextInput::make('major')
                            ->label('Jurusan'),

                        FileUpload::make('photo')
                            ->label('Foto Alumni')
                            ->directory('alumni')
                            ->image()
                            ->imageEditor()
                            ->disk('public'),

                        TextInput::make('email')
                            ->email(),

                        TextInput::make('phone')
                            ->label('No HP'),

                    ])
                    ->columns(2),

                Section::make('Verifikasi')
                    ->schema([

                        Select::make('verification_status')
                            ->label('Status')
                            ->required()
                            ->default('pending')
                            ->options([
                                'pending' => 'Menunggu',
                                'verified' => 'Terverifikasi',
                                'rejected' => 'Ditolak',
                            ]),

                        Select::make('verified_by')
                            ->relationship('verifiedBy', 'name')
                            ->label('Diverifikasi Oleh')
                            ->searchable()
                            ->preload(),

                        DateTimePicker::make('verified_at')
                            ->label('Tanggal Verifikasi'),

                        Textarea::make('verification_notes')
                            ->label('Catatan')
                            ->rows(4)
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}