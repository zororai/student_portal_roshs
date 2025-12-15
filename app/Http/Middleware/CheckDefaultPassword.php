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
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $defaultPassword = '12345678';

        // Check for Student role
        if ($user->hasRole('Student')) {
            if (Hash::check($defaultPassword, $user->password)) {
                if (!$request->is('student/change-password') &&
                    !$request->is('student/update-password') &&
                    !$request->is('logout')) {
                    return redirect()->route('student.change-password');
                }
            }
        }

        // Check for Teacher role
        if ($user->hasRole('Teacher')) {
            if (Hash::check($defaultPassword, $user->password)) {
                if (!$request->is('teacher/change-password') &&
                    !$request->is('teacher/update-password') &&
                    !$request->is('logout')) {
                    return redirect()->route('teacher.change-password')
                        ->with('warning', 'Please change your default password to continue.');
                }
            }
        }

        return $next($request);
    }
}
