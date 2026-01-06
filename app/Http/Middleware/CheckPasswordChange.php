<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->must_change_password) {
            // Allow access to password change routes and logout
            $allowedRoutes = [
                'user.force-change-password',
                'user.force-change-password.update',
                'logout',
                'logout.get'
            ];
            
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('user.force-change-password')
                    ->with('warning', 'You must change your password before continuing.');
            }
        }
        
        return $next($request);
    }
}
