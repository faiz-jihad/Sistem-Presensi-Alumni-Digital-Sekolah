<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('class_id')
                    ->label('Kelas')
                    ->relationship('class', 'name')
                    ->searchable()
                    ->preload(),

                // ── Data Orang Tua / Wali ─────────────────────────────────
                TextInput::make('parent_name')
                    ->label('Nama Orang Tua / Wali')
                    ->placeholder('Contoh: Budi Santoso')
                    ->dehydrated(false),

                TextInput::make('parent_phone')
                    ->label('Nomor WhatsApp Orang Tua')
                    ->tel()
                    ->placeholder('Contoh: 628123456789')
                    ->dehydrated(false)
                    ->maxLength(20),

                // ── Data Siswa ────────────────────────────────────────────
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
                    ->label('Nama Lengkap Siswa')
                    ->placeholder('Nama Lengkap Siswa')
                    ->required(),

                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'male'   => 'Laki-laki',
                        'female' => 'Perempuan',
                    ])
                    ->required(),

                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active'      => 'Aktif',
                        'inactive'    => 'Tidak Aktif',
                        'graduated'   => 'Lulus',
                        'transferred' => 'Pindahan',
                        'dropout'     => 'Keluar',
                    ])
                    ->default('active')
                    ->required(),

                // ── Akun Login Siswa (Mobile) ─────────────────────────────
                TextInput::make('email')
                    ->label('Email Akun Siswa (Login Mobile)')
                    ->email()
                    ->placeholder('Contoh: siswa@sekolah.sch.id')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        table: 'users',
                        column: 'email',
                        ignorable: function ($record) {
                            if (! $record) {
                                return null;
                            }
                            return \App\Models\User::where('role', 'student')
                                ->where(function ($q) use ($record) {
                                    $q->where('email', $record->nis)
                                      ->orWhere('name', $record->name);
                                })
                                ->first();
                        }
                    ),

                TextInput::make('password')
                    ->label('Password Akun Siswa')
                    ->password()
                    ->placeholder('Masukkan password untuk login mobile')
                    ->dehydrated(false)
                    ->required(fn (string $context): bool => $context === 'create'),
            ]);
    }
}
