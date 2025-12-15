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
        Schema::create('occupations', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('occupation', 50)->nullable();
        });

        // Insert data from SQL file
        $occupations = [
            ['id' => -1, 'occupation' => 'BELUM/TIDAK BEKERJA'],
            ['id' => 0, 'occupation' => 'AUTOMOTIF'],
            ['id' => 1, 'occupation' => 'MENGURUS RUMAH TANGGA'],
            ['id' => 2, 'occupation' => 'PELAJAR/MAHASISWA'],
            ['id' => 3, 'occupation' => 'PENSIUNAN'],
            ['id' => 4, 'occupation' => 'PEGAWAI NEGERI SIPIL'],
            ['id' => 5, 'occupation' => 'TENTARA NASIONAL INDONESIA'],
            ['id' => 6, 'occupation' => 'KEPOLISIAN RI'],
            ['id' => 7, 'occupation' => 'PERDAGANGAN'],
            ['id' => 8, 'occupation' => 'PETANI/PEKEBUN'],
            ['id' => 9, 'occupation' => 'PETERNAK'],
            ['id' => 10, 'occupation' => 'NELAYAN/PERIKANAN'],
            ['id' => 11, 'occupation' => 'INDUSTRI'],
            ['id' => 12, 'occupation' => 'KONSTRUKSI'],
            ['id' => 13, 'occupation' => 'TRANSPORTASI'],
            ['id' => 14, 'occupation' => 'KARYAWAN SWASTA'],
            ['id' => 15, 'occupation' => 'KARYAWAN BUMN'],
            ['id' => 16, 'occupation' => 'KARYAWAN BUMD'],
            ['id' => 17, 'occupation' => 'KARYAWAN HONORER'],
            ['id' => 18, 'occupation' => 'BURUH HARIAN LEPAS'],
            ['id' => 19, 'occupation' => 'BURUH TANI/PERKEBUNAN'],
            ['id' => 20, 'occupation' => 'BURUH NELAYAN/PERIKANAN'],
            ['id' => 21, 'occupation' => 'BURUH PETERNAKAN'],
            ['id' => 22, 'occupation' => 'PEMBANTU RUMAH TANGGA'],
            ['id' => 23, 'occupation' => 'TUKANG CUKUR'],
            ['id' => 24, 'occupation' => 'TUKANG LISTRIK'],
            ['id' => 25, 'occupation' => 'TUKANG BATU'],
            ['id' => 26, 'occupation' => 'TUKANG KAYU'],
            ['id' => 27, 'occupation' => 'TUKANG SOL SEPATU'],
            ['id' => 28, 'occupation' => 'TUKANG LAS / PANDAI BESI'],
            ['id' => 29, 'occupation' => 'TUKANG JAHIT'],
            ['id' => 30, 'occupation' => 'PENATA RAMBUT'],
            ['id' => 31, 'occupation' => 'PENATA RIAS'],
            ['id' => 32, 'occupation' => 'PENATA BUSANA'],
            ['id' => 33, 'occupation' => 'MEKANIK'],
            ['id' => 34, 'occupation' => 'TUKANG GIGI'],
            ['id' => 35, 'occupation' => 'SENIMAN'],
            ['id' => 36, 'occupation' => 'TABIB'],
            ['id' => 37, 'occupation' => 'PARAJI'],
            ['id' => 38, 'occupation' => 'PERANCANG BUSANA'],
            ['id' => 39, 'occupation' => 'PENTERJEMAH'],
            ['id' => 40, 'occupation' => 'IMAM MESJID'],
            ['id' => 41, 'occupation' => 'PENDETA'],
            ['id' => 42, 'occupation' => 'PASTOR'],
            ['id' => 43, 'occupation' => 'WARTAWAN'],
            ['id' => 44, 'occupation' => 'USTADZ/MUBALIGH'],
            ['id' => 45, 'occupation' => 'JURU MASAK'],
            ['id' => 46, 'occupation' => 'PROMOTOR ACARA'],
            ['id' => 47, 'occupation' => 'ANGGOTA DPR-RI'],
            ['id' => 48, 'occupation' => 'ANGGOTA DPD'],
            ['id' => 49, 'occupation' => 'ANGGOTA BPK'],
            ['id' => 50, 'occupation' => 'PRESIDEN'],
            ['id' => 51, 'occupation' => 'WAKIL PRESIDEN'],
            ['id' => 52, 'occupation' => 'ANGGOTA MAHKAMAH KONSTITUSI'],
            ['id' => 53, 'occupation' => 'ANGGOTA KABINET / KEMENTERIAN'],
            ['id' => 54, 'occupation' => 'DUTA BESAR'],
            ['id' => 55, 'occupation' => 'GUBERNUR'],
            ['id' => 56, 'occupation' => 'WAKIL GUBERNUR'],
            ['id' => 57, 'occupation' => 'BUPATI'],
            ['id' => 58, 'occupation' => 'WAKIL BUPATI'],
            ['id' => 59, 'occupation' => 'WALIKOTA'],
            ['id' => 60, 'occupation' => 'WAKIL WALIKOTA'],
            ['id' => 61, 'occupation' => 'ANGGOTA DPRD PROVINSI'],
            ['id' => 62, 'occupation' => 'ANGGOTA DPRD KABUPATEN/KOTA'],
            ['id' => 63, 'occupation' => 'DOSEN'],
            ['id' => 64, 'occupation' => 'GURU'],
            ['id' => 65, 'occupation' => 'PILOT'],
            ['id' => 66, 'occupation' => 'PENGACARA'],
            ['id' => 67, 'occupation' => 'NOTARIS'],
            ['id' => 68, 'occupation' => 'ARSITEK'],
            ['id' => 69, 'occupation' => 'AKUNTAN'],
            ['id' => 70, 'occupation' => 'KONSULTAN'],
            ['id' => 71, 'occupation' => 'DOKTER'],
            ['id' => 72, 'occupation' => 'BIDAN'],
            ['id' => 73, 'occupation' => 'PERAWAT'],
            ['id' => 74, 'occupation' => 'APOTEKER'],
            ['id' => 75, 'occupation' => 'PSIKIATER / PSIKOLOG'],
            ['id' => 76, 'occupation' => 'PENYIAR TELEVISI'],
            ['id' => 77, 'occupation' => 'PENYIAR RADIO'],
            ['id' => 78, 'occupation' => 'PELAUT'],
            ['id' => 79, 'occupation' => 'PENELITI'],
            ['id' => 80, 'occupation' => 'SOPIR'],
            ['id' => 81, 'occupation' => 'PIALANG'],
            ['id' => 82, 'occupation' => 'PARANORMAL'],
            ['id' => 83, 'occupation' => 'PEDAGANG'],
            ['id' => 84, 'occupation' => 'PERANGKAT DESA'],
            ['id' => 85, 'occupation' => 'KEPALA DESA'],
            ['id' => 86, 'occupation' => 'BIARAWATI'],
            ['id' => 87, 'occupation' => 'WIRASWASTA'],
            ['id' => 88, 'occupation' => 'LAINNYA'],
            ['id' => 89, 'occupation' => 'TUKANG OJEK'],
            ['id' => 90, 'occupation' => 'JURU PARKIR'],
        ];

        DB::table('occupations')->insert($occupations);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupations');
    }
};
