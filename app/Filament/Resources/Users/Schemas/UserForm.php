<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Telepon')
                    ->tel(),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->label('Peran')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin' => 'Admin',
                        'teacher' => 'Guru',
                        'student' => 'Siswa',
                        'parent' => 'Orang Tua / Wali',
                        'alumni' => 'Alumni',
                    ])
                    ->default('student')
                    ->required(),
                TextInput::make('school_id')
                    ->label('ID Sekolah')
                    ->numeric(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ])
                    ->default('active')
                    ->required(),
                DateTimePicker::make('email_verified_at')
                    ->label('Verifikasi Email Pada'),
            ]);
    }
}
