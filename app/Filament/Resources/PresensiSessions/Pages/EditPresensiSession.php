<?php

namespace App\Filament\Resources\PresensiSessions\Pages;

use App\Filament\Resources\PresensiSessions\PresensiSessionResource;
use App\Services\PresensiSessionService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditPresensiSession extends EditRecord
{
    protected static string $resource = PresensiSessionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            return app(PresensiSessionService::class)->update($record, $data);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Sesi Presensi Gagal Diperbarui')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function ($record) {
                    if ($record->studentAttendances()->exists()) {
                        Notification::make()
                            ->title('Sesi Tidak Dapat Dihapus')
                            ->body('Sesi sudah memiliki data presensi siswa.')
                            ->danger()
                            ->send();

                        $this->halt();
                    }
                }),
        ];
    }
}