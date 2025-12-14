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
        Schema::table('devices', function (Blueprint $table) {
            // Drop the farm_id foreign key and column
            $table->dropForeign(['farm_id']);
            $table->dropColumn('farm_id');

            // Add user_id to link device directly to user
            $table->foreignId('user_id')->after('status')->constrained()->onDelete('cascade');

            // Rename farm_plot_number to installation_location for clarity
            $table->renameColumn('farm_plot_number', 'installation_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Add back farm_id
            $table->foreignId('farm_id')->after('status')->constrained()->onDelete('cascade');

            // Drop user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Rename back to farm_plot_number
            $table->renameColumn('installation_location', 'farm_plot_number');
        });
    }
};
