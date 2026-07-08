<?php

namespace App\Filament\Resources\ClassHourPackages\Pages;

use App\Filament\Resources\ClassHourPackages\ClassHourPackageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassHourPackage extends CreateRecord
{
    protected static string $resource = ClassHourPackageResource::class;

    public function getTitle(): string
    {
        return 'Tambah Paket Jam Belajar';
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah';
    }
}
