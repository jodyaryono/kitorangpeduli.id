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
        DB::table('educations')->truncate();

        // Drop old column if exists
        Schema::table('educations', function (Blueprint $table) {
            if (Schema::hasColumn('educations', 'education')) {
                $table->dropColumn('education');
            }
        });

        // Add new columns
        Schema::table('educations', function (Blueprint $table) {
            if (!Schema::hasColumn('educations', 'name')) {
                $table->string('name', 50)->after('id');
            }
            if (!Schema::hasColumn('educations', 'code')) {
                $table->string('code', 2)->unique()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('educations', function (Blueprint $table) {
            $table->dropColumn(['name', 'code']);
            $table->string('education', 50)->nullable();
        });
    }
};
