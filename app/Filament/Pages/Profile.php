<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Profil Saya';
    protected static ?string $title = 'Profil Saya';
    protected static ?int $navigationSort = 100;
    protected string $view = 'filament.pages.profile';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->form->fill(
            auth()->user()->only(['name', 'email', 'phone'])
        );
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->schema([
                $this->profileInformationSection(),
                $this->securitySection(),
            ]);
    }

    protected function profileInformationSection(): Section
    {
        return Section::make('Informasi Profil')
            ->description('Perbarui informasi akun Anda.')
            // ❌ HAPUS ->icon()
            ->columns(2)
            ->schema([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->required()
                    ->unique(
                        table: 'users',
                        column: 'email',
                        ignorable: auth()->user()
                    ),

                TextInput::make('phone')
                    ->label('Nomor WhatsApp')
                    ->tel()
                    ->placeholder('081234567890')
                    ->maxLength(20),

                Placeholder::make('google_status')
                    ->label('Status Login Google')
                    ->columnSpanFull()
                    ->content(fn (): Htmlable => $this->googleStatusHtml()),
            ]);
    }

    protected function securitySection(): Section
    {
        return Section::make('Keamanan')
            ->description('Kosongkan password jika tidak ingin mengubahnya.')
            // ❌ HAPUS ->icon()
            ->columns(2)
            ->schema([
                TextInput::make('password')
                    ->label('Password Baru')
                    ->password()
                    ->revealable()
                    ->rule(Password::defaults())
                    ->same('passwordConfirmation')
                    ->dehydrated(fn (?string $state): bool => filled($state)),

                TextInput::make('passwordConfirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->dehydrated(false),
            ]);
    }

    protected function googleStatusHtml(): Htmlable
    {
        $connected = filled(auth()->user()->google_id);

        $badge = $connected
            ? '<div class="google-sso-badge google-sso-badge--connected">
                <span class="google-sso-badge__dot google-sso-badge__dot--pulse"></span>
                Terhubung
               </div>'
            : '<div class="google-sso-badge google-sso-badge--disconnected">
                <span class="google-sso-badge__dot"></span>
                Belum Terhubung
               </div>';

        $html = '
        <div class="google-sso-card">
            <div class="google-sso-card__left">
                <div class="google-sso-card__logo-wrapper">
                    <svg class="google-sso-card__logo" viewBox="0 0 24 24">
                        <path fill="#EA4335" d="M5.266 9.765A7.077 7.077 0 0 1 12 4.909c1.69 0 3.218.6 4.418 1.582l3.51-3.51C17.828.95 15.084 0 12 0 7.39 0 3.41 2.657 1.477 6.545l3.789 3.22z"/>
                        <path fill="#34A853" d="M16.04 15.345c-1.07.728-2.42 1.164-4.04 1.164-2.927 0-5.418-1.982-6.303-4.654L1.91 15.073C3.882 19.018 7.98 21.818 12 21.818c3.09 0 5.927-1.09 8.082-2.973l-4.042-3.5z"/>
                        <path fill="#4285F4" d="M23.82 12.273c0-.818-.08-1.61-.227-2.373H12v4.51h6.636c-.285 1.51-1.136 2.782-2.42 3.636l4.043 3.5c2.364-2.18 3.56-5.38 3.56-9.273z"/>
                        <path fill="#FBBC05" d="M5.697 11.855a7.002 7.002 0 0 1 0-2.09L1.91 6.545a11.96 11.96 0 0 0 0 10.91l3.788-3.6z"/>
                    </svg>
                </div>
                <div class="google-sso-card__text">
                    <div class="google-sso-card__title">Google Single Sign-On (SSO)</div>
                    <div class="google-sso-card__desc">Masuk ke sistem secara cepat menggunakan kredensial Google Anda.</div>
                </div>
            </div>
            <div>
                ' . $badge . '
            </div>
        </div>';

        return new HtmlString($html);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $user = auth()->user();
        $data = $this->form->getState();

        $payload = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if (filled($data['password'] ?? null)) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui!')
            ->body('Semua perubahan telah disimpan dengan sukses.')
            ->send();

        $this->dispatch('profile-updated');
    }
}