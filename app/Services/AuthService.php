<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Proses login user
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function login(array $data): array
    {
        // 1. Cari user berdasarkan email
        $user = User::where('email', $data['email'])->first();

        // 2. Validasi kredensial
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Email atau password salah.', 401);
        }

        // 3. Cek status user
        $this->checkUserStatus($user);

        // 4. Generate Sanctum token
        $deviceName = $data['device_name'] ?? 'default';
        $token = $user->createToken($deviceName, $this->getAbilities($user->role))->plainTextToken;

        // 5. Return data user + token
        return [
            'user' => $this->formatUserData($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Proses logout user
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Cek status user (active/inactive/suspended)
     *
     * @param User $user
     * @return void
     * @throws \Exception
     */
    private function checkUserStatus(User $user): void
    {
        if ($user->status === 'inactive') {
            throw new \Exception('Akun Anda tidak aktif. Hubungi admin.', 403);
        }

        if ($user->status === 'suspended') {
            throw new \Exception('Akun Anda ditangguhkan. Hubungi admin.', 403);
        }
    }

    /**
     * Get token abilities berdasarkan role
     *
     * @param string $role
     * @return array
     */
    private function getAbilities(string $role): array
    {
        return match ($role) {
            'super_admin' => ['*'],
            'admin' => ['school:manage', 'user:manage', 'presensi:manage', 'report:view'],
            'teacher' => ['presensi:input', 'presensi:view', 'student:view'],
            'student' => ['presensi:self', 'profile:edit'],
            'parent' => ['presensi:view', 'notification:receive'],
            'alumni' => ['profile:edit', 'alumni:access'],
            default => [],
        };
    }

    /**
     * Format data user untuk response
     *
     * @param User $user
     * @return array
     */
    private function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status,
            'school_id' => $user->school_id,
            'school_name' => $user->school?->name,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
        ];
    }
}