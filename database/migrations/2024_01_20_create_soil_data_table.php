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
        Schema::create('soil_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('ph_level', 4, 2)->nullable();
            $table->decimal('moisture_level', 5, 2)->nullable(); // percentage
            $table->decimal('nitrogen_level', 8, 2)->nullable(); // mg/kg
            $table->decimal('phosphorus_level', 8, 2)->nullable(); // mg/kg
            $table->decimal('potassium_level', 8, 2)->nullable(); // mg/kg
            $table->decimal('temperature', 5, 2)->nullable(); // celsius
            $table->decimal('conductivity', 8, 2)->nullable(); // µS/cm
            $table->decimal('organic_matter', 5, 2)->nullable(); // percentage
            $table->string('season', 10); // A2024, B2024, A2025, etc.
            $table->enum('data_quality', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->json('additional_parameters')->nullable(); // for other sensor data
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['device_id', 'recorded_at']);
            $table->index(['user_id', 'season']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_data');
    }
};
