<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageFeature
{
    /**
     * Handle an incoming request.
     * Sistem paket langganan telah dihapus — semua fitur diizinkan untuk semua sekolah.
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

        // Sistem paket langganan telah dihapus — semua fitur diizinkan
        return $next($request);
    }
}
