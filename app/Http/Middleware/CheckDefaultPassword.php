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

        // Check for Teacher role - only redirect if BOTH conditions are true:
        // 1. Password is still default (12345678)
        // 2. Email is still placeholder (teacher_*@placeholder.co.zw)
        if ($user->hasRole('Teacher')) {
            $isDefaultPassword = Hash::check($defaultPassword, $user->password);
            $isPlaceholderEmail = str_contains($user->email, '@placeholder.co.zw');
            
            if ($isDefaultPassword && $isPlaceholderEmail) {
                if (!$request->is('teacher/change-password') &&
                    !$request->is('teacher/update-password') &&
                    !$request->is('logout')) {
                    return redirect()->route('teacher.change-password')
                        ->with('warning', 'Please complete your profile and change your default password to continue.');
                }
            }
        }

        // Check must_change_password flag for other users (Admin, etc.)
        if ($user->must_change_password) {
            if (!$request->is('user/force-change-password') &&
                !$request->is('logout')) {
                return redirect()->route('user.force-change-password')
                    ->with('warning', 'Please change your default password to continue.');
            }
        }

        return $next($request);
    }
}
