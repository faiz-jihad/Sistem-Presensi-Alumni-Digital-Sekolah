<?php

namespace App\Filament\Resources\Semesters\Pages;

use App\Filament\Resources\Semesters\SemesterResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSemesters extends EditRecord
{
    protected static string $resource = SemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
