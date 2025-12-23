<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupations = [
            ['id' => 1, 'code' => '1', 'name' => 'Tidak kerja'],
            ['id' => 2, 'code' => '2', 'name' => 'Sekolah'],
            ['id' => 3, 'code' => '3', 'name' => 'PNS/TNI/Polri/BUMN/BUMD'],
            ['id' => 4, 'code' => '4', 'name' => 'Pegawai Swasta'],
            ['id' => 5, 'code' => '5', 'name' => 'Wiraswasta/Pedagang/Jasa'],
            ['id' => 6, 'code' => '6', 'name' => 'Petani'],
            ['id' => 7, 'code' => '7', 'name' => 'Nelayan'],
            ['id' => 8, 'code' => '8', 'name' => 'Buruh'],
            ['id' => 9, 'code' => '9', 'name' => 'Lainnya'],
        ];

        foreach ($occupations as $occupation) {
            \DB::table('occupations')->insert($occupation);
        }
    }
}
