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
        Schema::create('crop_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soil_data_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('farm_id')->constrained()->onDelete('cascade');
            $table->string('crop_type');
            $table->string('season')->nullable();
            $table->date('planted_date');
            $table->date('harvest_date')->nullable();
            $table->decimal('yield_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['farm_id', 'planted_date']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crop_histories');
    }
};
