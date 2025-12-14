<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Farm;
use App\Models\Device;
use App\Models\SoilData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // 1. Seed roles and permissions first
        $this->command->info('📋 Seeding roles and permissions...');
        $this->call(RolePermissionSeeder::class);

        // 2. Seed users (admin and farmers)
        $this->command->info('👥 Seeding users...');
        $this->call(UserSeeder::class);

        // 3. Seed farms for farmers
        $this->command->info('🚜 Seeding farms...');
        $this->call(FarmSeeder::class);

        // 4. Seed devices for farms
        $this->command->info('📱 Seeding devices...');
        $this->call(DeviceSeeder::class);

        // 5. Seed soil data for devices
        $this->command->info('🌱 Seeding soil data...');
        $this->call(SoilDataSeeder::class);

        // 6. Seed crop history
        $this->command->info('🌾 Seeding crop history...');
        $this->call(CropHistorySeeder::class);

        $this->command->info('✅ All seeders completed successfully!');

        // Create roles first
        $adminRole = Role::firstOrCreate(['name' => 'System Admin'], [





































































































}    }        $this->command->info('Farmer login: farmer@test.com / password123');        $this->command->info('Admin login: admin@smartcrop.com / password123');        $this->command->info('Database seeded successfully!');        }            }                ]);                    'updated_at' => now()                    'created_at' => now(),                    'season' => 'Season A',                    'recorded_at' => Carbon::now()->subHours(rand(1, 720)), // Last month                    'soil_health_score' => rand(40, 95),                    'potassium' => rand(15, 40),                    'phosphorus' => rand(5, 25),                    'nitrogen' => rand(10, 50),                    'temperature' => rand(18, 35),                    'moisture_level' => rand(20, 80),                    'ph_level' => round(rand(55, 85) / 10, 1), // 5.5 to 8.5                    'farm_id' => $farms[$index]->id,                    'device_id' => $device->id,                SoilData::create([            for ($i = 0; $i < 15; $i++) {        foreach ($devices as $index => $device) {        $farms = [$farm1, $farm2];        $devices = [$device1, $device2];        // Create sample soil data        ]);            'status' => 'active'            'farm_id' => $farm2->id,            'device_type' => 'Soil Sensor',            'device_name' => 'South Hill Sensor',        ], [            'device_serial_number' => 'SOIL-SENSOR-002'            'user_id' => $farmer->id,        $device2 = Device::firstOrCreate([        ]);            'status' => 'active'            'farm_id' => $farm1->id,            'device_type' => 'Soil Sensor',            'device_name' => 'North Field Sensor',        ], [            'device_serial_number' => 'SOIL-SENSOR-001'            'user_id' => $farmer->id,        $device1 = Device::firstOrCreate([        // Create devices for the farmer        ]);            'soil_type' => 'Sandy Loam'            'size' => 15.2,            'location' => 'Southern Hills, Test Region',        ], [            'name' => 'South Hill Farm'            'user_id' => $farmer->id,        $farm2 = Farm::firstOrCreate([        ]);            'soil_type' => 'Clay Loam'            'size' => 25.5,            'location' => 'Northern Valley, Test Region',        ], [            'name' => 'North Field Farm'            'user_id' => $farmer->id,        $farm1 = Farm::firstOrCreate([        // Create farms for the farmer        ]);            'phone' => '+0987654321'            'status' => 'active',            'role_id' => $farmerRole->id,            'password' => Hash::make('password123'),            'username' => 'testfarmer',            'name' => 'Test Farmer',        ], [            'email' => 'farmer@test.com'        $farmer = User::firstOrCreate([        // Create test farmer        ]);            'phone' => '+1234567890'            'status' => 'active',            'role_id' => $adminRole->id,            'password' => Hash::make('password123'),            'username' => 'admin',            'name' => 'System Administrator',        ], [            'email' => 'admin@smartcrop.com'        $admin = User::firstOrCreate([        // Create admin user        ]);            'description' => 'Farmer with access to own farms and devices'        $farmerRole = Role::firstOrCreate(['name' => 'Farmer'], [        ]);            'description' => 'Full system administrator access'
