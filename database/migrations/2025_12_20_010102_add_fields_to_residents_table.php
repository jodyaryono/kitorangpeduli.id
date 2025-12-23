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
        Schema::table('residents', function (Blueprint $table) {
            // Add postal code
            $table->string('kode_pos', 5)->nullable()->after('rw');

            // Add foreign keys to lookup tables
            $table->foreignId('family_relation_id')->nullable()->after('status_hubungan')->constrained('family_relations')->nullOnDelete();
            $table->foreignId('marital_status_id')->nullable()->after('status_perkawinan')->constrained('marital_statuses')->nullOnDelete();
            $table->foreignId('religion_id')->nullable()->after('agama')->constrained('religions')->nullOnDelete();

            // Add family lineage fields
            $table->string('nik_ibu', 16)->nullable()->after('nik');
            $table->string('nama_ibu', 100)->nullable()->after('nik_ibu');
            $table->string('nama_ayah', 100)->nullable()->after('nama_ibu');

            // Add birth certificate and health insurance
            $table->string('no_akta_lahir', 50)->nullable()->after('tanggal_lahir');
            $table->string('bpjs_number', 20)->nullable()->after('phone');

            // Add disability status
            $table->string('status_disabilitas', 100)->nullable()->after('kewarganegaraan');

            // Add audit trail
            $table->foreignId('updated_by_user_id')->nullable()->after('verification_notes')->constrained('users')->nullOnDelete();
        });

        // Make nik nullable and add partial unique index
        Schema::table('residents', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->change();
        });

        // Add partial unique index for non-null nik values
        DB::statement('CREATE UNIQUE INDEX residents_nik_unique ON residents (nik) WHERE nik IS NOT NULL');

        // Drop old text columns that are replaced by foreign keys
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn(['agama', 'status_perkawinan', 'status_hubungan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            // Restore old text columns
            $table->string('agama', 20)->nullable();
            $table->string('status_perkawinan', 20)->nullable();
            $table->string('status_hubungan', 30)->nullable();
        });

        DB::statement('DROP INDEX IF EXISTS residents_nik_unique');

        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeign(['family_relation_id']);
            $table->dropForeign(['marital_status_id']);
            $table->dropForeign(['religion_id']);
            $table->dropForeign(['updated_by_user_id']);

            $table->dropColumn([
                'kode_pos',
                'family_relation_id',
                'marital_status_id',
                'religion_id',
                'nik_ibu',
                'nama_ibu',
                'nama_ayah',
                'no_akta_lahir',
                'bpjs_number',
                'status_disabilitas',
                'updated_by_user_id'
            ]);
        });
    }
};
