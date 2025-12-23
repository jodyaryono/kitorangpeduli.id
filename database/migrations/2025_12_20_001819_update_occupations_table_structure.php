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
        // Truncate existing data
        DB::table('occupations')->truncate();

        // Drop old column if exists
        Schema::table('occupations', function (Blueprint $table) {
            if (Schema::hasColumn('occupations', 'occupation')) {
                $table->dropColumn('occupation');
            }
        });

        // Add new columns
        Schema::table('occupations', function (Blueprint $table) {
            if (!Schema::hasColumn('occupations', 'name')) {
                $table->string('name', 100)->after('id');
            }
            if (!Schema::hasColumn('occupations', 'code')) {
                $table->string('code', 2)->unique()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('occupations', function (Blueprint $table) {
            $table->dropColumn(['name', 'code']);
            $table->string('occupation', 50)->nullable();
        });
    }
};
