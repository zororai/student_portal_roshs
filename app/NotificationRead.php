<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationRead extends Model
{
    protected $fillable = [
        'notification_id',
        'user_id',
        'read_at'
    ];

    protected $dates = ['read_at'];

    public function notification()
    {
        return $this->belongsTo(SchoolNotification::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
