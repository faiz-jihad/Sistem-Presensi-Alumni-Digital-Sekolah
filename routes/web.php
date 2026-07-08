<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('welcome');
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

