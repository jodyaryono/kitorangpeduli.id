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
        Schema::table('answers', function (Blueprint $table) {
            $table->foreignId('family_response_id')->nullable()->after('response_id')->constrained('family_responses')->cascadeOnDelete();
            $table->foreignId('resident_id')->nullable()->after('family_response_id')->constrained('residents')->nullOnDelete();
            $table->softDeletes()->after('answered_at');
        });

        // Add check constraint to ensure exactly one response type
        DB::statement(
            'ALTER TABLE answers ADD CONSTRAINT answers_response_type_check CHECK ('
            . '(response_id IS NOT NULL AND family_response_id IS NULL) OR '
            . '(response_id IS NULL AND family_response_id IS NOT NULL)'
            . ')'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE answers DROP CONSTRAINT IF EXISTS answers_response_type_check');

        Schema::table('answers', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['family_response_id']);
            $table->dropForeign(['resident_id']);
            $table->dropColumn(['family_response_id', 'resident_id']);
        });
    }
};
