<?php

namespace App\Services;

use App\Models\SoilData;
use App\Models\SoilRecommendation;

class CropRecommendationService
{
    /**
     * Crop database with optimal growing conditions
     */
    private array $cropDatabase = [
        'maize' => [
            'name' => 'Maize (Corn)',
            'ph_range' => [6.0, 7.5],
            'ph_optimal' => [6.2, 6.8],
            'moisture_range' => [45, 70],
            'moisture_optimal' => [55, 65],
            'temp_range' => [18, 32],
            'temp_optimal' => [21, 27],
            'nitrogen_min' => 30,
            'phosphorus_min' => 20,
            'potassium_min' => 150,
            'season_preference' => ['A', 'B'],
            'growth_period' => '90-120 days',
            'yield_potential' => 'High',
            'market_value' => 'Good',
            'drought_tolerance' => 'Medium',
            'soil_type' => 'Well-drained, fertile soil',
            'follow_up_crops' => ['beans', 'groundnuts', 'vegetables']
        ],
        'beans' => [
            'name' => 'Common Beans',
            'ph_range' => [6.0, 7.5],
            'ph_optimal' => [6.2, 7.0],
            'moisture_range' => [40, 65],
            'moisture_optimal' => [50, 60],
            'temp_range' => [15, 28],
            'temp_optimal' => [18, 24],
            'nitrogen_min' => 15, // Legume, fixes nitrogen
            'phosphorus_min' => 25,
            'potassium_min' => 120,
            'season_preference' => ['A', 'B'],
            'growth_period' => '75-90 days',
            'yield_potential' => 'Medium-High',
            'market_value' => 'Excellent',
            'drought_tolerance' => 'Medium',
            'soil_type' => 'Well-drained, organic-rich',
            'follow_up_crops' => ['maize', 'vegetables', 'cereals']
        ],
        'irish_potatoes' => [
            'name' => 'Irish Potatoes',
            'ph_range' => [5.0, 6.5],
            'ph_optimal' => [5.5, 6.2],
            'moisture_range' => [60, 80],
            'moisture_optimal' => [65, 75],
            'temp_range' => [10, 20],
            'temp_optimal' => [13, 18],
            'nitrogen_min' => 25,
            'phosphorus_min' => 30,
            'potassium_min' => 200,
            'season_preference' => ['A'],
            'growth_period' => '90-120 days',
            'yield_potential' => 'High',
            'market_value' => 'Very Good',
            'drought_tolerance' => 'Low',
            'soil_type' => 'Cool, well-drained, loose soil',
            'follow_up_crops' => ['beans', 'vegetables', 'cereals']
        ],
        'sorghum' => [
            'name' => 'Sorghum',
            'ph_range' => [6.0, 8.5],
            'ph_optimal' => [6.5, 7.5],
            'moisture_range' => [25, 50],
            'moisture_optimal' => [30, 45],
            'temp_range' => [20, 35],
            'temp_optimal' => [25, 30],
            'nitrogen_min' => 20,
            'phosphorus_min' => 15,
            'potassium_min' => 100,
            'season_preference' => ['A', 'B'],
            'growth_period' => '100-130 days',
            'yield_potential' => 'Medium-High',
            'market_value' => 'Good',
            'drought_tolerance' => 'Very High',
            'soil_type' => 'Wide adaptation, drought-tolerant',
            'follow_up_crops' => ['legumes', 'beans', 'groundnuts']
        ],
        'rice' => [
            'name' => 'Rice',
            'ph_range' => [5.5, 7.0],
            'ph_optimal' => [6.0, 6.5],
            'moisture_range' => [80, 95],
            'moisture_optimal' => [85, 90],
            'temp_range' => [20, 30],
            'temp_optimal' => [23, 27],
            'nitrogen_min' => 35,
            'phosphorus_min' => 20,
            'potassium_min' => 150,
            'season_preference' => ['A', 'B'],
            'growth_period' => '120-150 days',
            'yield_potential' => 'Very High',
            'market_value' => 'Excellent',
            'drought_tolerance' => 'Very Low',
            'soil_type' => 'Flooded/waterlogged conditions',
            'follow_up_crops' => ['vegetables', 'legumes', 'upland_crops']
        ],
        'cassava' => [
            'name' => 'Cassava',
            'ph_range' => [5.5, 7.5],
            'ph_optimal' => [6.0, 6.8],
            'moisture_range' => [20, 40],
            'moisture_optimal' => [25, 35],
            'temp_range' => [20, 35],
            'temp_optimal' => [25, 30],
            'nitrogen_min' => 10,
            'phosphorus_min' => 8,
            'potassium_min' => 80,
            'season_preference' => ['A', 'B'],
            'growth_period' => '8-12 months',
            'yield_potential' => 'Very High',
            'market_value' => 'Good',
            'drought_tolerance' => 'Very High',
            'soil_type' => 'Sandy, well-drained, poor soils',
            'follow_up_crops' => ['legumes', 'cereals', 'vegetables']
        ],
        'tea' => [
            'name' => 'Tea',
            'ph_range' => [4.5, 6.0],
            'ph_optimal' => [5.0, 5.5],
            'moisture_range' => [40, 60],
            'moisture_optimal' => [45, 55],
            'temp_range' => [13, 25],
            'temp_optimal' => [16, 22],
            'nitrogen_min' => 40,
            'phosphorus_min' => 25,
            'potassium_min' => 200,
            'season_preference' => ['A', 'B'],
            'growth_period' => 'Perennial (3+ years)',
            'yield_potential' => 'Medium',
            'market_value' => 'Very High',
            'drought_tolerance' => 'Medium',
            'soil_type' => 'Acidic, well-drained, high altitude',
            'follow_up_crops' => ['maintain_plantation']
        ],
        'coffee' => [
            'name' => 'Coffee',
            'ph_range' => [6.0, 7.0],
            'ph_optimal' => [6.2, 6.8],
            'moisture_range' => [50, 70],
            'moisture_optimal' => [55, 65],
            'temp_range' => [15, 25],
            'temp_optimal' => [18, 22],
            'nitrogen_min' => 35,
            'phosphorus_min' => 30,
            'potassium_min' => 220,
            'season_preference' => ['A', 'B'],
            'growth_period' => 'Perennial (3+ years)',
            'yield_potential' => 'High',
            'market_value' => 'Very High',
            'drought_tolerance' => 'Medium',
            'soil_type' => 'Well-drained, volcanic soil',
            'follow_up_crops' => ['maintain_plantation']
        ],
        'tomatoes' => [
            'name' => 'Tomatoes',
            'ph_range' => [6.0, 7.5],
            'ph_optimal' => [6.2, 6.8],
            'moisture_range' => [55, 75],
            'moisture_optimal' => [60, 70],
            'temp_range' => [18, 28],
            'temp_optimal' => [21, 25],
            'nitrogen_min' => 40,
            'phosphorus_min' => 35,
            'potassium_min' => 200,
            'season_preference' => ['A', 'B'],
            'growth_period' => '90-120 days',
            'yield_potential' => 'High',
            'market_value' => 'Very Good',
            'drought_tolerance' => 'Low',
            'soil_type' => 'Rich, well-drained, organic soil',
            'follow_up_crops' => ['legumes', 'leafy_vegetables', 'root_crops']
        ],
        'sweet_potatoes' => [
            'name' => 'Sweet Potatoes',
            'ph_range' => [5.5, 7.0],
            'ph_optimal' => [6.0, 6.5],
            'moisture_range' => [35, 55],
            'moisture_optimal' => [40, 50],
            'temp_range' => [20, 30],
            'temp_optimal' => [23, 27],
            'nitrogen_min' => 15,
            'phosphorus_min' => 20,
            'potassium_min' => 180,
            'season_preference' => ['A', 'B'],
            'growth_period' => '90-120 days',
            'yield_potential' => 'High',
            'market_value' => 'Good',
            'drought_tolerance' => 'High',
            'soil_type' => 'Sandy, well-drained soil',
            'follow_up_crops' => ['legumes', 'cereals', 'vegetables']
        ]
    ];

    /**
     * Analyze soil data and recommend best crops
     */
    public function analyzeSoilAndRecommend(SoilData $soilData): array
    {
        $recommendations = [];

        foreach ($this->cropDatabase as $cropKey => $crop) {
            $score = $this->calculateCropSuitabilityScore($soilData, $crop);

            if ($score > 0) {
                $recommendations[] = [
                    'crop_key' => $cropKey,
                    'crop_data' => $crop,
                    'suitability_score' => $score,
                    'confidence_level' => $this->getConfidenceLevel($score),
                    'priority' => $this->getPriority($score),
                    'recommendation_details' => $this->generateRecommendationDetails($soilData, $crop, $score),
                    'fertilizer_recommendations' => $this->getFertilizerRecommendations($soilData, $crop),
                    'irrigation_schedule' => $this->getIrrigationSchedule($soilData, $crop)
                ];
            }
        }

        // Sort by suitability score (highest first)
        usort($recommendations, function($a, $b) {
            return $b['suitability_score'] <=> $a['suitability_score'];
        });

        // Return top 2 recommendations with follow-up crops
        return array_slice($recommendations, 0, 2);
    }

    /**
     * Calculate crop suitability score based on soil conditions
     */
    private function calculateCropSuitabilityScore(SoilData $soilData, array $crop): float
    {
        $score = 0;
        $maxScore = 100;

        // pH Score (25 points)
        $phScore = $this->calculateParameterScore(
            $soilData->ph_level,
            $crop['ph_range'][0],
            $crop['ph_range'][1],
            $crop['ph_optimal'][0],
            $crop['ph_optimal'][1],
            25
        );

        // Moisture Score (25 points)
        $moistureScore = $this->calculateParameterScore(
            $soilData->moisture_level,
            $crop['moisture_range'][0],
            $crop['moisture_range'][1],
            $crop['moisture_optimal'][0],
            $crop['moisture_optimal'][1],
            25
        );

        // Temperature Score (20 points)
        $tempScore = $this->calculateParameterScore(
            $soilData->temperature,
            $crop['temp_range'][0],
            $crop['temp_range'][1],
            $crop['temp_optimal'][0],
            $crop['temp_optimal'][1],
            20
        );

        // Nutrient Score (30 points total: N=10, P=10, K=10)
        $nitrogenScore = $soilData->nitrogen_level >= $crop['nitrogen_min'] ? 10 :
            ($soilData->nitrogen_level / $crop['nitrogen_min']) * 10;

        $phosphorusScore = $soilData->phosphorus_level >= $crop['phosphorus_min'] ? 10 :
            ($soilData->phosphorus_level / $crop['phosphorus_min']) * 10;

        $potassiumScore = $soilData->potassium_level >= $crop['potassium_min'] ? 10 :
            ($soilData->potassium_level / $crop['potassium_min']) * 10;

        $score = $phScore + $moistureScore + $tempScore + $nitrogenScore + $phosphorusScore + $potassiumScore;

        // Season bonus (5 points)
        $currentSeasonLetter = substr($soilData->season, 0, 1);
        if (in_array($currentSeasonLetter, $crop['season_preference'])) {
            $score += 5;
            $maxScore += 5;
        }

        // Normalize score to 0-100 range
        return min(100, ($score / $maxScore) * 100);
    }

    /**
     * Calculate score for a specific parameter
     */
    private function calculateParameterScore(
        ?float $value,
        float $minRange,
        float $maxRange,
        float $minOptimal,
        float $maxOptimal,
        float $maxPoints
    ): float {
        if ($value === null) {
            return $maxPoints * 0.5; // Give 50% score if data is missing
        }

        // If within optimal range, give full points
        if ($value >= $minOptimal && $value <= $maxOptimal) {
            return $maxPoints;
        }

        // If within acceptable range but not optimal, give partial points
        if ($value >= $minRange && $value <= $maxRange) {
            if ($value < $minOptimal) {
                // Below optimal
                $ratio = ($value - $minRange) / ($minOptimal - $minRange);
                return $maxPoints * (0.5 + $ratio * 0.5);
            } else {
                // Above optimal
                $ratio = ($maxRange - $value) / ($maxRange - $maxOptimal);
                return $maxPoints * (0.5 + $ratio * 0.5);
            }
        }

        // Outside acceptable range
        return 0;
    }

    /**
     * Get confidence level based on score
     */
    private function getConfidenceLevel(float $score): string
    {
        if ($score >= 85) return 'Very High';
        if ($score >= 70) return 'High';
        if ($score >= 55) return 'Medium';
        if ($score >= 40) return 'Low';
        return 'Very Low';
    }

    /**
     * Get priority based on score
     */
    private function getPriority(float $score): string
    {
        if ($score >= 75) return 'high';
        if ($score >= 50) return 'medium';
        return 'low';
    }

    /**
     * Generate detailed recommendation text
     */
    private function generateRecommendationDetails(SoilData $soilData, array $crop, float $score): string
    {
        $details = "Based on your soil analysis, {$crop['name']} is ";

        if ($score >= 85) {
            $details .= "an excellent choice for your field conditions. ";
        } elseif ($score >= 70) {
            $details .= "a very good choice for your field conditions. ";
        } elseif ($score >= 55) {
            $details .= "a suitable choice for your field conditions. ";
        } else {
            $details .= "possible but may require soil improvements. ";
        }

        $details .= "Growth period: {$crop['growth_period']}. ";
        $details .= "Yield potential: {$crop['yield_potential']}. ";
        $details .= "Market value: {$crop['market_value']}. ";
        $details .= "Drought tolerance: {$crop['drought_tolerance']}. ";

        // Add specific recommendations based on soil conditions
        $issues = [];

        if ($soilData->ph_level && ($soilData->ph_level < $crop['ph_range'][0] || $soilData->ph_level > $crop['ph_range'][1])) {
            if ($soilData->ph_level < $crop['ph_range'][0]) {
                $issues[] = "Consider lime application to increase soil pH";
            } else {
                $issues[] = "Consider sulfur application to decrease soil pH";
            }
        }

        if ($soilData->nitrogen_level && $soilData->nitrogen_level < $crop['nitrogen_min']) {
            $issues[] = "Nitrogen levels are below optimal - consider nitrogen-rich fertilizers";
        }

        if ($soilData->phosphorus_level && $soilData->phosphorus_level < $crop['phosphorus_min']) {
            $issues[] = "Phosphorus levels are low - consider phosphate fertilizers";
        }

        if ($soilData->potassium_level && $soilData->potassium_level < $crop['potassium_min']) {
            $issues[] = "Potassium levels are insufficient - consider potash fertilizers";
        }

        if (!empty($issues)) {
            $details .= " Recommendations: " . implode('; ', $issues) . ".";
        }

        return $details;
    }

    /**
     * Get fertilizer recommendations
     */
    private function getFertilizerRecommendations(SoilData $soilData, array $crop): array
    {
        $recommendations = [];

        // NPK recommendations based on crop needs and current soil levels
        $nDeficit = max(0, $crop['nitrogen_min'] - ($soilData->nitrogen_level ?? 0));
        $pDeficit = max(0, $crop['phosphorus_min'] - ($soilData->phosphorus_level ?? 0));
        $kDeficit = max(0, $crop['potassium_min'] - ($soilData->potassium_level ?? 0));

        if ($nDeficit > 0) {
            $recommendations[] = [
                'type' => 'Nitrogen',
                'fertilizer' => 'Urea (46-0-0)',
                'amount' => round($nDeficit * 2.17) . ' kg/ha',
                'timing' => 'Split application: 50% at planting, 50% at 4-6 weeks'
            ];
        }

        if ($pDeficit > 0) {
            $recommendations[] = [
                'type' => 'Phosphorus',
                'fertilizer' => 'DAP (18-46-0)',
                'amount' => round($pDeficit * 2.17) . ' kg/ha',
                'timing' => 'Apply all at planting'
            ];
        }

        if ($kDeficit > 0) {
            $recommendations[] = [
                'type' => 'Potassium',
                'fertilizer' => 'Muriate of Potash (0-0-60)',
                'amount' => round($kDeficit * 1.67) . ' kg/ha',
                'timing' => 'Apply before planting or side-dress'
            ];
        }

        // Organic matter recommendations
        if (($soilData->organic_matter ?? 0) < 3.0) {
            $recommendations[] = [
                'type' => 'Organic Matter',
                'fertilizer' => 'Compost or Well-rotted Manure',
                'amount' => '2-3 tons/ha',
                'timing' => 'Apply 2-3 weeks before planting'
            ];
        }

        return $recommendations;
    }

    /**
     * Get irrigation schedule recommendations
     */
    private function getIrrigationSchedule(SoilData $soilData, array $crop): array
    {
        $schedule = [];
        $currentMoisture = $soilData->moisture_level ?? 50;
        $optimalMoisture = ($crop['moisture_optimal'][0] + $crop['moisture_optimal'][1]) / 2;

        if ($currentMoisture < $crop['moisture_range'][0]) {
            $schedule[] = [
                'phase' => 'Immediate',
                'frequency' => 'Daily',
                'amount' => '25-30 mm',
                'method' => 'Drip or sprinkler irrigation',
                'note' => 'Soil moisture is critically low'
            ];
        } elseif ($currentMoisture < $optimalMoisture) {
            $schedule[] = [
                'phase' => 'Establishment (0-2 weeks)',
                'frequency' => 'Every 2-3 days',
                'amount' => '15-20 mm',
                'method' => 'Light, frequent watering',
                'note' => 'Keep soil consistently moist'
            ];
        }

        $schedule[] = [
            'phase' => 'Vegetative Growth',
            'frequency' => 'Every 3-5 days',
            'amount' => '20-25 mm',
            'method' => 'Deep watering',
            'note' => 'Adjust based on rainfall and temperature'
        ];

        $schedule[] = [
            'phase' => 'Reproductive/Flowering',
            'frequency' => 'Every 2-3 days',
            'amount' => '25-30 mm',
            'method' => 'Consistent moisture critical',
            'note' => 'Most critical phase for water'
        ];

        if (isset($crop['growth_period']) && !str_contains($crop['growth_period'], 'Perennial')) {
            $schedule[] = [
                'phase' => 'Maturity',
                'frequency' => 'Reduce to weekly',
                'amount' => '15-20 mm',
                'method' => 'Allow slight drying',
                'note' => 'Reduce irrigation before harvest'
            ];
        }

        return $schedule;
    }

    /**
     * Generate soil recommendations and save to database
     */
    public function generateAndSaveRecommendations(SoilData $soilData): array
    {
        $recommendations = $this->analyzeSoilAndRecommend($soilData);
        $savedRecommendations = [];

        foreach ($recommendations as $index => $recommendation) {
            $saved = SoilRecommendation::create([
                'soil_data_id' => $soilData->id,
                'user_id' => $soilData->user_id,
                'recommended_crop' => $recommendation['crop_data']['name'],
                'recommendation_details' => $recommendation['recommendation_details'],
                'fertilizer_recommendations' => $recommendation['fertilizer_recommendations'],
                'irrigation_schedule' => $recommendation['irrigation_schedule'],
                'confidence_score' => round($recommendation['suitability_score'], 2),
                'season' => $soilData->season,
                'priority' => $recommendation['priority']
            ]);

            $savedRecommendations[] = $saved;
        }

        return $savedRecommendations;
    }
}
