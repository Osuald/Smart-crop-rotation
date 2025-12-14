<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder is now replaced by RolePermissionSeeder
        // Call the main seeder instead
        $this->call(RolePermissionSeeder::class);
    }
}
