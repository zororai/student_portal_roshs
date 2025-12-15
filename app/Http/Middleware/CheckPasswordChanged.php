<?php

namespace App\Http\Middleware;

use Closure;

class CheckPasswordChanged
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
        if (auth()->check() && !auth()->user()->password_changed) {
            // Allow access to password change route and logout
            $allowedRoutes = ['password.change', 'password.update', 'logout'];
            
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                return redirect()->route('password.change')
                    ->with('warning', 'You must change your default password before continuing.');
            }
        }

        return $next($request);
    }
}
