<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Map education text to education_id (sesuai format data yang ada)
        $educationMapping = [
            'Tidak/Belum Sekolah' => -1,
            'SD/Sederajat' => 2,
            'SMP/Sederajat' => 3,
            'SMA/Sederajat' => 4,
            'D1/D2/D3' => 6,
            'S1/D4' => 7,
            'S2' => 8,
            'S3' => 9,
        ];

        foreach ($educationMapping as $educationText => $educationId) {
            DB::table('respondents')
                ->where('pendidikan_old', $educationText)
                ->update(['education_id' => $educationId]);
        }

        // Map occupation text to occupation_id (sesuai format data yang ada)
        $occupationMapping = [
            'Tidak Bekerja' => -1,
            'PNS' => 1,
            'Dosen' => 19,
            'Guru' => 20,
            'Dokter' => 27,
            'Bidan' => 28,
            'Perawat' => 29,
            'Tenaga Kesehatan' => 29, // map ke Perawat
            'Sopir' => 36,
            'Pedagang' => 39,
            'Wiraswasta' => 43,
            'Buruh' => 44,
            'Tukang' => 51, // map ke Tukang Batu (general)
            'TNI/Polri' => 73,
            'Pelajar' => 75,
            'Mahasiswa' => 75,
            'Ibu Rumah Tangga' => 76,
            'Karyawan Swasta' => 77,
            'Honorer' => 80,
            'Pensiunan' => 82,
            'Petani' => 88,
            'Nelayan' => 89,
            'Pengusaha' => 43, // map ke Wiraswasta
        ];

        foreach ($occupationMapping as $occupationText => $occupationId) {
            DB::table('respondents')
                ->where('pekerjaan_old', $occupationText)
                ->update(['occupation_id' => $occupationId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset occupation_id and education_id to null
        DB::table('respondents')->update([
            'occupation_id' => null,
            'education_id' => null,
        ]);
    }
};
