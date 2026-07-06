<?php

namespace App\Filament\Resources\PresensiSessions\Pages;

use App\Filament\Resources\PresensiSessions\PresensiSessionResource;
use App\Models\Teacher;
use App\Services\PresensiSessionService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePresensiSession extends CreateRecord
{
    protected static string $resource = PresensiSessionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $teacher = Teacher::where('user_id', auth()->id())->first();

            return app(PresensiSessionService::class)->create($data, $teacher?->id);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Sesi Presensi Gagal Dibuat')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }
}