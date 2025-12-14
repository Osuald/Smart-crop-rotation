<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_serial_number')->unique();
            $table->string('device_name');
            $table->string('device_type', 100);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('installation_location');
            $table->string('farm_upi', 100)->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'offline'])->default('inactive');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('firmware_version', 50)->nullable();
            $table->integer('battery_level')->nullable();
            $table->json('sensor_types')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('last_communication')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['status']);
            $table->index(['user_id']);
            $table->index(['device_type']);
            $table->index(['last_communication']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
};
