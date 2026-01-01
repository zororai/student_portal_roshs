<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolNotification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'recipient_type',
        'class_id',
        'recipient_id',
        'sent_by',
        'priority'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function reads()
    {
        return $this->hasMany(NotificationRead::class, 'notification_id');
    }

    public function getPriorityBadgeAttribute()
    {
        switch ($this->priority) {
            case 'urgent':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Urgent</span>';
            case 'high':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">High</span>';
            case 'normal':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Normal</span>';
            case 'low':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Low</span>';
            default:
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Normal</span>';
        }
    }

    public function getRecipientLabelAttribute()
    {
        switch ($this->recipient_type) {
            case 'all':
                return 'Whole School';
            case 'teachers':
                return 'All Teachers';
            case 'students':
                return 'All Students';
            case 'parents':
                return 'All Parents';
            case 'class':
                return $this->class ? $this->class->class_name : 'Class';
            case 'individual':
                return $this->recipient ? $this->recipient->name : 'Individual';
            default:
                return 'Unknown';
        }
    }
}
