<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\Farm;
use App\Models\User;

class DeviceSeeder extends Seeder
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

        $deviceTypes = [
            'soil_sensor' => ['pH Sensor', 'Moisture Sensor', 'NPK Sensor'],
            'weather_station' => ['Temperature Sensor', 'Humidity Sensor', 'Rainfall Gauge'],
            'ph_meter' => ['pH Level Monitor'],
            'moisture_sensor' => ['Soil Moisture Monitor'],
            'temperature_sensor' => ['Soil Temperature Monitor'],
            'nutrient_analyzer' => ['NPK Analyzer', 'Organic Matter Sensor']
        ];

        $deviceCounter = 1;
        $systemAdminId = User::where('role_id', 4)->first()->id ?? 1;

        foreach ($farms as $farm) {
            // Each farm gets 2-4 devices
            $numDevices = rand(2, 4);

            for ($i = 0; $i < $numDevices; $i++) {
                $deviceType = array_rand($deviceTypes);
                $sensors = $deviceTypes[$deviceType];

                Device::create([
                    'device_serial_number' => sprintf('SCR%04d%03d', $farm->id, $deviceCounter),
                    'device_name' => ucfirst(str_replace('_', ' ', $deviceType)) . ' #' . $deviceCounter,
                    'device_type' => $deviceType,
                    'user_id' => $farm->user_id,
                    'farm_id' => $farm->id,
                    'installation_location' => 'Section ' . chr(65 + $i) . ' - ' . $farm->name,
                    'farm_upi' => $farm->name . '-' . sprintf('%03d', $i + 1),
                    'sensor_types' => $sensors,
                    'latitude' => $farm->latitude ? $farm->latitude + (rand(-100, 100) / 10000) : rand(-9000, 9000) / 100000,
                    'longitude' => $farm->longitude ? $farm->longitude + (rand(-100, 100) / 10000) : rand(-18000, 18000) / 100000,
                    'notes' => 'Automated seeded device for testing purposes. Installed in ' . $farm->location,
                    'firmware_version' => 'v' . rand(1, 3) . '.' . rand(0, 9) . '.' . rand(0, 9),
                    'battery_level' => rand(20, 100),
                    'status' => $this->randomStatus(),
                    'assigned_by' => $systemAdminId,
                    'installed_at' => now()->subDays(rand(1, 365)),
                    'last_reading_at' => $this->randomLastReading(),
                    'last_maintenance_at' => rand(0, 1) ? now()->subDays(rand(1, 90)) : null,
                    'created_at' => now()->subDays(rand(1, 365)),
                    'updated_at' => now()
                ]);

                $deviceCounter++;
            }
        }

        $this->command->info('Devices created successfully with new structure!');
        $this->command->info("Created {$deviceCounter} devices across {$farms->count()} farms");
    }

    /**
     * Generate random device status
     */
    private function randomStatus()
    {
        $statuses = ['active', 'inactive', 'maintenance', 'offline'];
        $weights = [70, 15, 10, 5]; // 70% active, 15% inactive, 10% maintenance, 5% offline

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $statuses[$index];
            }
        }

        return 'active';
    }

    /**
     * Generate random last reading time
     */
    private function randomLastReading()
    {
        $statusOptions = ['active', 'inactive', 'maintenance', 'offline'];
        $status = $statusOptions[array_rand($statusOptions)];

        if ($status === 'offline') {
            return now()->subDays(rand(1, 30)); // Offline devices haven't read in days
        } elseif ($status === 'maintenance') {
            return now()->subHours(rand(6, 48)); // Maintenance devices last read hours ago
        } elseif ($status === 'inactive') {
            return now()->subHours(rand(2, 24)); // Inactive devices last read hours ago
        } else {
            return now()->subMinutes(rand(5, 120)); // Active devices read recently
        }
    }
}
