<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class UserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->is_admin == 0) {
                return $next($request); // Allow user to access
            } else {
                return redirect('/admin-dashboard'); // Admins should not access user routes
            }
        }

        return redirect()->route('login.user');

    }
}

