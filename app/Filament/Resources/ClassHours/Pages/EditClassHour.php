<?php

namespace App\Filament\Resources\ClassHours\Pages;

use App\Filament\Resources\ClassHours\ClassHourResource;
use Filament\Resources\Pages\EditRecord;

class EditClassHour extends EditRecord
{
    protected static string $resource = ClassHourResource::class;

    protected function getRedirectUrl(): string
    {
        return "/admin/class-hour-packages/" . $this->record->class_hour_package_id . "/edit";
    }

    public function getTitle(): string
    {
        return 'Edit Jam Pelajaran ';
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

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }
}
