<?php

namespace App\Listeners;

use App\AuditTrail;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     *
     * @param Logout $event
     * @return void
     */
    public function handle(Logout $event)
    {
        if ($event->user) {
            AuditTrail::create([
                'user_id' => $event->user->id,
                'user_name' => $event->user->name,
                'user_role' => $event->user->roles->first()->name ?? 'Unknown',
                'action' => 'logout',
                'description' => 'User logged out of the system',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}
