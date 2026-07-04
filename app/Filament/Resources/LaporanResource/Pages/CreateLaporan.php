<?php

namespace App\Filament\Resources\LaporanResource\Pages;

use App\Filament\Resources\LaporanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLaporan extends CreateRecord
{
    protected static string $resource = LaporanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        if (empty($data['school_id'])) {
            $data['school_id'] = auth()->user()->school_id;
        }

        $data['status'] = 'pending';
        $data['file_name'] = 'sedang_diproses';
        $data['file_path'] = '';

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        // Generate the export file immediately
        app(\App\Services\ExportService::class)->generate($record);
    }
}
