<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahSeeder extends Seeder
{
    /**
     * Seed data wilayah Indonesia dari cahyadsn/wilayah
     * Sumber: https://github.com/cahyadsn/wilayah
     */
    public function run(): void
    {
        $this->command->info('Seeding Wilayah Indonesia...');

        // Data provinsi lengkap Indonesia
        $provinces = [
            ['code' => '11', 'name' => 'ACEH'],
            ['code' => '12', 'name' => 'SUMATERA UTARA'],
            ['code' => '13', 'name' => 'SUMATERA BARAT'],
            ['code' => '14', 'name' => 'RIAU'],
            ['code' => '15', 'name' => 'JAMBI'],
            ['code' => '16', 'name' => 'SUMATERA SELATAN'],
            ['code' => '17', 'name' => 'BENGKULU'],
            ['code' => '18', 'name' => 'LAMPUNG'],
            ['code' => '19', 'name' => 'KEPULAUAN BANGKA BELITUNG'],
            ['code' => '21', 'name' => 'KEPULAUAN RIAU'],
            ['code' => '31', 'name' => 'DKI JAKARTA'],
            ['code' => '32', 'name' => 'JAWA BARAT'],
            ['code' => '33', 'name' => 'JAWA TENGAH'],
            ['code' => '34', 'name' => 'DI YOGYAKARTA'],
            ['code' => '35', 'name' => 'JAWA TIMUR'],
            ['code' => '36', 'name' => 'BANTEN'],
            ['code' => '51', 'name' => 'BALI'],
            ['code' => '52', 'name' => 'NUSA TENGGARA BARAT'],
            ['code' => '53', 'name' => 'NUSA TENGGARA TIMUR'],
            ['code' => '61', 'name' => 'KALIMANTAN BARAT'],
            ['code' => '62', 'name' => 'KALIMANTAN TENGAH'],
            ['code' => '63', 'name' => 'KALIMANTAN SELATAN'],
            ['code' => '64', 'name' => 'KALIMANTAN TIMUR'],
            ['code' => '65', 'name' => 'KALIMANTAN UTARA'],
            ['code' => '71', 'name' => 'SULAWESI UTARA'],
            ['code' => '72', 'name' => 'SULAWESI TENGAH'],
            ['code' => '73', 'name' => 'SULAWESI SELATAN'],
            ['code' => '74', 'name' => 'SULAWESI TENGGARA'],
            ['code' => '75', 'name' => 'GORONTALO'],
            ['code' => '76', 'name' => 'SULAWESI BARAT'],
            ['code' => '81', 'name' => 'MALUKU'],
            ['code' => '82', 'name' => 'MALUKU UTARA'],
            ['code' => '91', 'name' => 'PAPUA'],
            ['code' => '92', 'name' => 'PAPUA BARAT'],
            ['code' => '93', 'name' => 'PAPUA SELATAN'],
            ['code' => '94', 'name' => 'PAPUA TENGAH'],
            ['code' => '95', 'name' => 'PAPUA PEGUNUNGAN'],
            ['code' => '96', 'name' => 'PAPUA BARAT DAYA'],
        ];

        $this->command->info('Inserting provinces...');
        foreach ($provinces as $province) {
            Province::updateOrCreate(
                ['code' => $province['code']],
                ['name' => $province['name']]
            );
        }
        $this->command->info('Provinces seeded: ' . count($provinces));

        // Seed Papua region (prioritas untuk kitorangpeduli)
        $this->seedPapuaRegion();
    }

    private function seedPapuaRegion(): void
    {
        $this->command->info('Seeding Papua region in detail...');

        // Data Kabupaten/Kota di Papua (Provinsi code 91)
        $papuaRegencies = [
            ['code' => '9101', 'name' => 'KABUPATEN MERAUKE', 'province_code' => '91'],
            ['code' => '9102', 'name' => 'KABUPATEN JAYAWIJAYA', 'province_code' => '91'],
            ['code' => '9103', 'name' => 'KABUPATEN JAYAPURA', 'province_code' => '91'],
            ['code' => '9104', 'name' => 'KABUPATEN NABIRE', 'province_code' => '91'],
            ['code' => '9105', 'name' => 'KABUPATEN KEPULAUAN YAPEN', 'province_code' => '91'],
            ['code' => '9106', 'name' => 'KABUPATEN BIAK NUMFOR', 'province_code' => '91'],
            ['code' => '9108', 'name' => 'KABUPATEN PANIAI', 'province_code' => '91'],
            ['code' => '9109', 'name' => 'KABUPATEN PUNCAK JAYA', 'province_code' => '91'],
            ['code' => '9110', 'name' => 'KABUPATEN MIMIKA', 'province_code' => '91'],
            ['code' => '9111', 'name' => 'KABUPATEN BOVEN DIGOEL', 'province_code' => '91'],
            ['code' => '9112', 'name' => 'KABUPATEN MAPPI', 'province_code' => '91'],
            ['code' => '9113', 'name' => 'KABUPATEN ASMAT', 'province_code' => '91'],
            ['code' => '9114', 'name' => 'KABUPATEN YAHUKIMO', 'province_code' => '91'],
            ['code' => '9115', 'name' => 'KABUPATEN PEGUNUNGAN BINTANG', 'province_code' => '91'],
            ['code' => '9116', 'name' => 'KABUPATEN TOLIKARA', 'province_code' => '91'],
            ['code' => '9117', 'name' => 'KABUPATEN SARMI', 'province_code' => '91'],
            ['code' => '9118', 'name' => 'KABUPATEN KEEROM', 'province_code' => '91'],
            ['code' => '9119', 'name' => 'KABUPATEN WAROPEN', 'province_code' => '91'],
            ['code' => '9120', 'name' => 'KABUPATEN SUPIORI', 'province_code' => '91'],
            ['code' => '9121', 'name' => 'KABUPATEN MAMBERAMO RAYA', 'province_code' => '91'],
            ['code' => '9122', 'name' => 'KABUPATEN NDUGA', 'province_code' => '91'],
            ['code' => '9123', 'name' => 'KABUPATEN LANNY JAYA', 'province_code' => '91'],
            ['code' => '9124', 'name' => 'KABUPATEN MAMBERAMO TENGAH', 'province_code' => '91'],
            ['code' => '9125', 'name' => 'KABUPATEN YALIMO', 'province_code' => '91'],
            ['code' => '9126', 'name' => 'KABUPATEN PUNCAK', 'province_code' => '91'],
            ['code' => '9127', 'name' => 'KABUPATEN DOGIYAI', 'province_code' => '91'],
            ['code' => '9128', 'name' => 'KABUPATEN INTAN JAYA', 'province_code' => '91'],
            ['code' => '9129', 'name' => 'KABUPATEN DEIYAI', 'province_code' => '91'],
            ['code' => '9171', 'name' => 'KOTA JAYAPURA', 'province_code' => '91'],
        ];

        $province = Province::where('code', '91')->first();
        if (!$province) {
            return;
        }

        foreach ($papuaRegencies as $regency) {
            Regency::updateOrCreate(
                ['code' => $regency['code']],
                [
                    'province_id' => $province->id,
                    'name' => $regency['name'],
                ]
            );
        }
        $this->command->info('Papua regencies seeded: ' . count($papuaRegencies));

        // Seed Kota Jayapura districts dan villages (prioritas)
        $this->seedKotaJayapura();
    }

    private function seedKotaJayapura(): void
    {
        $regency = Regency::where('code', '9171')->first();
        if (!$regency) {
            return;
        }

        // Distrik/Kecamatan di Kota Jayapura
        $districts = [
            ['code' => '9171010', 'name' => 'ABEPURA'],
            ['code' => '9171011', 'name' => 'HERAM'],
            ['code' => '9171020', 'name' => 'JAYAPURA SELATAN'],
            ['code' => '9171021', 'name' => 'JAYAPURA UTARA'],
            ['code' => '9171030', 'name' => 'MUARA TAMI'],
        ];

        foreach ($districts as $district) {
            District::updateOrCreate(
                ['code' => $district['code']],
                [
                    'regency_id' => $regency->id,
                    'name' => $district['name'],
                ]
            );
        }
        $this->command->info('Kota Jayapura districts seeded: ' . count($districts));

        // Kelurahan/Kampung di Kota Jayapura
        $villages = [
            // Abepura
            ['code' => '9171010001', 'name' => 'ABEPURA', 'district_code' => '9171010'],
            ['code' => '9171010002', 'name' => 'ASANO', 'district_code' => '9171010'],
            ['code' => '9171010003', 'name' => 'KOTA BARU', 'district_code' => '9171010'],
            ['code' => '9171010004', 'name' => 'WAHNO', 'district_code' => '9171010'],
            ['code' => '9171010005', 'name' => 'YOBE', 'district_code' => '9171010'],
            ['code' => '9171010006', 'name' => 'VIM', 'district_code' => '9171010'],
            ['code' => '9171010007', 'name' => 'WAY MHOROCK', 'district_code' => '9171010'],
            ['code' => '9171010008', 'name' => 'AWIYO', 'district_code' => '9171010'],
            // Heram
            ['code' => '9171011001', 'name' => 'WAENA', 'district_code' => '9171011'],
            ['code' => '9171011002', 'name' => 'YABANSAI', 'district_code' => '9171011'],
            ['code' => '9171011003', 'name' => 'HEDAM', 'district_code' => '9171011'],
            ['code' => '9171011004', 'name' => 'WAENA SELATAN', 'district_code' => '9171011'],
            // Jayapura Selatan
            ['code' => '9171020001', 'name' => 'NUMBAY', 'district_code' => '9171020'],
            ['code' => '9171020002', 'name' => 'ENTROP', 'district_code' => '9171020'],
            ['code' => '9171020003', 'name' => 'HAMADI', 'district_code' => '9171020'],
            ['code' => '9171020004', 'name' => 'TOBATI', 'district_code' => '9171020'],
            ['code' => '9171020005', 'name' => 'ARD UJUNG', 'district_code' => '9171020'],
            ['code' => '9171020006', 'name' => 'VIM PANTAI', 'district_code' => '9171020'],
            // Jayapura Utara
            ['code' => '9171021001', 'name' => 'TANJUNG RIA', 'district_code' => '9171021'],
            ['code' => '9171021002', 'name' => 'MANDALA', 'district_code' => '9171021'],
            ['code' => '9171021003', 'name' => 'IMBI', 'district_code' => '9171021'],
            ['code' => '9171021004', 'name' => 'ANGKASAPURA', 'district_code' => '9171021'],
            ['code' => '9171021005', 'name' => 'BHAYANGKARA', 'district_code' => '9171021'],
            ['code' => '9171021006', 'name' => 'GURABESI', 'district_code' => '9171021'],
            // Muara Tami
            ['code' => '9171030001', 'name' => 'KOYA BARAT', 'district_code' => '9171030'],
            ['code' => '9171030002', 'name' => 'KOYA TIMUR', 'district_code' => '9171030'],
            ['code' => '9171030003', 'name' => 'KOYA TENGAH', 'district_code' => '9171030'],
            ['code' => '9171030004', 'name' => 'HOLTEKAMP', 'district_code' => '9171030'],
            ['code' => '9171030005', 'name' => 'SKOUW YAMBE', 'district_code' => '9171030'],
            ['code' => '9171030006', 'name' => 'SKOUW SAE', 'district_code' => '9171030'],
            ['code' => '9171030007', 'name' => 'SKOUW MABO', 'district_code' => '9171030'],
        ];

        foreach ($villages as $village) {
            $district = District::where('code', $village['district_code'])->first();
            if ($district) {
                Village::updateOrCreate(
                    ['code' => $village['code']],
                    [
                        'district_id' => $district->id,
                        'name' => $village['name'],
                    ]
                );
            }
        }
        $this->command->info('Kota Jayapura villages seeded: ' . count($villages));
    }
}
