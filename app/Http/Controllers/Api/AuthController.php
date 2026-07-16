<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());
            return $this->success($result, 'Login berhasil', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return $this->error('Email tidak terdaftar.', 404);
        }

        $rateKey = $this->forgotPasswordRateKey($request, $email);
        if (Cache::has($rateKey)) {
            return $this->error('Tunggu 60 detik sebelum meminta kode OTP baru.', 429);
        }

        $otpCode = (string) random_int(100000, 999999);

        PasswordResetOtp::where('email', $email)
            ->where('is_used', false)
            ->update(['is_used' => true]);

        PasswordResetOtp::create([
            'email' => $email,
            'otp_code' => Hash::make($otpCode),
            'expires_at' => now()->addMinutes(10),
            'failed_attempts' => 0,
            'is_used' => false,
        ]);

        Mail::to($email)->send(new \App\Mail\ForgotPasswordMail($otpCode));

        Cache::put($rateKey, true, now()->addSeconds(60));

        return $this->success(null, 'Kode OTP sudah dikirim ke email Anda.');
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'digits:6'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $otp = PasswordResetOtp::where('email', $email)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otp) {
            return $this->error('Kode OTP tidak ditemukan. Silakan minta kode baru.', 404);
        }

        if ($otp->expires_at->isPast()) {
            $otp->update(['is_used' => true]);
            return $this->error('Kode OTP sudah kedaluwarsa, silakan minta kode baru.', 422);
        }

        if ($otp->failed_attempts >= 5) {
            $otp->update(['is_used' => true]);
            return $this->error('Kode OTP sudah terlalu sering salah. Silakan minta kode baru.', 422);
        }

        if (!Hash::check($validated['otp_code'], $otp->otp_code)) {
            $failedAttempts = $otp->failed_attempts + 1;
            $otp->update([
                'failed_attempts' => $failedAttempts,
                'is_used' => $failedAttempts >= 5,
            ]);

            if ($failedAttempts >= 5) {
                return $this->error('Kode OTP sudah terlalu sering salah. Silakan minta kode baru.', 422);
            }

            return $this->error('Kode OTP salah.', 422);
        }

        $resetToken = Str::random(64);
        Cache::put($this->passwordResetTokenKey($resetToken), [
            'email' => $email,
            'otp_id' => $otp->id,
        ], now()->addMinutes(5));

        return $this->success([
            'reset_token' => $resetToken,
        ], 'Kode OTP valid.');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'reset_token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $tokenKey = $this->passwordResetTokenKey($validated['reset_token']);
        $tokenData = Cache::get($tokenKey);

        if (!is_array($tokenData) || ($tokenData['email'] ?? null) !== $email) {
            return $this->error('Token reset password tidak valid atau sudah kedaluwarsa.', 422);
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$user) {
            return $this->error('Email tidak terdaftar.', 404);
        }

        $otp = PasswordResetOtp::where('id', $tokenData['otp_id'] ?? null)
            ->where('email', $email)
            ->where('is_used', false)
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            Cache::forget($tokenKey);
            return $this->error('Kode OTP sudah kedaluwarsa, silakan minta kode baru.', 422);
        }

        DB::transaction(function () use ($user, $otp, $validated) {
            $user->forceFill([
                'password' => Hash::make($validated['password']),
            ])->save();

            $user->tokens()->delete();

            $otp->update(['is_used' => true]);
        });

        Cache::forget($tokenKey);

        return $this->success(null, 'Password berhasil diperbarui. Silakan login dengan password baru.');
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Hapus token FCM jika disertakan dalam request
            $fcmToken = $request->input('fcm_token');
            if ($fcmToken) {
                $user->fcmTokens()->where('token', $fcmToken)->delete();
            }

            $this->authService->logout($user);
            return $this->success(null, 'Logout berhasil', 200);
        } catch (\Exception $e) {
            return $this->error('Terjadi kesalahan saat logout.', 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['school', 'teacher', 'alumni']);
        $phone = $user->phone ?: $user->teacher?->phone ?: $user->alumni?->phone;

        $userData = [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'phone'       => $phone,
            'role'        => $user->role,
            'status'      => $user->status,
            'school_id'   => $user->school_id,
            'school_name' => $user->school?->name,
            'created_at'  => $user->created_at->format('Y-m-d H:i:s'),
        ];

        if ($user->role === 'alumni') {
            $alumni = \App\Models\Alumni::where('user_id', $user->id)->first();
            $userData['verification_status'] = $alumni ? $alumni->verification_status : null;
        }

        return $this->success($userData, 'Data user berhasil diambil');
    }

    private function forgotPasswordRateKey(Request $request, string $email): string
    {
        return 'forgot-password-otp:' . sha1($email . '|' . $request->ip());
    }

    private function passwordResetTokenKey(string $token): string
    {
        return 'password-reset-token:' . hash('sha256', $token);
    }
}
