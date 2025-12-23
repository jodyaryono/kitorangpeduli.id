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
        // First, truncate data from respondents and families tables
        DB::table('responses')->truncate();
        DB::table('respondents')->truncate();
        DB::table('kartu_keluarga')->truncate();

        // First, rename kartu_keluarga to families (as respondents depends on it)
        Schema::rename('kartu_keluarga', 'families');

        // Then rename respondents to residents
        Schema::rename('respondents', 'residents');

        // Get the constraint name for kartu_keluarga_id foreign key
        $constraintName = DB::select("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'residents'
            AND constraint_type = 'FOREIGN KEY'
            AND constraint_name LIKE '%kartu_keluarga_id%'
        ");

        // Drop the foreign key constraint if it exists
        if (!empty($constraintName)) {
            DB::statement('ALTER TABLE residents DROP CONSTRAINT IF EXISTS ' . $constraintName[0]->constraint_name);
        }

        // Rename the column
        Schema::table('residents', function (Blueprint $table) {
            $table->renameColumn('kartu_keluarga_id', 'family_id');
        });

        // Add new foreign key
        Schema::table('residents', function (Blueprint $table) {
            $table->foreign('family_id')->references('id')->on('families')->nullOnDelete();
        });

        // Update responses table
        if (Schema::hasTable('responses') && Schema::hasColumn('responses', 'respondent_id')) {
            // Get the constraint name for respondent_id foreign key
            $responseConstraint = DB::select("
                SELECT constraint_name
                FROM information_schema.table_constraints
                WHERE table_name = 'responses'
                AND constraint_type = 'FOREIGN KEY'
                AND constraint_name LIKE '%respondent_id%'
            ");

            // Drop the foreign key constraint if it exists
            if (!empty($responseConstraint)) {
                DB::statement('ALTER TABLE responses DROP CONSTRAINT IF EXISTS ' . $responseConstraint[0]->constraint_name);
            }

            // Rename the column
            Schema::table('responses', function (Blueprint $table) {
                $table->renameColumn('respondent_id', 'resident_id');
            });

            // Add new foreign key
            Schema::table('responses', function (Blueprint $table) {
                $table->foreign('resident_id')->references('id')->on('residents')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the responses table changes
        if (Schema::hasTable('responses') && Schema::hasColumn('responses', 'resident_id')) {
            $responseConstraint = DB::select("
                SELECT constraint_name
                FROM information_schema.table_constraints
                WHERE table_name = 'responses'
                AND constraint_type = 'FOREIGN KEY'
                AND constraint_name LIKE '%resident_id%'
            ");

            if (!empty($responseConstraint)) {
                DB::statement('ALTER TABLE responses DROP CONSTRAINT IF EXISTS ' . $responseConstraint[0]->constraint_name);
            }

            Schema::table('responses', function (Blueprint $table) {
                $table->renameColumn('resident_id', 'respondent_id');
            });

            Schema::table('responses', function (Blueprint $table) {
                $table->foreign('respondent_id')->references('id')->on('respondents')->cascadeOnDelete();
            });
        }

        // Reverse the residents table changes
        $familyConstraint = DB::select("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'residents'
            AND constraint_type = 'FOREIGN KEY'
            AND constraint_name LIKE '%family_id%'
        ");

        if (!empty($familyConstraint)) {
            DB::statement('ALTER TABLE residents DROP CONSTRAINT IF EXISTS ' . $familyConstraint[0]->constraint_name);
        }

        Schema::table('residents', function (Blueprint $table) {
            $table->renameColumn('family_id', 'kartu_keluarga_id');
        });

        Schema::table('residents', function (Blueprint $table) {
            $table->foreign('kartu_keluarga_id')->references('id')->on('kartu_keluarga')->nullOnDelete();
        });

        // Rename tables back
        Schema::rename('residents', 'respondents');
        Schema::rename('families', 'kartu_keluarga');
    }
};
