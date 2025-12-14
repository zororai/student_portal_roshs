<?php

namespace App\Traits;

use App\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::audit('create', $model);
        });

        static::updated(function ($model) {
            self::audit('update', $model, $model->getOriginal(), $model->getAttributes());
        });

        static::deleted(function ($model) {
            self::audit('delete', $model, $model->getOriginal());
        });
    }

    protected static function audit($action, $model, $oldValues = null, $newValues = null)
    {
        $user = Auth::user();
        $modelName = class_basename($model);
        
        $descriptions = [
            'create' => "Created new {$modelName}",
            'update' => "Updated {$modelName}",
            'delete' => "Deleted {$modelName}",
        ];

        // Add identifier if available
        $identifier = '';
        if (isset($model->name)) {
            $identifier = ": {$model->name}";
        } elseif (isset($model->title)) {
            $identifier = ": {$model->title}";
        } elseif (isset($model->id)) {
            $identifier = " #{$model->id}";
        }

        AuditTrail::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System',
            'user_role' => $user ? ($user->roles->first()->name ?? 'Unknown') : 'System',
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $descriptions[$action] . $identifier,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
