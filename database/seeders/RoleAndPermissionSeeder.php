<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // School Management
            'manage_school',
            'view_school',
            'create_school',
            'update_school',
            'delete_school',
            
            // User Management
            'manage_users',
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            
            // Student Management
            'manage_students',
            'view_students',
            'create_students',
            'update_students',
            'delete_students',
            
            // Alumni Management
            'manage_alumni',
            'view_alumni',
            'create_alumni',
            'update_alumni',
            'delete_alumni',
            
            // Attendance
            'manage_attendance',
            'view_attendance',
            'create_attendance',
            'update_attendance',
            
            // Report
            'view_reports',
            'generate_reports',
            
            // Profile
            'update_profile',
            'view_profile',
            
            // Grades
            'view_grades',
            'manage_grades',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $rolePermissions = [
            'super_admin' => Permission::all(),
            'admin' => [
                'manage_school',
                'view_school',
                'create_school',
                'update_school',
                'manage_users',
                'view_users',
                'create_users',
                'update_users',
                'manage_students',
                'view_students',
                'create_students',
                'update_students',
                'manage_alumni',
                'view_alumni',
                'create_alumni',
                'update_alumni',
                'manage_attendance',
                'view_attendance',
                'create_attendance',
                'update_attendance',
                'view_reports',
                'generate_reports',
            ],
            'teacher' => [
                'view_students',
                'view_attendance',
                'create_attendance',
                'update_attendance',
                'view_reports',
                'view_profile',
                'update_profile',
            ],
            'student' => [
                'view_attendance',
                'view_grades',
                'view_profile',
                'update_profile',
            ],
            'alumni' => [
                'view_alumni',
                'view_profile',
                'update_profile',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionsList) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ], [
                'display_name' => ucwords(str_replace('_', ' ', $roleName)),
                'description' => "Role: " . ucwords(str_replace('_', ' ', $roleName))
            ]);

            $role->syncPermissions($permissionsList);
        }

        // Assign roles to existing users based on role column
        $users = User::whereNotNull('role')->get();
        foreach ($users as $user) {
            $user->syncRoles([$user->role]);
        }

        $this->command->info('Roles and permissions seeded successfully!');
    }
}