<?php

namespace App\Filament\Resources\TeacherSubjects\Pages;

use App\Filament\Resources\TeacherSubjects\TeacherSubjectResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacherSubject extends EditRecord
{
    protected static string $resource = TeacherSubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
