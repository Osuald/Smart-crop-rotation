<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoilRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'soil_data_id',
        'user_id',
        'recommended_crop',
        'recommendation_details',
        'fertilizer_recommendations',
        'irrigation_schedule',
        'confidence_score',
        'season',
        'priority',
        'is_applied',
        'applied_at'
    ];

    protected $casts = [
        'fertilizer_recommendations' => 'array',
        'irrigation_schedule' => 'array',
        'confidence_score' => 'decimal:2',
        'is_applied' => 'boolean',
        'applied_at' => 'datetime'
    ];

    /**
     * Get the soil data this recommendation is based on
     */
    public function soilData()
    {
        return $this->belongsTo(SoilData::class);
    }

    /**
     * Get the user this recommendation is for
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
