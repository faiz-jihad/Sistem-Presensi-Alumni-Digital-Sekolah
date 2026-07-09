<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class GoogleAuthController extends Controller
{
    /**
     * Authenticate user using Google ID Token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_token' => ['required', 'string'],
        ], [
            'id_token.required' => 'ID Token Google wajib disertakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $idToken = $request->input('id_token');

        try {
            // Verify ID Token with Google API
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google tidak valid atau telah kedaluwarsa.',
                ], 400);
            }

            $payload = $response->json();
            $email = $payload['email'] ?? null;
            $googleId = $payload['sub'] ?? null;

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil alamat email dari akun Google Anda.',
                ], 400);
            }

            // Find user by google_id or email
            $user = User::where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email Google Anda (' . $email . ') tidak terdaftar di sistem. Silakan hubungi Admin Sekolah.',
                ], 404);
            }

            // Link google_id if not linked yet
            if (empty($user->google_id)) {
                $user->update(['google_id' => $googleId]);
            }

            // Verify if user is active
            if ($user->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda sedang ditangguhkan atau tidak aktif.',
                ], 403);
            }

            // Check if school is active
            if (!$user->isSchoolActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sekolah Anda sedang tidak aktif.',
                ], 403);
            }

            // Generate Sanctum token
            $token = $user->createToken('google-auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login Google berhasil.',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'school_id' => $user->school_id,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses login Google: ' . $e->getMessage(),
            ], 500);
        }
    }
}
