<?php

namespace App\Filament\Resources\PackageResource\Pages;

use App\Filament\Resources\PackageResource\PackageResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\DeleteAction;

class EditPackage extends EditRecord
{
    protected static string $resource = PackageResource::class;

    public function getTitle(): string
    {
        return 'Edit Paket Langganan';
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Kembali');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    $this->record->schools()->update(['package_id' => null]);
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
