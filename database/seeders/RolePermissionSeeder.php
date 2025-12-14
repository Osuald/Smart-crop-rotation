<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $roles = [
            ['id' => 1, 'name' => 'Farmer', 'description' => 'Individual farmers who manage their own land'],
            ['id' => 2, 'name' => 'Field Officer', 'description' => 'Agricultural extension officers'],
            ['id' => 3, 'name' => 'Data Analyst', 'description' => 'Analyze crop and soil data'],
            ['id' => 4, 'name' => 'System Admin', 'description' => 'Full system administration access']
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['id' => $roleData['id']], $roleData);
        }

        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'view_users', 'description' => 'View user list', 'category' => 'User Management'],
            ['name' => 'create_users', 'description' => 'Create new users', 'category' => 'User Management'],
            ['name' => 'edit_users', 'description' => 'Edit user information', 'category' => 'User Management'],
            ['name' => 'delete_users', 'description' => 'Delete users', 'category' => 'User Management'],
            ['name' => 'manage_user_permissions', 'description' => 'Manage user permissions', 'category' => 'User Management'],

            // Soil Management
            ['name' => 'view_soil_data', 'description' => 'View soil data', 'category' => 'Soil Management'],
            ['name' => 'create_soil_data', 'description' => 'Create soil data entries', 'category' => 'Soil Management'],
            ['name' => 'edit_soil_data', 'description' => 'Edit soil data', 'category' => 'Soil Management'],
            ['name' => 'delete_soil_data', 'description' => 'Delete soil data', 'category' => 'Soil Management'],
            ['name' => 'generate_soil_recommendations', 'description' => 'Generate soil recommendations', 'category' => 'Soil Management'],

            // Farm Management
            ['name' => 'view_farms', 'description' => 'View farm information', 'category' => 'Farm Management'],
            ['name' => 'create_farms', 'description' => 'Create new farms', 'category' => 'Farm Management'],
            ['name' => 'edit_farms', 'description' => 'Edit farm information', 'category' => 'Farm Management'],
            ['name' => 'delete_farms', 'description' => 'Delete farms', 'category' => 'Farm Management'],
            ['name' => 'view_own_farms', 'description' => 'View own farms only', 'category' => 'Farm Management'],

            // Device Management
            ['name' => 'view_devices', 'description' => 'View device information', 'category' => 'Device Management'],
            ['name' => 'create_devices', 'description' => 'Register new devices', 'category' => 'Device Management'],
            ['name' => 'edit_devices', 'description' => 'Edit device information', 'category' => 'Device Management'],
            ['name' => 'delete_devices', 'description' => 'Delete devices', 'category' => 'Device Management'],

            // Reports & Analytics
            ['name' => 'view_reports', 'description' => 'View system reports', 'category' => 'Reports & Analytics'],
            ['name' => 'export_data', 'description' => 'Export data', 'category' => 'Reports & Analytics'],
            ['name' => 'view_analytics', 'description' => 'View analytics dashboard', 'category' => 'Reports & Analytics'],

            // System Administration
            ['name' => 'manage_roles', 'description' => 'Manage roles and permissions', 'category' => 'System Administration'],
            ['name' => 'system_settings', 'description' => 'Access system settings', 'category' => 'System Administration'],
            ['name' => 'view_logs', 'description' => 'View system logs', 'category' => 'System Administration'],
            ['name' => 'view_dashboard', 'description' => 'Access dashboard', 'category' => 'System Administration']
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(['name' => $permissionData['name']], $permissionData);
        }

        // Assign permissions to roles
        $rolePermissions = [
            'Farmer' => [
                'view_soil_data', 'create_soil_data', 'edit_soil_data',
                'view_own_farms', 'create_farms', 'edit_farms',
                'view_devices', 'create_devices', 'edit_devices',
                'view_analytics', 'view_dashboard'
            ],
            'Field Officer' => [
                'view_soil_data', 'create_soil_data', 'edit_soil_data', 'generate_soil_recommendations',
                'view_farms', 'view_devices', 'view_reports', 'view_analytics', 'view_dashboard'
            ],
            'Data Analyst' => [
                'view_soil_data', 'view_farms', 'view_devices',
                'view_reports', 'export_data', 'view_analytics', 'view_dashboard'
            ],
            'System Admin' => [
                'view_users', 'create_users', 'edit_users', 'delete_users', 'manage_user_permissions',
                'view_soil_data', 'create_soil_data', 'edit_soil_data', 'delete_soil_data', 'generate_soil_recommendations',
                'view_farms', 'create_farms', 'edit_farms', 'delete_farms', 'view_own_farms',
                'view_devices', 'create_devices', 'edit_devices', 'delete_devices',
                'view_reports', 'export_data', 'view_analytics',
                'manage_roles', 'system_settings', 'view_logs', 'view_dashboard'
            ]
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $role->permissions()->sync($permissions->pluck('id'));
            }
        }

        $this->command->info('Roles and permissions created successfully!');
    }


}
