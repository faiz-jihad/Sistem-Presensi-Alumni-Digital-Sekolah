<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * ⚠️  Production-safe: hanya membuat role & super admin default.
     *     Untuk data dummy (dev/staging), jalankan:
     *     php artisan db:seed --class=DummyDataSeeder
     */
    public function run(): void
    {
        // 1. Buat role & permission Spatie
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Buat Super Admin default jika belum ada
        $admin = User::firstOrCreate(
            ['email' => 'admin@simpad.test'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'role'      => 'super_admin',
                'status'    => 'active',
                'school_id' => null,
            ]
        );

        // 3. Pastikan role Spatie di-assign
        $admin->syncRoles(['super_admin']);
    }
}

