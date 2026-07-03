<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Untuk API request, return null agar tidak redirect
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        return $request->expectsJson() ? null : route('login');
    }
}