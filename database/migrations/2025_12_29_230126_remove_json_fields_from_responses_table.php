<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            // Remove redundant JSON fields - data now in dedicated tables
            $table->dropColumn(['family_members', 'health_data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            // Restore columns if needed for rollback
            $table->text('family_members')->nullable();
            $table->text('health_data')->nullable();
        });
    }
};
