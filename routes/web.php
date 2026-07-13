<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/public-stats', function () {
    $schoolsCount = \App\Models\School::where('status', 'active')->count();
    $alumniCount = \App\Models\Alumni::count();
    $attendanceCount = \App\Models\StudentAttendance::count();

    return response()->json([
        'schools' => $schoolsCount,
        'alumni' => $alumniCount,
        'attendance' => $attendanceCount,
    ]);
});



Route::get('/storage/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    
    // Ini bagian pentingnya: Membuka kunci CORS untuk Flutter Web
    $response->header("Access-Control-Allow-Origin", "*");
    $response->header("Access-Control-Allow-Methods", "GET, OPTIONS");
    $response->header("Access-Control-Allow-Headers", "Origin, Content-Type, Accept, Authorization, X-Request-With");
    
    return $response;
})->where('filename', '.*');

Route::post('/admin/device-token', function (Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required|string',
    ]);

    if (!auth()->check()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $fcmToken = \App\Models\FcmToken::updateOrCreate(
        ['token' => $request->token],
        [
            'user_id' => auth()->id(),
            'device_type' => 'web',
        ]
    );

    return response()->json([
        'success' => true,
        'message' => 'Web FCM token registered successfully.',
        'data' => $fcmToken
    ]);
})->middleware(['web', 'auth']);

Route::post('/webpush/subscribe', [\App\Http\Controllers\WebPushController::class, 'subscribe'])
    ->middleware(['web', 'auth']);

// Redirect route for default Filament login page
Route::redirect('/login', '/admin/login')->name('login');



// Google Web Login - Redirect ke Google OAuth 2.0 (tanpa Firebase)
Route::get('/admin/login/google/redirect', function () {
    $clientId = env('WEB_CLIENT_ID');
    $redirectUri = url('/admin/login/google/callback');

    $params = http_build_query([
        'client_id'     => $clientId,
        'redirect_uri'  => $redirectUri,
        'response_type' => 'code',
        'scope'         => 'openid email profile',
        'access_type'   => 'online',
        'prompt'        => 'select_account',
    ]);

    return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
})->middleware(['web'])->name('google.redirect');

// Google Web Login - Callback dari Google OAuth 2.0
Route::get('/admin/login/google/callback', function (\Illuminate\Http\Request $request) {
    $code = $request->input('code');
    $error = $request->input('error');

    if ($error || !$code) {
        return redirect('/admin/login')->withErrors([
            'google' => 'Login Google dibatalkan atau gagal. Silakan coba lagi.'
        ]);
    }

    try {
        $clientId     = env('WEB_CLIENT_ID');
        $clientSecret = env('WEB_CLIENT_SECRET');
        $redirectUri  = url('/admin/login/google/callback');

        // Tukarkan authorization code dengan access token
        $tokenResponse = \Illuminate\Support\Facades\Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code',
        ]);

        if ($tokenResponse->failed()) {
            return redirect('/admin/login')->withErrors([
                'google' => 'Gagal mendapatkan token dari Google. Periksa konfigurasi WEB_CLIENT_SECRET.'
            ]);
        }

        $idToken = $tokenResponse->json('id_token');

        // Verifikasi ID Token dengan Google API
        $verify = \Illuminate\Support\Facades\Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if ($verify->failed()) {
            return redirect('/admin/login')->withErrors([
                'google' => 'Token Google tidak valid atau telah kedaluwarsa.'
            ]);
        }

        $payload  = $verify->json();
        $email    = $payload['email'] ?? null;
        $googleId = $payload['sub'] ?? null;

        if (!$email) {
            return redirect('/admin/login')->withErrors([
                'google' => 'Gagal mengambil alamat email dari akun Google Anda.'
            ]);
        }

        // Cari user berdasarkan google_id atau email
        $user = \App\Models\User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            return redirect('/admin/login')->withErrors([
                'google' => 'Email Google Anda (' . $email . ') tidak terdaftar di sistem. Silakan hubungi Admin Sekolah.'
            ]);
        }

        // Hubungkan google_id dan avatar_url jika diperlukan
        $avatarUrl = $payload['picture'] ?? null;
        $updateData = [];
        if (empty($user->google_id)) {
            $updateData['google_id'] = $googleId;
        }
        if ($avatarUrl && $user->avatar_url !== $avatarUrl) {
            $updateData['avatar_url'] = $avatarUrl;
        }
        if (!empty($updateData)) {
            $user->update($updateData);
        }

        // Verifikasi status user aktif
        if ($user->status !== 'active') {
            return redirect('/admin/login')->withErrors([
                'google' => 'Akun Anda sedang ditangguhkan atau tidak aktif.'
            ]);
        }

        // Verifikasi sekolah aktif
        if (!$user->isSchoolActive()) {
            return redirect('/admin/login')->withErrors([
                'google' => 'Sekolah Anda sedang tidak aktif.'
            ]);
        }

        // Login menggunakan web guard Laravel
        \Illuminate\Support\Facades\Auth::login($user, true);

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        return redirect('/admin');

    } catch (\Exception $e) {
        return redirect('/admin/login')->withErrors([
            'google' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ]);
    }
})->middleware(['web'])->name('google.callback');



