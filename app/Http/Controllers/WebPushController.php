<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebPushController extends Controller
{
    /**
     * Store the user's web push subscription.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.auth' => 'required',
            'keys.p256dh' => 'required',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        try {
            $endpoint = $request->input('endpoint');
            $key = $request->input('keys.p256dh');
            $token = $request->input('keys.auth');
            $contentEncoding = $request->input('content_encoding', 'aesgcm');

            $user->updatePushSubscription($endpoint, $key, $token, $contentEncoding);

            return response()->json([
                'success' => true,
                'message' => 'Subscription saved successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save push subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save subscription: ' . $e->getMessage()
            ], 500);
        }
    }
}
