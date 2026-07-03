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

        $device = $data['device_name'] ?? 'default';
        $token = $user->createToken($device)->plainTextToken;

        return [
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone,
                'role'      => $user->role,
                'status'    => $user->status,
                'school_id' => $user->school_id,
            ],
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