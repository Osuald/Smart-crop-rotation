<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'soil_data_id',
        'farm_id',
        'crop_type',
        'season',
        'planted_date',
        'harvest_date',
        'yield_amount',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'planted_date' => 'date',
        'harvest_date' => 'date',
        'yield_amount' => 'decimal:2',
    ];

    /**
     * Get the soil data that owns the crop history.
     */
    public function soilData()
    {
        return $this->belongsTo(SoilData::class);
    }

    /**
     * Get the farm that owns the crop history.
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the user who created the crop history.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
