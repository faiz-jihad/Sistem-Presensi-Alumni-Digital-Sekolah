<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\User;
use App\Services\FilamentChangeNotificationService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    public function getTitle(): string
    {
        return 'Edit Siswa';
    }

    public function getBreadcrumb(): string
    {
        return 'Edit';
    }

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Simpan');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Kembali');
    }    

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Muat email login siswa dari tabel users
        $studentUser = User::where('role', 'student')
            ->where(function ($q) use ($data) {
                $q->where('email', $data['nis'] ?? '')
                    ->orWhere('name', $data['name'] ?? '');
            })->first();

        $data['email'] = $studentUser?->email ?? ($data['nis'] ?? '');

        // Muat data orang tua dari relasi parent_user_id
        if (!empty($data['parent_user_id'])) {
            $parent = User::find($data['parent_user_id']);
            $data['parent_name']  = $parent?->name;
            $data['parent_phone'] = $parent?->phone;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        app(FilamentChangeNotificationService::class)->withoutModelChangeNotifications(function (): void {
            $student = $this->record;

            // Buat/update akun orang tua berdasarkan nama & no WA
            $parentName  = $this->data['parent_name'] ?? null;
            $parentPhone = $this->data['parent_phone'] ?? null;

            if ($parentName || $parentPhone) {
                // Gunakan phone sebagai basis email unik orang tua
                $parentEmail = 'ortu_' . preg_replace('/\D/', '', $parentPhone ?? $parentName) . '@internal.app';

                if ($student->parent_user_id) {
                    // Update parent yang sudah ada
                    User::where('id', $student->parent_user_id)->update([
                        'name'  => $parentName,
                        'phone' => $parentPhone,
                    ]);
                    $student->update(['parent_phone' => $parentPhone]);
                } else {
                    // Buat parent baru dan hubungkan ke siswa
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
                    $student->update([
                        'parent_user_id' => $parent->id,
                        'parent_phone' => $parentPhone,
                    ]);
                }
            }

            // Sinkronisasi akun login siswa
            $originalNis = $student->getOriginal('nis') ?? $student->nis;
            $originalName = $student->getOriginal('name') ?? $student->name;

            $studentUser = User::where('role', 'student')
                ->where(function ($q) use ($originalNis, $originalName) {
                    $q->where('email', $originalNis)
                        ->orWhere('name', $originalName);
                })->first();

            $email    = $this->data['email'] ?? $student->nis;
            $password = $this->data['password'] ?? null;

            $userData = [
                'name'      => $student->name,
                'email'     => $email,
                'school_id' => $student->school_id,
            ];

            if ($password) {
                $userData['password'] = $password;
            }

            if ($studentUser) {
                $studentUser->update($userData);
            } else {
                User::create(array_merge($userData, [
                    'password' => $password ?? '12345678',
                    'role'     => 'student',
                    'status'   => 'active',
                ]));
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
