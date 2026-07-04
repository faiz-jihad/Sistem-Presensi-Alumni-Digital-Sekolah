<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeacherForm
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
                    ->required(),
                Select::make('user_id')
                    ->label('Akun Pengguna (User)')
                    ->relationship('user', 'name', fn ($query) => $query->where('role', 'teacher'))
                    ->searchable()
                    ->preload(),
                TextInput::make('nip')
                    ->label('NIP')
                    ->required()
                    ->maxLength(18)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Contoh: 199001012015011001'),
                TextInput::make('name')
                    ->label('Nama Guru')
                    ->placeholder('Contoh: Budi Santoso, S.Pd.')
                    ->required(),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options(['male' => 'Laki-laki', 'female' => 'Perempuan'])
                    ->native(false),
                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('Contoh: 081234567890'),
                Textarea::make('address')
                    ->label('Alamat Lengkap')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('photo')
                    ->label('Foto Guru')
                    ->image()
                    ->maxSize(1024)
                    ->directory('teachers/photos')
                    ->visibility('public')
                    ->helperText('Upload foto guru (maksimal 1MB)'),
                Select::make('employment_status')
                    ->label('Status Kepegawaian')
                    ->options([
                        'pns' => 'PNS',
                        'pppk' => 'PPPK',
                        'honorer' => 'Honorer',
                        'gtt' => 'Guru Tidak Tetap (GTT)',
                        'ptt' => 'Pegawai Tidak Tetap (PTT)',
                        'kontrak' => 'Kontrak',
                    ])
                    ->default('honorer')
                    ->native(false)
                    ->required(),
                TextInput::make('field_of_study')
                    ->label('Mata Pelajaran / Bidang Studi')
                    ->placeholder('Contoh: Matematika, Fisika'),
                TextInput::make('education_level')
                    ->label('Tingkat Pendidikan')
                    ->placeholder('Contoh: S1 Pendidikan Matematika'),
                TextInput::make('university')
                    ->label('Universitas / Perguruan Tinggi')
                    ->placeholder('Contoh: Universitas Negeri Jakarta'),
                DatePicker::make('join_date')
                    ->label('Tanggal Bergabung'),
                Select::make('status')
                    ->label('Status Aktif')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'retired' => 'Pensiun',
                    ])
                    ->default('active')
                    ->native(false)
                    ->required(),
            ]);
    }
}
