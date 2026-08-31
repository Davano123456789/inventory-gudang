<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->must_change_password) {
            // Prevent infinite loop if already on the password change routes
            if ($request->routeIs('password.force-change*') || $request->routeIs('logout')) {
                return $next($request);
            }
            
            return redirect()->route('password.force-change.show');
        }

        return $next($request);
    }
}
