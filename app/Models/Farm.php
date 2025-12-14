<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'size',
        'description',
        'user_id',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'size' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user that owns the farm.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the devices for the farm.
     */
    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Get the soil data for the farm.
     */
    public function soilData()
    {
        return $this->hasMany(SoilData::class);
    }

    /**
     * Get the crop histories for the farm.
     */
    public function cropHistories()
    {
        return $this->hasMany(CropHistory::class);
    }
}
