<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\User;
use App\Services\FilamentChangeNotificationService;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    public function getTitle(): string
    {
        return 'Tambah Siswa';
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->label('Simpan & Tambah Lagi');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Kembali');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hapus field virtual agar tidak masuk ke INSERT students
        unset($data['parent_name'], $data['parent_phone'], $data['email'], $data['password']);
        return $data;
    }

    protected function afterCreate(): void
    {
        app(FilamentChangeNotificationService::class)->withoutModelChangeNotifications(function (): void {
            $student = $this->record;

            // Buat/cari akun orang tua berdasarkan no WA
            $parentName  = $this->data['parent_name'] ?? null;
            $parentPhone = $this->data['parent_phone'] ?? null;

            if ($parentName || $parentPhone) {
                $parentEmail = 'ortu_' . preg_replace('/\D/', '', $parentPhone ?? $parentName) . '@internal.app';

                $parent = User::firstOrCreate(
                    ['phone' => $parentPhone, 'role' => 'parent'],
                    [
                        'name'      => $parentName,
                        'email'     => $parentEmail,
                        'password'  => 'ortu123456',
                        'role'      => 'parent',
                        'school_id' => $student->school_id,
                        'status'    => 'active',
                    ]
                );
                $parent->update(['name' => $parentName]);

                // Hubungkan orang tua ke siswa
                $student->update([
                    'parent_user_id' => $parent->id,
                    'parent_phone' => $parentPhone,
                ]);
            }

            // Buat akun login siswa (untuk Mobile)
            $email    = $this->data['email'] ?? $student->nis;
            $password = $this->data['password'] ?? '12345678';

            User::create([
                'name'      => $student->name,
                'email'     => $email,
                'phone'     => null,
                'password'  => $password,
                'role'      => 'student',
                'school_id' => $student->school_id,
                'status'    => 'active',
            ]);
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
