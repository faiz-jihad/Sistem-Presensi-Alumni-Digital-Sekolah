<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Login user
     */
    public function login(array $data): array  // <-- NAMA METHOD: login
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
     * Logout user
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}