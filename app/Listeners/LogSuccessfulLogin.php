<?php

namespace App\Listeners;

use App\AuditTrail;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        try {
            $role = 'Unknown';
            if ($event->user->roles && $event->user->roles->count() > 0) {
                $role = $event->user->roles->first()->name;
            }

            AuditTrail::create([
                'user_id' => $event->user->id,
                'user_name' => $event->user->name,
                'user_role' => $role,
                'action' => 'login',
                'description' => 'User logged into the system',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log login audit: ' . $e->getMessage());
        }
    }
}
