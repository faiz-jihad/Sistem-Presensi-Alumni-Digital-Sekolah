<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageFeature
{
    /**
     * Handle an incoming request.
     * Memeriksa apakah sekolah user memiliki fitur paket langganan yang diizinkan.
     *
     * Penggunaan: ->middleware('feature:has_presensi')
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
            ], 401);
        }

        if ($user->hasFeature($feature)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Sekolah Anda tidak berlangganan paket yang mendukung fitur ini.',
        ], 403);
    }
}
