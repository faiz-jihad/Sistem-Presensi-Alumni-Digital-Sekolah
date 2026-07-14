<?php

namespace App\Filament\Resources\ClassHourPackages\Pages;

use App\Filament\Resources\ClassHourPackages\ClassHourPackageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassHourPackage extends EditRecord
{
    protected static string $resource = ClassHourPackageResource::class;

    public function getTitle(): string
    {
        return 'Edit Paket Jam Pelajaran';
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Simpan');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Kembali');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
