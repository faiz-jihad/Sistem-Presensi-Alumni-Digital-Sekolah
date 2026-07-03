<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
                            {--email=admin@simpad.test : Email admin}
                            {--name=Super Admin : Nama admin}
                            {--password=password : Password admin}';

    protected $description = 'Buat atau reset admin user untuk login Filament';

    public function handle(): int
    {
        $email    = $this->option('email');
        $name     = $this->option('name');
        $password = $this->option('password');

        // Pastikan role super_admin ada di Spatie
        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['display_name' => 'Super Admin', 'description' => 'Akses penuh ke sistem']
        );

        // Buat atau update user
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'      => $name,
                'password'  => Hash::make($password),
                'role'      => 'super_admin',
                'status'    => 'active',
                'school_id' => null,
            ]
        );

        // Assign Spatie role
        $user->syncRoles(['super_admin']);

        $this->info('✅ Admin user berhasil dibuat/diperbarui!');
        $this->table(
            ['Field', 'Value'],
            [
                ['Email',    $user->email],
                ['Password', $password],
                ['Role',     $user->role],
                ['Status',   $user->status],
                ['URL Login', url('/admin/login')],
            ]
        );

        return Command::SUCCESS;
    }
}
