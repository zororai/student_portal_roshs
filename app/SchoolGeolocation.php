<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolGeolocation extends Model
{
    protected $fillable = [
        'name',
        'shape_type',
        'coordinates',
        'center_lat',
        'center_lng',
        'radius',
        'is_active',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'coordinates' => 'array',
        'center_lat' => 'float',
        'center_lng' => 'float',
        'radius' => 'float',
        'is_active' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the active school boundary
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Check if a point is within the school boundary
     */
    public function containsPoint($lat, $lng)
    {
        if ($this->shape_type === 'circle') {
            return $this->isPointInCircle($lat, $lng);
        }
        
        return $this->isPointInPolygon($lat, $lng);
    }

    /**
     * Check if point is within circle boundary
     */
    private function isPointInCircle($lat, $lng)
    {
        if (!$this->center_lat || !$this->center_lng || !$this->radius) {
            return false;
        }

        $distance = $this->haversineDistance(
            $this->center_lat,
            $this->center_lng,
            $lat,
            $lng
        );

        return $distance <= $this->radius;
    }

    /**
     * Check if point is within polygon boundary using ray casting algorithm
     */
    private function isPointInPolygon($lat, $lng)
    {
        $coordinates = $this->coordinates;
        
        if (!$coordinates || count($coordinates) < 3) {
            return false;
        }

        $inside = false;
        $n = count($coordinates);

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $coordinates[$i]['lat'];
            $yi = $coordinates[$i]['lng'];
            $xj = $coordinates[$j]['lat'];
            $yj = $coordinates[$j]['lng'];

            if ((($yi > $lng) !== ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi)) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
