<?php

namespace App\Filament\Pages;

use App\Services\WhatsAppService;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

    protected static ?string $navigationLabel = 'WhatsApp Gateway';

    protected static ?string $title = 'WhatsApp Gateway';

    protected static \UnitEnum|string|null $navigationGroup = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 100;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin'], true);
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
