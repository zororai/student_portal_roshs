<?php

namespace App\Listeners;

use App\AuditTrail;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

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
        AuditTrail::create([
            'user_id' => $event->user->id,
            'user_name' => $event->user->name,
            'user_role' => $event->user->roles->first()->name ?? 'Unknown',
            'action' => 'login',
            'description' => 'User logged into the system',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
