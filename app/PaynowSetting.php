<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaynowSetting extends Model
{
    protected $fillable = [
        'paynow_id',
        'paynow_key',
        'is_active',
        'environment',
        'return_url',
        'result_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active Paynow configuration.
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Get Paynow ID for use in transactions.
     */
    public static function getPaynowId()
    {
        $setting = self::getActive();
        return $setting ? $setting->paynow_id : env('PAYNOW_ID');
    }

    /**
     * Get Paynow Key for use in transactions.
     */
    public static function getPaynowKey()
    {
        $setting = self::getActive();
        return $setting ? $setting->paynow_key : env('PAYNOW_KEY');
    }

    /**
     * Check if using production environment.
     */
    public static function isProduction()
    {
        $setting = self::getActive();
        return $setting ? $setting->environment === 'production' : false;
    }

    /**
     * Get return URL.
     */
    public static function getReturnUrl()
    {
        $setting = self::getActive();
        return $setting && $setting->return_url ? $setting->return_url : url('/payments/return');
    }

    /**
     * Get result URL.
     */
    public static function getResultUrl()
    {
        $setting = self::getActive();
        return $setting && $setting->result_url ? $setting->result_url : url('/payments/result');
    }
}
