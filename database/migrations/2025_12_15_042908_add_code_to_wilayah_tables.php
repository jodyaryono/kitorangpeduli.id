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
        // Add code column to provinces (same as id for backward compatibility)
        Schema::table('provinces', function (Blueprint $table) {
            $table->char('code', 2)->after('id')->nullable();
            $table->index('code');
        });

        // Update existing data: set code = id
        DB::statement('UPDATE provinces SET code = id WHERE code IS NULL');

        // Make code non-nullable after data update
        Schema::table('provinces', function (Blueprint $table) {
            $table->char('code', 2)->nullable(false)->change();
        });

        // Add code column to regencies
        Schema::table('regencies', function (Blueprint $table) {
            $table->char('code', 4)->after('id')->nullable();
            $table->unique('code');
        });

        // Update existing regencies: set code based on id (formatted as 4 digits)
        DB::statement("UPDATE regencies SET code = LPAD(id::text, 4, '0') WHERE code IS NULL");

        // Add code column to districts
        Schema::table('districts', function (Blueprint $table) {
            $table->char('code', 7)->after('id')->nullable();
            $table->index('code');
        });

        // Update existing districts: set code based on id (formatted as 7 digits)
        DB::statement("UPDATE districts SET code = LPAD(id::text, 7, '0') WHERE code IS NULL");

        // Add code column to villages
        Schema::table('villages', function (Blueprint $table) {
            $table->char('code', 10)->after('id')->nullable();
            $table->index('code');
        });

        // Update existing villages: set code based on id (formatted as 10 digits)
        DB::statement("UPDATE villages SET code = LPAD(id::text, 10, '0') WHERE code IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('regencies', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
