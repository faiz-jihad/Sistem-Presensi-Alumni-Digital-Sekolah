<?php

namespace App\Filament\Resources\PresensiSessions\Pages;

use App\Models\PresensiSession;
use App\Filament\Resources\PresensiSessions\PresensiSessionResource;
use Filament\Resources\Pages\Page;

class QrPresensiPage extends Page
{
    protected static string $resource = PresensiSessionResource::class;

    public function getView(): string
    {
        return 'filament.resources.presensi-sessions.pages.qr-presensi';
    }

    protected static ?string $title = 'QR Code Presensi';

    public PresensiSession $record;

    public function mount(PresensiSession $record): void
    {
        $this->record = $record;
    }

    /**
     * Generate QR Code data URL menggunakan Google Charts API
     * Format QR: session_{id}
     */
    public function getQrUrl(): string
    {
        $qrData = "session_{$this->record->id}";
        $encoded = urlencode($qrData);
        return "https://api.qrserver.com/v1/create-qr-code/?size=400x400&margin=20&data={$encoded}";
    }

    public function getQrData(): string
    {
        return "session_{$this->record->id}";
    }
}
