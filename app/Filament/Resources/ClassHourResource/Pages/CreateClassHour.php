<?php

namespace App\Filament\Resources\ClassHourResource\Pages;

use App\Filament\Resources\ClassHourResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassHour extends CreateRecord
{
    protected static string $resource = ClassHourResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['class_hour_package_id'] = request()->query('package_id');
        
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
}
