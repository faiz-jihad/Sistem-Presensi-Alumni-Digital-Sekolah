<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // SECTION 1: INFORMASI UTAMA (WAJIB)
                Section::make('Informasi Utama Akun & Profil')
                    ->description('Wajib diisi untuk pembuatan akun login dan data mengajar.')
                    ->columns(2)
                    ->schema([
                        Select::make('school_id')
                            ->label('Sekolah')
                            ->relationship('school', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        TextInput::make('name')
                            ->label('Nama Lengkap Guru')
                            ->placeholder('Contoh: Budi Santoso, S.Kom.')
                            ->required(),

                        TextInput::make('nip')
                            ->label('NIP (Nomor Induk Pegawai)')
                            ->required()
                            ->maxLength(18)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: 198706052010011001'),

                        TextInput::make('field_of_study')
                            ->label('Mata Pelajaran Utama')
                            ->placeholder('Contoh: Pemrograman Web, Matematika'),

                        TextInput::make('email')
                            ->label('Alamat Email Login')
                            ->email()
                            ->placeholder('Contoh: budi@sekolah.sch.id')
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignorable: fn ($record) => $record?->user
                            ),

                        TextInput::make('password')
                            ->label('Kata Sandi (Password)')
                            ->password()
                            ->placeholder(fn (string $context): string => $context === 'create' ? 'Minimal 8 karakter' : 'Biarkan kosong jika tidak diubah')
                            ->required(fn (string $context): bool => $context === 'create'),

                        Select::make('status')
                            ->label('Status Aktif Mengajar')
                            ->options([
                                'active' => 'Aktif Mengajar',
                                'inactive' => 'Tidak Aktif',
                                'retired' => 'Pensiun',
                            ])
                            ->default('active')
                            ->native(false)
                            ->required(),
                    ]),

                // SECTION 2: INFORMASI AKADEMIK & KEPANGKATAN (OPSIONAL - TERLIPAT SECARA DEFAULT)
                Section::make('Riwayat Akademik & Status Pegawai (Opsional)')
                    ->description('Informasi latar belakang pendidikan tinggi guru.')
                    ->collapsed() // Terlipat secara default agar tampilan bersih
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('education_level')
                            ->label('Tingkat Pendidikan Terakhir')
                            ->placeholder('Contoh: S1 Pendidikan Komputer'),

                        TextInput::make('university')
                            ->label('Lulusan Perguruan Tinggi / Universitas')
                            ->placeholder('Contoh: Universitas Negeri Jakarta'),

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
                            ->native(false),

                        DatePicker::make('join_date')
                            ->label('Tanggal Mulai Bertugas'),
                    ]),

                // SECTION 3: PROFIL FISIK & KONTAK (OPSIONAL - TERLIPAT SECARA DEFAULT)
                Section::make('Kontak & Foto Profil (Opsional)')
                    ->description('Data alamat rumah, nomor telepon, dan foto guru.')
                    ->collapsed() // Terlipat secara default agar tampilan bersih
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['male' => 'Laki-laki', 'female' => 'Perempuan'])
                            ->native(false),

                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 081234567890'),

                        Textarea::make('address')
                            ->label('Alamat Rumah Lengkap')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('photo')
                            ->label('Foto Profil Guru')
                            ->image()
                            ->maxSize(1024)
                            ->directory('teachers/photos')
                            ->visibility('public')
                            ->columnSpanFull()
                            ->helperText('Upload foto format JPG/PNG (maksimal 1MB)'),
                    ]),
            ]);
    }
}
