<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoilData;
use App\Services\CropRecommendationService;

class SoilRecommendationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cropRecommendationService = new CropRecommendationService();

        // Get recent soil data
        $soilDataRecords = SoilData::with(['device', 'user'])
            ->orderBy('recorded_at', 'desc')
            ->take(50) // Process last 50 soil readings
            ->get();

        if ($soilDataRecords->isEmpty()) {
            $this->command->warn('No soil data found. Please run SoilDataSeeder first.');
            return;
        }

        $recommendationsGenerated = 0;

        foreach ($soilDataRecords as $soilData) {
            try {
                $recommendations = $cropRecommendationService->generateAndSaveRecommendations($soilData);
                $recommendationsGenerated += count($recommendations);

                $this->command->info("Generated " . count($recommendations) . " recommendations for soil data ID: {$soilData->id}");
            } catch (\Exception $e) {
                $this->command->error("Failed to generate recommendations for soil data ID {$soilData->id}: " . $e->getMessage());
            }
        }

        $this->command->info("Total recommendations generated: {$recommendationsGenerated}");
    }
}
