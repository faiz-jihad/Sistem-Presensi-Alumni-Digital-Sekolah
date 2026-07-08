<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;

    public function getTitle(): string
    {
        return 'Edit Guru';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Muat alamat email dari user relationship untuk di-render di form
        $teacher = $this->getRecord();
        if ($teacher && $teacher->user) {
            $data['email'] = $teacher->user->email;
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $teacher = $this->getRecord();

            if ($teacher && $teacher->user) {
                // Update data User login yang terhubung
                $userData = [
                    'name'      => $data['name'],
                    'email'     => $data['email'],
                    'school_id' => $data['school_id'],
                ];

                if (!empty($data['password'])) {
                    $userData['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
                }

                $teacher->user->update($userData);
            }

            // Bersihkan fields email dan password agar tidak error saat update data Teacher
            unset($data['email'], $data['password']);

            return $data;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
