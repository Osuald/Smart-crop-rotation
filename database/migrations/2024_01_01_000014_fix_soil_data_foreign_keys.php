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
        // Check if soil_data table exists and drop it
        if (Schema::hasTable('soil_data')) {
            Schema::drop('soil_data');
        }

        // Recreate soil_data table with proper foreign keys
        Schema::create('soil_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->decimal('ph', 3, 1);
            $table->decimal('moisture', 5, 2);
            $table->decimal('temperature', 5, 2);
            $table->decimal('nitrogen', 8, 2)->nullable();
            $table->decimal('phosphorus', 8, 2)->nullable();
            $table->decimal('potassium', 8, 2)->nullable();
            $table->decimal('soil_health_score', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_manual')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['device_id', 'created_at']);
            $table->index(['farm_id', 'created_at']);
            $table->index('soil_health_score');
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
