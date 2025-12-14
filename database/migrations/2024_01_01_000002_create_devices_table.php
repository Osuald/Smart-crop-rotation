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
            $table->string('name');
            $table->string('type')->default('soil_sensor');
            $table->string('serial_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('farm_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->string('location')->nullable();
            $table->timestamp('last_reading_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('farm_id');
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
