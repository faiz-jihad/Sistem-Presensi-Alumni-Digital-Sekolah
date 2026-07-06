<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class CustomEditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Profil Pengguna')
                    ->description('Perbarui nama lengkap, alamat email, dan nomor telepon WhatsApp Anda.')
                    ->columns(2)
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        TextInput::make('phone')
                            ->label('Nomor Telepon / WhatsApp')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 081234567890'),
                    ]),

                Section::make('Keamanan & Kata Sandi (Password)')
                    ->description('Kosongkan kolom sandi jika Anda tidak ingin memperbarui kata sandi saat ini.')
                    ->columns(2)
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ]),
            ]);
    }
}
