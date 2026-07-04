<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Memeriksa apakah user yang login memiliki role yang diizinkan.
     *
     * Penggunaan: ->middleware('role:admin,super_admin')
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Silakan login terlebih dahulu.',
            ], 401);
        }

        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak. Role Anda (' . $user->role . ') tidak diizinkan untuk mengakses endpoint ini.',
        ], 403);
    }
}
