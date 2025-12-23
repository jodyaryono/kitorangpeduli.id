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
        // Check if respondent_id column exists
        if (!Schema::hasColumn('responses', 'respondent_id')) {
            // Already migrated, skip
            return;
        }

        // Drop constraints safely using raw SQL
        DB::statement('ALTER TABLE responses DROP CONSTRAINT IF EXISTS responses_questionnaire_id_respondent_id_unique');
        DB::statement('DROP INDEX IF EXISTS responses_respondent_id_index');
        DB::statement('ALTER TABLE responses DROP CONSTRAINT IF EXISTS responses_respondent_id_foreign');

        // Rename the column
        DB::statement('ALTER TABLE responses RENAME COLUMN respondent_id TO resident_id');

        // Re-add constraints and indexes
        Schema::table('responses', function (Blueprint $table) {
            $table->foreign('resident_id')->references('id')->on('residents')->onDelete('cascade');
            $table->index('resident_id');
            $table->unique(['questionnaire_id', 'resident_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if resident_id column exists
        if (!Schema::hasColumn('responses', 'resident_id')) {
            return;
        }

        Schema::table('responses', function (Blueprint $table) {
            $table->dropUnique(['questionnaire_id', 'resident_id']);
            $table->dropIndex(['resident_id']);
            $table->dropForeign(['resident_id']);
        });

        // Rename back
        DB::statement('ALTER TABLE responses RENAME COLUMN resident_id TO respondent_id');

        Schema::table('responses', function (Blueprint $table) {
            $table->foreign('respondent_id')->references('id')->on('respondents')->onDelete('cascade');
            $table->index('respondent_id');
            $table->unique(['questionnaire_id', 'respondent_id']);
        });
    }
};
