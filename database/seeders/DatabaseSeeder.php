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
     */
    public function run(): void
    {
        // Jalankan RoleAndPermissionSeeder dulu agar role tersedia
        $this->call(RoleAndPermissionSeeder::class);

        // Buat Super Admin default jika belum ada
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

        // Pastikan role Spatie di-assign ke admin ini
        $admin->syncRoles(['super_admin']);

        // Seed data dummy lengkap (sekolah, kelas, guru, siswa, jadwal, dll.)
        $this->call(DummyDataSeeder::class);
    }
}
