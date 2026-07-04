<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('nis')
                    ->label('NIS')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                TextInput::make('nisn')
                    ->label('NISN')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                    ])
                    ->native(false)
                    ->required(),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->required(),
                TextInput::make('birth_place')
                    ->label('Tempat Lahir')
                    ->maxLength(100),
                TextInput::make('parent_name')
                    ->label('Nama Orang Tua')
                    ->maxLength(255),
                TextInput::make('parent_phone')
                    ->label('Telepon Orang Tua')
                    ->tel()
                    ->maxLength(20),
                Select::make('parent_user_id')
                    ->label('Akun Orang Tua (User)')
                    ->relationship('parent', 'name', fn ($query) => $query->where('role', 'parent'))
                    ->searchable()
                    ->preload(),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'graduated' => 'Lulus',
                        'transferred' => 'Pindah',
                        'dropout' => 'Drop Out',
                    ])
                    ->default('active')
                    ->native(false)
                    ->required(),
                TextInput::make('enrollment_year')
                    ->label('Tahun Masuk')
                    ->numeric(),
                \Filament\Forms\Components\FileUpload::make('photo')
                    ->label('Foto Siswa')
                    ->image()
                    ->maxSize(1024)
                    ->directory('students/photos')
                    ->visibility('public')
                    ->helperText('Upload foto siswa (maksimal 1MB)'),
            ]);
    }
}
