<?php

namespace App\Filament\Resources\AlumniResource\Pages;

use App\Filament\Resources\AlumniResource\AlumniResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAlumni extends EditRecord
{
    protected static string $resource = AlumniResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus'),
            Action::make('back')
                ->label('Kembali')
                ->url(fn () => AlumniResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $alumni = $this->getRecord();
        if ($alumni->user) {
            $data['email'] = $alumni->user->email ?? $data['email'];
            $data['phone'] = $alumni->profile?->whatsapp ?? $alumni->user->phone ?? $data['phone'];
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $alumni = $this->getRecord();
        
        if ($alumni->user) {
            $alumni->user->update([
                'email' => $data['email'] ?? $alumni->user->email,
                'phone' => $data['phone'] ?? $alumni->user->phone,
            ]);
        }
        
        if ($alumni->profile && isset($data['phone'])) {
            $alumni->profile->update([
                'whatsapp' => $data['phone']
            ]);
        }

        return $data;
    }
}