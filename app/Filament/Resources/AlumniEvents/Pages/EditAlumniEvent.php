<?php

namespace App\Filament\Resources\AlumniEvents\Pages;

use App\Filament\Resources\AlumniEvents\AlumniEventResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAlumniEvent extends EditRecord
{
    protected static string $resource = AlumniEventResource::class;

    public function getTitle(): string
    {
        return 'Edit Kegiatan Alumni';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()->role !== 'super_admin') {
            $data['school_id'] = auth()->user()->school_id;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Kegiatan alumni berhasil disimpan')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
