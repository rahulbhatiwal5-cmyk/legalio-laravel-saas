<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
    
        if (Auth::check()) {
            if (Auth::user()->is_admin == 1) {
                return redirect('/admin-dashboard'); // Redirect admin
            } elseif (Auth::user()->is_admin == 2) {
                return redirect('/admin-dashboard'); // Redirect reviewer
            } elseif (Auth::user()->is_admin == 3) {
                return redirect('/admin-dashboard'); // Redirect support assistant
            } else {
                // return redirect('/user-dashboard'); // Redirect user
                return redirect('/account'); // Redirect user
            }
        }

        return $next($request);
    }
}
