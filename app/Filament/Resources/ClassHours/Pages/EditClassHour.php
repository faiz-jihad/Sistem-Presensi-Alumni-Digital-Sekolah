<?php

namespace App\Filament\Resources\ClassHours\Pages;

use App\Filament\Resources\ClassHours\ClassHourResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassHour extends EditRecord
{
    protected static string $resource = ClassHourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
