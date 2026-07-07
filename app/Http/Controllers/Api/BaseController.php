<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

class BaseController extends Controller
{
    use ApiResponse;

    /**
     * Check if the authenticated user's school has access to a specific feature.
     */
    protected function checkFeature(string $feature): void
    {
        $user = auth()->user();
        if ($user && !$user->hasFeature($feature)) {
            // Throw HttpResponseException to halt execution and return JSON response
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Sekolah Anda tidak berlangganan paket yang mendukung fitur ini.',
                ], 403)
            );
        }
    }
}