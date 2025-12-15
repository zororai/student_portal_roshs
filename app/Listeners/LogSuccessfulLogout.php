<?php

namespace App\Listeners;

use App\AuditTrail;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            if ($event->user) {
                $role = 'Unknown';
                if ($event->user->roles && $event->user->roles->count() > 0) {
                    $role = $event->user->roles->first()->name;
                }

                AuditTrail::create([
                    'user_id' => $event->user->id,
                    'user_name' => $event->user->name,
                    'user_role' => $role,
                    'action' => 'logout',
                    'description' => 'User logged out of the system',
                    'ip_address' => Request::ip(),
                    'user_agent' => Request::userAgent(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log logout audit: ' . $e->getMessage());
        }
    }
}
