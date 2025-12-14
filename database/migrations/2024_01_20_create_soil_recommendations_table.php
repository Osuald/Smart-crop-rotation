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
        Schema::create('soil_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soil_data_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('recommended_crop');
            $table->text('recommendation_details');
            $table->json('fertilizer_recommendations')->nullable();
            $table->json('irrigation_schedule')->nullable();
            $table->decimal('confidence_score', 5, 2)->default(0); // 0-100
            $table->string('season', 10);
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->boolean('is_applied')->default(false);
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'season']);
            $table->index(['recommended_crop', 'confidence_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soil_recommendations');
    }
};
