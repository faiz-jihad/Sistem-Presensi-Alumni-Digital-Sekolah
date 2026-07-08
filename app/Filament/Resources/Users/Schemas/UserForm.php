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
                    ->label('Nama Lengkap')
                    ->placeholder('Masukkan nama lengkap')
                    ->required(),
                    
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->placeholder('contoh@sekolah.sch.id')
                    ->unique(ignoreRecord: true)
                    ->required(),
                    
                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->placeholder('Contoh: 08123456789')
                    ->tel()
                    ->maxLength(20),
                    
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->placeholder(fn (string $context): string => $context === 'create' ? 'Kata sandi minimal 8 karakter' : 'Biarkan kosong jika tidak diubah')
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                    
                Select::make('role')
                    ->label('Peran (Role)')
                    ->options(function () {
                        $currentUser = auth()->user();
                        
                        // Jika bukan super_admin, sembunyikan opsi Super Admin dari dropdown
                        if ($currentUser && !$currentUser->isSuperAdmin()) {
                            return [
                                'admin' => 'Admin Sekolah',
                                'teacher' => 'Guru',
                                'student' => 'Siswa',
                                'parent' => 'Orang Tua / Wali',
                                'alumni' => 'Alumni',
                            ];
                        }
                        
                        return [
                            'super_admin' => 'Super Admin',
                            'admin' => 'Admin Sekolah',
                            'teacher' => 'Guru',
                            'student' => 'Siswa',
                            'parent' => 'Orang Tua / Wali',
                            'alumni' => 'Alumni',
                        ];
                    })
                    ->default('student')
                    ->required()
                    ->native(false),
                    
                Select::make('school_id')
                    ->label('Sekolah')
                    ->relationship('school', 'name')
                    ->searchable()
                    ->preload()
                    // Jika login sebagai Admin Sekolah biasa, otomatis set sekolahnya dan sembunyikan inputnya
                    ->default(fn () => auth()->user()?->school_id)
                    ->disabled(fn () => !auth()->user()?->isSuperAdmin())
                    ->dehydrated(true) // Tetap dikirim saat form disimpan meski di-disabled
                    ->helperText('Hanya Super Admin yang dapat mengganti sekolah dari user.'),
                    
                Select::make('status')
                    ->label('Status Akun')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ])
                    ->default('active')
                    ->native(false)
                    ->required(),
                    
                DateTimePicker::make('email_verified_at')
                    ->label('Diverifikasi Pada')
                    ->placeholder('Tanggal verifikasi email')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }
}
