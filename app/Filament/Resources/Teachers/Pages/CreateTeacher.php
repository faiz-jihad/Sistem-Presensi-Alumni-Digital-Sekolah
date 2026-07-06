<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            // 1. Buat User baru dengan role 'teacher'
            $user = \App\Models\User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => \Illuminate\Support\Facades\Hash::make($data['password']),
                'role'      => 'teacher',
                'school_id' => $data['school_id'],
                'status'    => 'active',
            ]);

            // Sync Spatie role
            $user->syncRoles(['teacher']);

            // 2. Hubungkan ke data guru
            $data['user_id'] = $user->id;

            // 3. Bersihkan fields email dan password agar tidak error saat create Teacher
            unset($data['email'], $data['password']);

            return $data;
        });
    }
}
