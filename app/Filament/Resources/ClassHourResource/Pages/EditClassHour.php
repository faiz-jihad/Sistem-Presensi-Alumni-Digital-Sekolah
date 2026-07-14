<?php

namespace App\Filament\Resources\ClassHourResource\Pages;

use App\Filament\Resources\ClassHourResource;
use Filament\Resources\Pages\EditRecord;

class EditClassHour extends EditRecord
{
    protected static string $resource = ClassHourResource::class;

    protected function getRedirectUrl(): string
    {
        return "/admin/class-hour-packages/" . $this->record->class_hour_package_id . "/edit";
    }
}
