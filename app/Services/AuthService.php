<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Proses login user
     */
    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Email atau password salah.', 401);
        }

        if ($user->status !== 'active') {
            throw new \Exception('Akun tidak aktif. Hubungi admin.', 403);
        }

        if (!in_array($user->role, ['teacher', 'alumni', 'student', 'parent'], true)) {
            throw new \Exception('Akses ditolak. Hanya guru, alumni, siswa, dan orangtua yang dapat mengakses aplikasi mobile.', 403);
        }

        $user->loadMissing(['teacher', 'alumni']);

        $device = $data['device_name'] ?? 'default';
        $token = $user->createToken($device)->plainTextToken;
        $phone = $user->phone ?: $user->teacher?->phone ?: $user->alumni?->phone;

        $userData = [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'phone'     => $phone,
            'role'      => $user->role,
            'status'    => $user->status,
            'school_id' => $user->school_id,
        ];

        if ($user->role === 'alumni') {
            $alumni = \App\Models\Alumni::where('user_id', $user->id)->first();
            $userData['verification_status'] = $alumni ? $alumni->verification_status : null;
        }

        return [
            'user' => $userData,
            'token'      => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Proses logout user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Login user (alias untuk konsistensi)
     */
    public function loginApi(array $data): array
    {
        return $this->login($data);
    }
}
