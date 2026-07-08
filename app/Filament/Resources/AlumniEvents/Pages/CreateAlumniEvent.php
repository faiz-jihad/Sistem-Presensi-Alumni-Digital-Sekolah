<?php

namespace App\Filament\Resources\AlumniEvents\Pages;

use App\Filament\Resources\AlumniEvents\AlumniEventResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateAlumniEvent extends CreateRecord
{
    protected static string $resource = AlumniEventResource::class;

    public function getTitle(): string
    {
        return 'Tambah Kegiatan Alumni';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Tambah Lagi');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Kembali');
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['posted_by'] = auth()->id();

        if (auth()->user()->role === 'alumni') {
            $data['approval_status'] = 'pending';
        } else {
            $data['approval_status'] = 'approved';
        }

        if (auth()->user()->role !== 'super_admin') {
            $data['school_id'] = auth()->user()->school_id;
        }

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Kegiatan alumni berhasil ditambahkan')
            ->success();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
