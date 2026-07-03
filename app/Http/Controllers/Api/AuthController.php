<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // PANGGIL METHOD login() BUKAN loginApi()
            $result = $this->authService->login($request->validated());

            return $this->success($result, 'Login berhasil', 200);

        } catch (\Exception $e) {
            return $this->error(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return $this->success(null, 'Logout berhasil', 200);

        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan saat logout.', 500);
        }
    }

    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('school');

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status,
            'school_id' => $user->school_id,
            'school_name' => $user->school?->name,
            'created_at' => $user->created_at->format('Y-m-d H:i:s'),
        ], 'Data user berhasil diambil', 200);
    }
}