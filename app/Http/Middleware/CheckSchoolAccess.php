<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSchoolAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->role == 'super_admin') {
            return $next($request);
        }

        if ($user->school?->status == 'inactive') {

            $dashboard = filament()->getCurrentPanel()->getUrl();

            if (!$request->is('admin')) {
                return redirect($dashboard);
            }
        }

        return $next($request);
    }
}
