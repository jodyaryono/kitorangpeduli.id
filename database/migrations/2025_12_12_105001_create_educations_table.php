<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('educations', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('education', 50)->nullable();
        });

        // Insert data from SQL file
        DB::table('educations')->insert([
            ['id' => -1, 'education' => 'TIDAK/BELUM SEKOLAH'],
            ['id' => 1, 'education' => 'TAMAT SD/SEDERAJAT'],
            ['id' => 2, 'education' => 'BELUM TAMAT SD/SEDERAJAT'],
            ['id' => 3, 'education' => 'SLTP/SEDERAJAT'],
            ['id' => 4, 'education' => 'SLTA/SEDERAJAT'],
            ['id' => 5, 'education' => 'DIPLOMA I/II'],
            ['id' => 6, 'education' => 'AKADEMI/DIPLOMA III/S. MUDA'],
            ['id' => 7, 'education' => 'DIPLOMA IV/STRATA I'],
            ['id' => 8, 'education' => 'STRATA II'],
            ['id' => 9, 'education' => 'STRATA III'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};
