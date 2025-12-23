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
        Schema::table('families', function (Blueprint $table) {
            // Add puskesmas reference
            $table->foreignId('puskesmas_id')->nullable()->after('village_id')->constrained('puskesmas')->nullOnDelete();

            // Add building number
            $table->string('no_bangunan', 10)->nullable()->after('rw');

            // Add audit trail
            $table->foreignId('updated_by_user_id')->nullable()->after('verification_notes')->constrained('users')->nullOnDelete();
        });

        // Make no_kk nullable and add partial unique index
        Schema::table('families', function (Blueprint $table) {
            $table->string('no_kk', 16)->nullable()->change();
        });

        // Add partial unique index for non-null no_kk values
        DB::statement('CREATE UNIQUE INDEX families_no_kk_unique ON families (no_kk) WHERE no_kk IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS families_no_kk_unique');

        Schema::table('families', function (Blueprint $table) {
            $table->dropForeign(['puskesmas_id']);
            $table->dropColumn(['puskesmas_id', 'no_bangunan', 'updated_by_user_id']);
        });
    }
};
