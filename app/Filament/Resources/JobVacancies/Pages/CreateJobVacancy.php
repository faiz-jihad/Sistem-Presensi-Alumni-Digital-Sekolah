<?php

namespace App\Filament\Resources\JobVacancies\Pages;

use App\Filament\Resources\JobVacancies\JobVacancyResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateJobVacancy extends CreateRecord
{
    protected static string $resource = JobVacancyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['posted_by'] = auth()->id();

        if (auth()->user()->role !== 'super_admin') {
            $data['school_id'] = auth()->user()->school_id;
        }

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Lowongan kerja berhasil ditambahkan')
            ->success();
    }
}