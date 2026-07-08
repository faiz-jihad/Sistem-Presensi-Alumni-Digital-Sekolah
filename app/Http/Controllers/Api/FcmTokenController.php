<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterFcmTokenRequest;
use App\Models\FcmToken;
use Illuminate\Http\JsonResponse;

class FcmTokenController extends Controller
{
    /**
     * Daftarkan / perbarui FCM token perangkat pengguna.
     */
    public function register(RegisterFcmTokenRequest $request): JsonResponse
    {
        $user = $request->user();

        $fcmToken = FcmToken::updateOrCreate(
            ['token' => $request->validated('token')],
            [
                'user_id' => $user->id,
                'device_type' => $request->validated('device_type'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Token perangkat berhasil didaftarkan.',
            'data' => $fcmToken,
        ]);
    }
}
