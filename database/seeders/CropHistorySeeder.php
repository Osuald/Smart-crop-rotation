<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CropHistory;
use App\Models\Farm;
use App\Models\SoilData;
use Carbon\Carbon;

class CropHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $farms = Farm::with('user')->get();

        if ($farms->isEmpty()) {
            $this->command->warn('No farms found. Please run FarmSeeder first.');
            return;
        }

        $this->command->info('Creating crop history for ' . $farms->count() . ' farms...');

        $crops = [
            'Maize' => ['season' => 'Season A', 'yield_range' => [2000, 5000]],
            'Beans' => ['season' => 'Season B', 'yield_range' => [1000, 2500]],
            'Rice' => ['season' => 'Season A', 'yield_range' => [3000, 6000]],
            'Potato' => ['season' => 'Season B', 'yield_range' => [8000, 15000]],
            'Sorghum' => ['season' => 'Season A', 'yield_range' => [1500, 3000]],
            'Cassava' => ['season' => 'Both', 'yield_range' => [10000, 20000]],
            'Sweet Potato' => ['season' => 'Season B', 'yield_range' => [5000, 12000]],
            'Groundnuts' => ['season' => 'Season A', 'yield_range' => [800, 1800]],
            'Soybeans' => ['season' => 'Season B', 'yield_range' => [1200, 2400]],
            'Wheat' => ['season' => 'Season A', 'yield_range' => [2500, 4500]]
        ];

        $totalCreated = 0;

        foreach ($farms as $farm) {
            // Get some soil data entries for this farm
            $soilDataEntries = SoilData::where('farm_id', $farm->id)
                                      ->orderBy('created_at', 'desc')
                                      ->take(10)
                                      ->get();

            // Create 5-8 crop history entries per farm
            $numEntries = rand(5, 8);

            for ($i = 0; $i < $numEntries; $i++) {
                $cropName = array_rand($crops);
                $cropInfo = $crops[$cropName];

                // Random planting date in the last 2 years
                $plantedDate = Carbon::now()->subDays(rand(30, 730));
                $harvestDate = $plantedDate->copy()->addDays(rand(90, 150));

                // Determine season based on planting date
                $season = $this->determineSeason($plantedDate);

                // Get a soil data entry if available, otherwise use null
                $soilData = null;
                $healthMultiplier = 0.8; // Default multiplier

                if ($soilDataEntries->isNotEmpty()) {
                    $soilData = $soilDataEntries->random();
                    $healthMultiplier = $soilData->soil_health_score / 100;
                }

                // Calculate yield based on soil health or use default
                $baseYield = rand($cropInfo['yield_range'][0], $cropInfo['yield_range'][1]);
                $finalYield = round($baseYield * $healthMultiplier);

                try {
                    CropHistory::create([
                        'soil_data_id' => $soilData ? $soilData->id : null,
                        'farm_id' => $farm->id,
                        'crop_type' => $cropName,
                        'season' => $season,
                        'planted_date' => $plantedDate,
                        'harvest_date' => $harvestDate,
                        'yield_amount' => $finalYield,
                        'notes' => $this->generateCropNotes($cropName, $finalYield, $cropInfo['yield_range'], $soilData),
                        'user_id' => $farm->user_id,
                        'created_at' => $plantedDate,
                        'updated_at' => $harvestDate
                    ]);

                    $totalCreated++;
                } catch (\Exception $e) {
                    $this->command->error("Error creating crop history for farm {$farm->id}: " . $e->getMessage());
                    continue;
                }
            }
        }

        $this->command->info("Crop history created successfully! Created {$totalCreated} crop history records.");
    }

    private function determineSeason($date)
    {
        $month = $date->month;

        // Season A: September to February
        // Season B: March to August
        if ($month >= 9 || $month <= 2) {
            return 'Season A';
        } else {
            return 'Season B';
        }
    }

    private function generateCropNotes($cropName, $yield, $yieldRange, $soilData = null)
    {
        $notes = [];

        // Performance assessment
        $midRange = ($yieldRange[0] + $yieldRange[1]) / 2;
        if ($yield >= $midRange * 1.1) {
            $notes[] = "Excellent harvest with above-average yield";
        } elseif ($yield >= $midRange * 0.9) {
            $notes[] = "Good harvest with average yield";
        } else {
            $notes[] = "Below-average yield, needs soil improvement";
        }

        // Add soil data reference if available
        if ($soilData) {
            $notes[] = "Soil health score at time of planting: {$soilData->soil_health_score}%";
        }

        // Random additional notes
        $additionalNotes = [
            "Applied organic fertilizer during growth",
            "Good weather conditions during season",
            "Some pest issues addressed with organic pesticides",
            "Implemented crop rotation practices",
            "Used drought-resistant variety",
            "Manual irrigation supplemented rainfall",
            "Soil testing showed good nutrient levels",
            "Harvested at optimal maturity"
        ];

        if (rand(1, 3) == 1) { // 33% chance of additional note
            $notes[] = $additionalNotes[array_rand($additionalNotes)];
        }

        return implode('. ', $notes) . '.';
    }
}
