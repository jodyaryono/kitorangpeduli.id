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
        Schema::table('question_options', function (Blueprint $table) {
            $table->string('option_value')->nullable()->after('option_text');
        });

        // Auto-fill option_value dari option_text untuk data existing
        DB::statement("UPDATE question_options SET option_value = LOWER(REPLACE(option_text, ' ', '_')) WHERE option_value IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn('option_value');
        });
    }
};
