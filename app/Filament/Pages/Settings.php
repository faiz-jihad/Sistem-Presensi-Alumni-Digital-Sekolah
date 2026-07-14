<?php

namespace App\Filament\Pages;

use App\Services\WhatsAppService;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Settings extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected string $view = 'filament.pages.settings';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan WhatsApp';

    protected static \UnitEnum|string|null $navigationGroup = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin'], true);
    }

    public function mount(): void
    {
        $this->form->fill([
            'whatsapp_api_url' => env('WHATSAPP_API_URL', 'http://localhost:5000/send'),
            'whatsapp_api_token' => env('WHATSAPP_API_TOKEN', ''),
            'whatsapp_timeout' => env('WHATSAPP_TIMEOUT', 10),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Konfigurasi WhatsApp Gateway')
                    ->description('Tentukan URL endpoint dan parameter untuk local WhatsApp gateway.')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->schema([
                        TextInput::make('whatsapp_api_url')
                            ->label('API URL Endpoint')
                            ->required()
                            ->url(),
                        TextInput::make('whatsapp_api_token')
                            ->label('API Token / Auth Key')
                            ->placeholder('Kosongkan jika local gateway tidak memakai token')
                            ->nullable(),
                        TextInput::make('whatsapp_timeout')
                            ->label('Timeout (Detik)')
                            ->required()
                            ->numeric(),
                    ])
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $envData = [
            'WHATSAPP_API_URL' => $data['whatsapp_api_url'],
            'WHATSAPP_API_TOKEN' => $data['whatsapp_api_token'] ?? '',
            'WHATSAPP_TIMEOUT' => $data['whatsapp_timeout'],
        ];

        $this->updateEnvFile($envData);

        Notification::make()
            ->title('Pengaturan WhatsApp Berhasil Disimpan!')
            ->body('Konfigurasi gateway WhatsApp telah diperbarui.')
            ->success()
            ->send();
    }

    private function updateEnvFile(array $data): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            // Escape nilai jika ada spasi atau karakter khusus
            if ((str_contains($value, ' ') || str_contains($value, '$') || str_contains($value, '#')) && !str_starts_with($value, '"')) {
                $value = '"' . $value . '"';
            }

            // Update jika key ada, buat baru jika tidak ada
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }

    public function getViewData(): array
    {
        return [
            'gatewayStatus' => app(WhatsAppService::class)->getGatewayStatus(),
        ];
    }

    public function sendTestMessageAction(): Action
    {
        return Action::make('sendTestMessage')
            ->label('Kirim Pesan Uji Coba')
            ->icon(Heroicon::OutlinedPaperAirplane)
            ->color('success')
            ->form([
                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->placeholder('Contoh: 081234567890')
                    ->required()
                    ->tel(),
                Textarea::make('message')
                    ->label('Isi Pesan')
                    ->placeholder('Ketik pesan uji coba Anda di sini...')
                    ->default('Uji coba koneksi WhatsApp Gateway SIMPAD sukses!')
                    ->required(),
            ])
            ->action(function (array $data) {
                $service = app(WhatsAppService::class);
                $status = $service->getGatewayStatus();

                if (!$status['ready']) {
                    Notification::make()
                        ->title('Gagal Mengirim Pesan')
                        ->body('Gateway WhatsApp belum terhubung/login.')
                        ->danger()
                        ->send();
                    return;
                }

                $success = $service->sendMessage($data['phone'], $data['message']);

                if ($success) {
                    Notification::make()
                        ->title('Pesan Terkirim!')
                        ->body("Pesan uji coba berhasil dikirim ke nomor {$data['phone']}.")
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Gagal Mengirim Pesan')
                        ->body('Terjadi kesalahan saat mengirim pesan uji coba.')
                        ->danger()
                        ->send();
                }
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->sendTestMessageAction(),
        ];
    }
}
