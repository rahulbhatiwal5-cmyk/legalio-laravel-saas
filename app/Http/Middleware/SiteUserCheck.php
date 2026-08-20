<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;

class SiteUserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            if(Auth::user()->is_admin == 1){
                // return redirect('/admin-dashboard')->with('error','Logout from admin account to visit site');
                return $next($request);
            }else{
                return $next($request);
            }
        }else{
            return $next($request);
        }
    }
}
