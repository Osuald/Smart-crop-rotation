<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_serial_number',
        'device_name',
        'device_type',
        'user_id',
        'installation_location',
        'farm_upi',
        'status',
        'latitude',
        'longitude',
        'firmware_version',
        'battery_level',
        'sensor_types',
        'notes',
        'installed_at',
        'last_communication'
    ];

    protected $casts = [
        'sensor_types' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'battery_level' => 'integer',
        'installed_at' => 'datetime',
        'last_communication' => 'datetime'
    ];

    protected $dates = [
        'installed_at',
        'last_communication'
    ];

    /**
     * Get the user that owns the device
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if device is online (communicated within last 30 minutes)
     */
    public function isOnline()
    {
        if (!$this->last_communication) {
            return false;
        }

        return $this->last_communication->gt(now()->subMinutes(30));
    }

    /**
     * Get battery status color
     */
    public function getBatteryStatusAttribute()
    {
        if ($this->battery_level === null) {
            return 'secondary';
        }

        if ($this->battery_level >= 70) {
            return 'success';
        } elseif ($this->battery_level >= 30) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    /**
     * Scope for active devices
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for online devices
     */
    public function scopeOnline($query)
    {
        return $query->where('last_communication', '>', now()->subMinutes(30));
    }
}
