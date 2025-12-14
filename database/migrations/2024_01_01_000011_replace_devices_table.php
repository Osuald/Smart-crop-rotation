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
        // Drop the existing devices table if it exists
        Schema::dropIfExists('devices');

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');

        // Recreate a basic devices table if needed (optional)
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('soil_sensor');
            $table->string('serial_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('farm_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->string('location')->nullable();
            $table->timestamp('last_reading_at')->nullable();
            $table->timestamps();
        });
    }
};
