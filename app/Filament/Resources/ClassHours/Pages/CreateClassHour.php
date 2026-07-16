<?php

namespace App\Filament\Resources\ClassHours\Pages;

use App\Filament\Resources\ClassHours\ClassHourResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassHour extends CreateRecord
{
    protected static string $resource = ClassHourResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['class_hour_package_id'])) {
            $data['class_hour_package_id'] = request()->query('package_id');
        }
        
        $package = \App\Models\ClassHourPackage::find($data['class_hour_package_id']);
        if ($package) {
            $data['school_id'] = $package->school_id;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return "/admin/class-hour-packages/" . $this->record->class_hour_package_id . "/edit";
    }

    public function getTitle(): string
    {
        return 'Tambah Jam Pelajaran';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    public function canCreateAnother(): bool
    {
        return false;
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
}
