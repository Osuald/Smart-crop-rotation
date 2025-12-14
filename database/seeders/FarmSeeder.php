<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Farm;
use App\Models\User;

class FarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get farmers
        $farmers = User::where('role_id', 1)->get();

        if ($farmers->isEmpty()) {
            $this->command->warn('No farmers found. Please run UserSeeder first.');
            return;
        }

        $farmData = [
            [
                'name' => 'Green Valley Farm',
                'location' => 'Nyagatare, Eastern Province',
                'size' => 5.5,
                'description' => 'Large scale maize and bean cultivation',
                'latitude' => -1.2921,
                'longitude' => 30.0944
            ],
            [
                'name' => 'Sunrise Agricultural Plot',
                'location' => 'Musanze, Northern Province',
                'size' => 3.2,
                'description' => 'Potato and vegetable farming',
                'latitude' => -1.4991,
                'longitude' => 29.6341
            ],
            [
                'name' => 'Golden Harvest Field',
                'location' => 'Huye, Southern Province',
                'size' => 7.8,
                'description' => 'Coffee and banana plantation',
                'latitude' => -2.5958,
                'longitude' => 29.7394
            ],
            [
                'name' => 'River Side Farm',
                'location' => 'Kamonyi, Southern Province',
                'size' => 4.1,
                'description' => 'Rice paddies and fish farming',
                'latitude' => -2.0178,
                'longitude' => 29.7394
            ],
            [
                'name' => 'Hill Top Agriculture',
                'location' => 'Rulindo, Northern Province',
                'size' => 2.5,
                'description' => 'Tea plantation and mixed farming',
                'latitude' => -1.7833,
                'longitude' => 30.0833
            ],
            [
                'name' => 'Valley View Farm',
                'location' => 'Gatsibo, Eastern Province',
                'size' => 6.3,
                'description' => 'Sorghum and groundnut cultivation',
                'latitude' => -1.5833,
                'longitude' => 30.4167
            ]
        ];

        foreach ($farmers as $index => $farmer) {
            // Each farmer gets 1-2 farms
            $numFarms = rand(1, 2);

            for ($i = 0; $i < $numFarms; $i++) {
                if (isset($farmData[$index * 2 + $i])) {
                    Farm::updateOrCreate(
                        [
                            'name' => $farmData[$index * 2 + $i]['name'],
                            'user_id' => $farmer->id
                        ],
                        array_merge($farmData[$index * 2 + $i], [
                            'user_id' => $farmer->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ])
                    );
                }
            }
        }

        $this->command->info('Farms created successfully!');
    }
}
