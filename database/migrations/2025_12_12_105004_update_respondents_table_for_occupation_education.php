<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            // Rename old columns to backup
            $table->renameColumn('pekerjaan', 'pekerjaan_old');
            $table->renameColumn('pendidikan', 'pendidikan_old');
        });

        Schema::table('respondents', function (Blueprint $table) {
            // Add new foreign key columns
            $table->integer('occupation_id')->nullable()->after('kewarganegaraan');
            $table->integer('education_id')->nullable()->after('occupation_id');

            // Add foreign key constraints
            $table->foreign('occupation_id')->references('id')->on('occupations')->onDelete('set null');
            $table->foreign('education_id')->references('id')->on('educations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['occupation_id']);
            $table->dropForeign(['education_id']);

            // Drop new columns
            $table->dropColumn(['occupation_id', 'education_id']);

            // Rename back
            $table->renameColumn('pekerjaan_old', 'pekerjaan');
            $table->renameColumn('pendidikan_old', 'pendidikan');
        });
    }
};
