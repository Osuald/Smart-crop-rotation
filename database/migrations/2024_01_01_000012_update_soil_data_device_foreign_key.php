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
        // Update soil_data table to ensure foreign key constraint is properly set
        Schema::table('soil_data', function (Blueprint $table) {
            // Drop existing foreign key if it exists
            $table->dropForeign(['device_id']);

            // Add the foreign key constraint again
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soil_data', function (Blueprint $table) {
            $table->dropForeign(['device_id']);
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }
};
