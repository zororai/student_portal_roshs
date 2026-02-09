<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetAssignmentHistory extends Model
{
    protected $fillable = [
        'asset_id',
        'from_type',
        'from_id',
        'to_type',
        'to_id',
        'assigned_by',
        'assigned_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function getFromNameAttribute()
    {
        return $this->resolveAssignmentName($this->from_type, $this->from_id);
    }

    public function getToNameAttribute()
    {
        return $this->resolveAssignmentName($this->to_type, $this->to_id);
    }

    protected function resolveAssignmentName($type, $id)
    {
        if (!$type || !$id) {
            return 'Unassigned';
        }

        switch ($type) {
            case 'user':
                $user = User::find($id);
                return $user ? $user->name : 'Unknown User';
            case 'teacher':
                $teacher = Teacher::find($id);
                return $teacher ? $teacher->name : 'Unknown Teacher';
            case 'student':
                $student = Student::find($id);
                return $student ? $student->name : 'Unknown Student';
            case 'class':
                $grade = Grade::find($id);
                return $grade ? $grade->name : 'Unknown Class';
            default:
                return 'Unknown';
        }
    }

    public function getActionDescriptionAttribute()
    {
        $from = $this->from_name;
        $to = $this->to_name;

        if ($from === 'Unassigned' && $to !== 'Unassigned') {
            return "Assigned to {$to}";
        } elseif ($from !== 'Unassigned' && $to === 'Unassigned') {
            return "Unassigned from {$from}";
        } else {
            return "Transferred from {$from} to {$to}";
        }
    }
}
