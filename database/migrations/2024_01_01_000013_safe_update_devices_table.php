<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if devices table exists and backup data if needed
        $hasData = false;
        $backupData = [];

        if (Schema::hasTable('devices')) {
            $backupData = DB::table('devices')->get()->toArray();
            $hasData = count($backupData) > 0;

            // Drop the existing devices table
            Schema::drop('devices');
        }

        // Create the new devices table with updated structure
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_serial_number')->unique();
            $table->string('device_name');
            $table->string('device_type')->default('soil_sensor');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('farm_id')->nullable()->constrained()->onDelete('set null');
            $table->string('installation_location');
            $table->string('farm_upi')->nullable();
            $table->json('sensor_types')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->string('firmware_version', 50)->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'offline'])->default('active');
            $table->integer('battery_level')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('last_reading_at')->nullable();
            $table->timestamp('last_maintenance_at')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index('farm_id');
            $table->index('device_type');
            $table->index('status');
            $table->index('last_reading_at');
        });

        // If we had data, try to migrate what we can
        if ($hasData) {
            foreach ($backupData as $device) {
                try {
                    DB::table('devices')->insert([
                        'id' => $device->id ?? null,
                        'device_serial_number' => $device->serial_number ?? $device->device_serial_number ?? 'MIGRATED_' . uniqid(),
                        'device_name' => $device->name ?? $device->device_name ?? 'Migrated Device',
                        'device_type' => $device->type ?? $device->device_type ?? 'soil_sensor',
                        'user_id' => $device->user_id,
                        'farm_id' => $device->farm_id ?? null,
                        'installation_location' => $device->location ?? $device->installation_location ?? 'Unknown Location',
                        'farm_upi' => $device->farm_upi ?? null,
                        'sensor_types' => $device->sensor_types ?? json_encode(['pH Sensor', 'Moisture Sensor']),
                        'latitude' => $device->latitude ?? null,
                        'longitude' => $device->longitude ?? null,
                        'notes' => $device->notes ?? 'Migrated from old structure',
                        'firmware_version' => $device->firmware_version ?? 'v1.0.0',
                        'status' => $device->status ?? 'active',
                        'battery_level' => $device->battery_level ?? rand(50, 100),
                        'assigned_by' => $device->assigned_by ?? 1,
                        'installed_at' => $device->installed_at ?? $device->created_at ?? now(),
                        'last_reading_at' => $device->last_reading_at ?? now(),
                        'last_maintenance_at' => $device->last_maintenance_at ?? null,
                        'created_at' => $device->created_at ?? now(),
                        'updated_at' => $device->updated_at ?? now(),
                    ]);
                } catch (\Exception $e) {
                    // Log error but continue migration
                    \Log::warning("Could not migrate device data: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
