<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create System Admin
        $systemAdmin = User::updateOrCreate(
            ['email' => 'admin@smartcrop.com'],
            [
                'name' => 'System Administrator',
                'username' => 'admin',
                'email' => 'admin@smartcrop.com',
                'phone' => '+250788123456',
                'password' => Hash::make('password123'),
                'role_id' => 4, // System Admin
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login' => now()
            ]
        );

        // Create Data Analyst
        $dataAnalyst = User::updateOrCreate(
            ['email' => 'analyst@smartcrop.com'],
            [
                'name' => 'Data Analyst',
                'username' => 'analyst',
                'email' => 'analyst@smartcrop.com',
                'phone' => '+250788123457',
                'password' => Hash::make('password123'),
                'role_id' => 3, // Data Analyst
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login' => now()
            ]
        );

        // Create Field Officer
        $fieldOfficer = User::updateOrCreate(
            ['email' => 'officer@smartcrop.com'],
            [
                'name' => 'Field Officer',
                'username' => 'officer',
                'email' => 'officer@smartcrop.com',
                'phone' => '+250788123458',
                'password' => Hash::make('password123'),
                'role_id' => 2, // Field Officer
                'status' => 'active',
                'email_verified_at' => now(),
                'last_login' => now()
            ]
        );

        // Create Sample Farmers
        $farmers = [
            [
                'name' => 'John Farmer',
                'username' => 'johnfarmer',
                'email' => 'john@farmer.com',
                'phone' => '+250788111111',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'status' => 'active'
            ],
            [
                'name' => 'Mary Agriculture',
                'username' => 'maryagri',
                'email' => 'mary@farmer.com',
                'phone' => '+250788222222',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'status' => 'active'
            ],
            [
                'name' => 'Peter Cultivator',
                'username' => 'petercult',
                'email' => 'peter@farmer.com',
                'phone' => '+250788333333',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'status' => 'active'
            ],
            [
                'name' => 'Sarah Grower',
                'username' => 'sarahgrow',
                'email' => 'sarah@farmer.com',
                'phone' => '+250788444444',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'status' => 'active'
            ]
        ];

        foreach ($farmers as $farmerData) {
            User::updateOrCreate(
                ['email' => $farmerData['email']],
                array_merge($farmerData, [
                    'email_verified_at' => now(),
                    'last_login' => now()
                ])
            );
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@smartcrop.com / password123');
        $this->command->info('Analyst: analyst@smartcrop.com / password123');
        $this->command->info('Officer: officer@smartcrop.com / password123');
        $this->command->info('Farmers: john@farmer.com, mary@farmer.com, peter@farmer.com, sarah@farmer.com / password123');
    }
}
