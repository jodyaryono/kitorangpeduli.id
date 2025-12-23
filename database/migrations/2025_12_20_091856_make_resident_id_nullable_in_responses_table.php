<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            // Drop existing constraints
            DB::statement('ALTER TABLE responses DROP CONSTRAINT IF EXISTS responses_questionnaire_id_respondent_id_unique');
            DB::statement('ALTER TABLE responses DROP CONSTRAINT IF EXISTS responses_resident_id_foreign');
        });

        // Make resident_id nullable using raw SQL
        DB::statement('ALTER TABLE responses ALTER COLUMN resident_id DROP NOT NULL');

        // Re-add foreign key
        Schema::table('responses', function (Blueprint $table) {
            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropForeign(['resident_id']);
        });

        // Make resident_id NOT NULL again
        DB::statement('ALTER TABLE responses ALTER COLUMN resident_id SET NOT NULL');

        // Re-add constraints
        Schema::table('responses', function (Blueprint $table) {
            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
            $table->unique(['questionnaire_id', 'resident_id']);
        });
    }
};
