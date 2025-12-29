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
        Schema::table('residents', function (Blueprint $table) {
            $table->string('hubungan_keluarga')->nullable()->after('nama_lengkap');
            $table->string('agama')->nullable()->after('religion_id');
            $table->string('status_kawin')->nullable()->after('marital_status_id');
            $table->string('pekerjaan')->nullable()->after('occupation_id');
            $table->string('pendidikan')->nullable()->after('education_id');
            $table->integer('umur')->nullable()->after('tanggal_lahir');
            $table->string('ktp_kia_path')->nullable()->after('ktp_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn([
                'hubungan_keluarga',
                'agama',
                'status_kawin',
                'pekerjaan',
                'pendidikan',
                'umur',
                'ktp_kia_path',
            ]);
        });
    }
};
