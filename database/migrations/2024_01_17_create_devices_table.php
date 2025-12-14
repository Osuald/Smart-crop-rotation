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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_serial_number')->unique();
            $table->string('device_name');
            $table->string('device_type')->default('IoT Sensor');
            $table->enum('status', ['active', 'inactive', 'maintenance', 'offline'])->default('inactive');
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            $table->string('farm_plot_number'); // UPI number
            $table->json('sensor_types')->nullable(); // ['temperature', 'humidity', 'soil_ph', etc.]
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('last_communication')->nullable();
            $table->text('notes')->nullable();
            $table->string('firmware_version')->nullable();
            $table->integer('battery_level')->nullable(); // 0-100
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
