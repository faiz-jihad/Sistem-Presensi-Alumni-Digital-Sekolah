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
                    ->required(),
                Select::make('user_id')
                    ->label('Akun Pengguna')
                    ->relationship('user', 'name'),
                TextInput::make('nip')
                    ->label('NIP')
                    ->placeholder('Nomor Induk Pegawai')
                    ->required()
                    ->maxLength(18)
                    ->unique(ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->placeholder('Nama Lengkap Guru')
                    ->required(),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan'
                    ]),
                TextInput::make('phone')
                    ->label('No. Telepon')
                    ->tel()
                    ->placeholder('Contoh: 628123456789')
                    ->maxLength(20),
                Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
                FileUpload::make('photo')
                    ->label('Foto Guru')
                    ->image()
                    ->directory('teachers/photos'),
                Select::make('employment_status')
                    ->label('Status Kepegawaian')
                    ->options([
                        'pns' => 'PNS',
                        'pppk' => 'PPPK',
                        'honorer' => 'Honorer',
                        'gtt' => 'GTT (Guru Tidak Tetap)',
                        'ptt' => 'PTT (Pegawai Tidak Tetap)',
                        'kontrak' => 'Kontrak',
                    ])
                    ->default('honorer')
                    ->required(),
                TextInput::make('field_of_study')
                    ->label('Mata Pelajaran yang Diampu')
                    ->placeholder('Contoh: Matematika, Pemrograman Web'),
                TextInput::make('education_level')
                    ->label('Pendidikan Terakhir')
                    ->placeholder('Contoh: S1 Pendidikan Komputer'),
                TextInput::make('university')
                    ->label('Universitas/Instansi')
                    ->placeholder('Contoh: Universitas Negeri Jakarta'),
                DatePicker::make('join_date')
                    ->label('Tanggal Mulai Tugas'),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'retired' => 'Pensiun'
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }
}
