<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->required(),
                Select::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'name'),
                Select::make('parent_user_id')
                    ->label('Wali Murid / Orang Tua')
                    ->relationship('parent', 'name'),
                TextInput::make('nis')
                    ->label('NIS')
                    ->placeholder('Nomor Induk Siswa')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('nisn')
                    ->label('NISN')
                    ->placeholder('Nomor Induk Siswa Nasional')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->placeholder('Nama Lengkap Siswa')
                    ->required(),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan'
                    ])
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->required(),
                TextInput::make('birth_place')
                    ->label('Tempat Lahir')
                    ->placeholder('Kota Lahir'),
                Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
                FileUpload::make('photo')
                    ->label('Foto Siswa')
                    ->image()
                    ->directory('students/photos'),
                TextInput::make('parent_name')
                    ->label('Nama Orang Tua / Wali'),
                TextInput::make('parent_phone')
                    ->label('Nomor WhatsApp Orang Tua')
                    ->tel()
                    ->placeholder('Contoh: 628123456789')
                    ->maxLength(20),
                TextInput::make('enrollment_year')
                    ->label('Tahun Masuk')
                    ->numeric()
                    ->placeholder('Contoh: 2023'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'graduated' => 'Lulus',
                        'transferred' => 'Pindahan',
                        'dropout' => 'Keluar',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
