<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('devices', 'device_serial_number')) {
                $table->string('device_serial_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('devices', 'device_name')) {
                $table->string('device_name')->after('device_serial_number');
            }
            if (!Schema::hasColumn('devices', 'device_type')) {
                $table->string('device_type')->default('soil_sensor')->after('device_name');
            }
            if (!Schema::hasColumn('devices', 'installation_location')) {
                $table->string('installation_location')->nullable()->after('location');
            }
            if (!Schema::hasColumn('devices', 'farm_upi')) {
                $table->string('farm_upi')->nullable()->after('installation_location');
            }
            if (!Schema::hasColumn('devices', 'sensor_types')) {
                $table->json('sensor_types')->nullable()->after('farm_upi');
            }
            if (!Schema::hasColumn('devices', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('sensor_types');
            }
            if (!Schema::hasColumn('devices', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('devices', 'notes')) {
                $table->text('notes')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('devices', 'firmware_version')) {
                $table->string('firmware_version')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('devices', 'battery_level')) {
                $table->integer('battery_level')->nullable()->after('firmware_version');
            }
            if (!Schema::hasColumn('devices', 'assigned_by')) {
                $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null')->after('battery_level');
            }
            if (!Schema::hasColumn('devices', 'installed_at')) {
                $table->timestamp('installed_at')->nullable()->after('assigned_by');
            }
            if (!Schema::hasColumn('devices', 'last_maintenance_at')) {
                $table->timestamp('last_maintenance_at')->nullable()->after('last_reading_at');
            }

            // Update status enum to include new values
            $table->enum('status', ['active', 'inactive', 'maintenance', 'offline'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn([
                'device_serial_number', 'device_name', 'device_type',
                'installation_location', 'farm_upi', 'sensor_types',
                'latitude', 'longitude', 'notes', 'firmware_version',
                'battery_level', 'assigned_by', 'installed_at', 'last_maintenance_at'
            ]);
        });
    }
};
