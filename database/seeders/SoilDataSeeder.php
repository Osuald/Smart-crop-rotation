<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoilData;
use App\Models\Device;
use App\Models\Farm;
use Carbon\Carbon;

class SoilDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if devices exist first
        $devices = Device::with(['farm', 'user'])->where('status', 'active')->get();

        if ($devices->isEmpty()) {
            $this->command->warn('No active devices found. Please run DeviceSeeder first.');
            return;
        }

        $this->command->info('Creating soil data for ' . $devices->count() . ' devices...');

        foreach ($devices as $device) {
            // Verify the device has a valid farm
            if (!$device->farm_id || !$device->farm) {
                $this->command->warn("Device {$device->id} has no valid farm. Skipping...");
                continue;
            }

            // Generate soil data for the last 30 days
            for ($days = 30; $days >= 0; $days--) {
                $readingsPerDay = rand(2, 6); // 2-6 readings per day

                for ($reading = 0; $reading < $readingsPerDay; $reading++) {
                    $timestamp = Carbon::now()->subDays($days)->addHours(rand(6, 18))->addMinutes(rand(0, 59));

                    try {
                        // Generate realistic soil data with some variation
                        $ph = $this->generatePH();
                        $moisture = $this->generateMoisture();
                        $temperature = $this->generateTemperature();
                        $nitrogen = rand(10, 80);
                        $phosphorus = rand(5, 40);
                        $potassium = rand(15, 60);

                        // Calculate soil health score
                        $healthScore = $this->calculateHealthScore($ph, $moisture, $temperature, $nitrogen, $phosphorus, $potassium);

                        SoilData::create([
                            'device_id' => $device->id,
                            'farm_id' => $device->farm_id,
                            'ph' => $ph,
                            'moisture' => $moisture,
                            'temperature' => $temperature,
                            'nitrogen' => $nitrogen,
                            'phosphorus' => $phosphorus,
                            'potassium' => $potassium,
                            'soil_health_score' => $healthScore,
                            'notes' => $days < 7 ? 'Recent automated reading' : null,
                            'is_manual' => rand(1, 10) == 1, // 10% manual entries
                            'created_by' => rand(1, 10) == 1 ? $device->user_id : null,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp
                        ]);
                    } catch (\Exception $e) {
                        $this->command->error("Error creating soil data for device {$device->id}: " . $e->getMessage());
                        continue;
                    }
                }
            }
        }

        $this->command->info('Soil data created successfully!');
    }

    private function generatePH()
    {
        // Generate pH between 4.5 and 8.5, with most values between 6.0 and 7.5
        $random = mt_rand() / mt_getrandmax();
        if ($random < 0.7) {
            // 70% chance of good pH (6.0-7.5)
            return round(6.0 + ($random * 1.5), 1);
        } else {
            // 30% chance of suboptimal pH
            return round(4.5 + ($random * 4.0), 1);
        }
    }

    private function generateMoisture()
    {
        // Generate moisture between 10% and 90%, with most values between 30% and 70%
        $random = mt_rand() / mt_getrandmax();
        if ($random < 0.6) {
            // 60% chance of good moisture (30-70%)
            return round(30 + ($random * 40), 1);
        } else {
            // 40% chance of suboptimal moisture
            return round(10 + ($random * 80), 1);
        }
    }

    private function generateTemperature()
    {
        // Generate temperature between 10°C and 40°C, with most values between 18°C and 28°C
        $random = mt_rand() / mt_getrandmax();
        if ($random < 0.65) {
            // 65% chance of good temperature (18-28°C)
            return round(18 + ($random * 10), 1);
        } else {
            // 35% chance of suboptimal temperature
            return round(10 + ($random * 30), 1);
        }
    }

    private function calculateHealthScore($ph, $moisture, $temperature, $nitrogen, $phosphorus, $potassium)
    {
        $score = 0;

        // pH score (0-30 points)
        if ($ph >= 6.0 && $ph <= 7.5) {
            $score += 30;
        } elseif ($ph >= 5.5 && $ph <= 8.0) {
            $score += 20;
        } else {
            $score += 10;
        }

        // Moisture score (0-25 points)
        if ($moisture >= 40 && $moisture <= 70) {
            $score += 25;
        } elseif ($moisture >= 30 && $moisture <= 80) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Temperature score (0-25 points)
        if ($temperature >= 18 && $temperature <= 28) {
            $score += 25;
        } elseif ($temperature >= 15 && $temperature <= 35) {
            $score += 15;
        } else {
            $score += 5;
        }

        // Nutrient score (0-20 points)
        if ($nitrogen >= 20 && $phosphorus >= 10 && $potassium >= 15) {
            $score += 20;
        } elseif ($nitrogen >= 10 && $phosphorus >= 5 && $potassium >= 10) {
            $score += 10;
        }

        return min(100, $score);
    }
}
