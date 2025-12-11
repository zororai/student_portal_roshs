<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Hash;

class CheckDefaultPassword
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
        // Only check for authenticated users with Student role
        if (auth()->check() && auth()->user()->hasRole('Student')) {
            // Check if user is still using default password
            if (Hash::check('12345678', auth()->user()->password)) {
                // Allow access to password change routes and logout
                if (!$request->is('student/change-password') &&
                    !$request->is('student/update-password') &&
                    !$request->is('logout')) {
                    return redirect()->route('student.change-password');
                }
            }
        }

        return $next($request);
    }
}
