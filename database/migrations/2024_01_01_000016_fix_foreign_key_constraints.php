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
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Drop soil_data table if it exists
        Schema::dropIfExists('soil_data');

        // Drop crop_histories table if it exists
        Schema::dropIfExists('crop_histories');

        // Recreate soil_data table with correct foreign key
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

        // Recreate crop_histories table with correct foreign key
        Schema::create('crop_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soil_data_id')->nullable()->constrained('soil_data')->onDelete('set null');
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->string('crop_type');
            $table->string('season')->nullable();
            $table->date('planted_date');
            $table->date('harvest_date')->nullable();
            $table->decimal('yield_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['farm_id', 'planted_date']);
            $table->index('user_id');
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_histories');
        Schema::dropIfExists('soil_data');
    }
};
