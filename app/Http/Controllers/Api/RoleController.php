<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    /**
     * List semua role dari Spatie Permission + data dari kolom role
     */
    public function index(): JsonResponse
    {
        // Ambil dari Spatie Permission
        $roles = Role::with('permissions')->get();
        
        // Ambil role unik dari kolom 'role' di users
        $userRoles = User::select('role')
            ->distinct()
            ->whereNotNull('role')
            ->pluck('role')
            ->toArray();
        
        return $this->success([
            'spatie_roles' => $roles,
            'user_role_column' => $userRoles,
            'role_mapping' => $this->getRoleMapping()
        ], 'List role berhasil diambil');
    }

    /**
     * Detail role
     */
    public function show($id): JsonResponse
    {
        $role = Role::with('permissions')->findOrFail($id);
        return $this->success($role, 'Detail role berhasil diambil');
    }

    /**
     * Buat role baru
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name ?? $request->name,
            'description' => $request->description,
            'guard_name' => 'web',
        ]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return $this->success($role->load('permissions'), 'Role berhasil dibuat', 201);
    }

    /**
     * Update role
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'display_name' => 'nullable|string',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name ?? $request->name,
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->success($role->load('permissions'), 'Role berhasil diupdate');
    }

    /**
     * Hapus role
     */
    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);

        // Cek apakah role digunakan di kolom 'role' users
        $usedByUsers = User::where('role', $role->name)->count();
        if ($usedByUsers > 0) {
            return $this->error(
                "Role masih digunakan oleh {$usedByUsers} user di kolom role", 
                400
            );
        }

        // Cek apakah role digunakan di Spatie Permission
        if ($role->users()->count() > 0) {
            return $this->error('Role masih digunakan oleh user', 400);
        }

        $role->delete();
        return $this->success(null, 'Role berhasil dihapus');
    }

    /**
     * Sinkronisasi role dari kolom 'role' ke Spatie Permission
     */
    public function syncUserRoles(): JsonResponse
    {
        $users = User::whereNotNull('role')->get();
        $synced = 0;

        foreach ($users as $user) {
            // Pastikan role ada di Spatie Permission
            $role = Role::firstOrCreate([
                'name' => $user->role,
                'guard_name' => 'web'
            ], [
                'display_name' => ucwords(str_replace('_', ' ', $user->role)),
                'description' => "Role: " . ucwords(str_replace('_', ' ', $user->role))
            ]);

            // Assign role ke user
            $user->syncRoles([$user->role]);
            $synced++;
        }

        return $this->success([
            'synced' => $synced,
            'total_users' => $users->count()
        ], 'User roles berhasil disinkronisasi');
    }

    /**
     * Seed default roles dan permissions
     */
    public function seedDefault(): JsonResponse
    {
        // Daftar default roles
        $defaultRoles = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin Sekolah',
            'teacher' => 'Guru',
            'student' => 'Siswa',
            'alumni' => 'Alumni',
        ];

        $created = [];
        foreach ($defaultRoles as $name => $displayName) {
            $role = Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web'
            ], [
                'display_name' => $displayName,
                'description' => "Role: {$displayName}"
            ]);
            $created[] = $role;
        }

        return $this->success($created, 'Default roles berhasil dibuat');
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role): JsonResponse
    {
        $users = User::where('role', $role)->get();
        
        return $this->success($users, "Data user dengan role {$role} berhasil diambil");
    }

    /**
     * Update user role
     */
    public function updateUserRole(Request $request, $userId): JsonResponse
    {
        $request->validate([
            'role' => 'required|string|in:super_admin,admin,teacher,student,alumni'
        ]);

        $user = User::findOrFail($userId);
        $user->role = $request->role;
        $user->save();

        return $this->success($user, 'Role user berhasil diperbarui');
    }

    /**
     * Mapping role
     */
    private function getRoleMapping(): array
    {
        return [
            'super_admin' => [
                'display_name' => 'Super Admin',
                'description' => 'Akses penuh ke sistem',
                'filament_access' => true,
                'permissions' => ['*']
            ],
            'admin' => [
                'display_name' => 'Admin Sekolah',
                'description' => 'Admin sekolah',
                'filament_access' => true,
                'permissions' => [
                    'manage_school',
                    'manage_users',
                    'manage_students',
                    'manage_alumni',
                    'manage_attendance',
                    'view_reports'
                ]
            ],
            'teacher' => [
                'display_name' => 'Guru',
                'description' => 'Guru sekolah',
                'filament_access' => false,
                'permissions' => [
                    'view_students',
                    'manage_attendance',
                    'view_reports'
                ]
            ],
            'student' => [
                'display_name' => 'Siswa',
                'description' => 'Siswa aktif',
                'filament_access' => false,
                'permissions' => [
                    'view_attendance',
                    'view_grades'
                ]
            ],
            'alumni' => [
                'display_name' => 'Alumni',
                'description' => 'Lulusan sekolah',
                'filament_access' => false,
                'permissions' => [
                    'view_alumni',
                    'update_profile'
                ]
            ],
        ];
    }
}