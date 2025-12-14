<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoilData extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'farm_id',
        'ph',
        'moisture',
        'temperature',
        'nitrogen',
        'phosphorus',
        'potassium',
        'soil_health_score',
        'notes',
        'is_manual',
        'created_by',
    ];

    protected $casts = [
        'ph' => 'decimal:1',
        'moisture' => 'decimal:2',
        'temperature' => 'decimal:2',
        'nitrogen' => 'decimal:2',
        'phosphorus' => 'decimal:2',
        'potassium' => 'decimal:2',
        'soil_health_score' => 'decimal:2',
        'is_manual' => 'boolean',
    ];

    /**
     * Get the device that owns the soil data.
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the farm that owns the soil data.
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the user who created the soil data.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get crop histories associated with this soil data
     */
    public function cropHistories()
    {
        return $this->hasMany(CropHistory::class);
    }

    /**
     * Scope for manual entries
     */
    public function scopeManual($query)
    {
        return $query->where('is_manual', true);
    }

    /**
     * Scope for automated entries
     */
    public function scopeAutomated($query)
    {
        return $query->where('is_manual', false);
    }

    /**
     * Scope for specific farm
     */
    public function scopeForFarm($query, $farmId)
    {
        return $query->where('farm_id', $farmId);
    }

    /**
     * Scope for specific device
     */
    public function scopeForDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    /**
     * Get health status based on score
     */
    public function getHealthStatusAttribute()
    {
        if ($this->soil_health_score >= 80) return 'excellent';
        if ($this->soil_health_score >= 60) return 'good';
        if ($this->soil_health_score >= 40) return 'fair';
        if ($this->soil_health_score >= 20) return 'poor';
        return 'very_poor';
    }

    /**
     * Get health color for UI
     */
    public function getHealthColorAttribute()
    {
        switch ($this->health_status) {
            case 'excellent':
                return 'success';
            case 'good':
                return 'info';
            case 'fair':
                return 'warning';
            case 'poor':
                return 'orange';
            case 'very_poor':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}
