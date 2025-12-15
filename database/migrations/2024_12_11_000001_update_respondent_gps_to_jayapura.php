<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all respondents GPS to Jayapura city coordinates
        // Jayapura city center: -2.5333, 140.7167
        // Range: Latitude -2.6000 to -2.4800, Longitude 140.6500 to 140.7800
        // This ensures all markers are on land in Jayapura city proper

        DB::statement('
            UPDATE respondents
            SET
                latitude = -2.6000 + (RANDOM() * 0.1200),
                longitude = 140.6500 + (RANDOM() * 0.1300)
            WHERE latitude IS NULL OR longitude IS NULL OR
                  latitude < -3.0 OR latitude > -2.0 OR
                  longitude < 140.0 OR longitude > 141.0
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse
    }
};
