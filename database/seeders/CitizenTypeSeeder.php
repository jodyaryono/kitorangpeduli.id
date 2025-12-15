<?php

namespace Database\Seeders;

use App\Models\CitizenType;
use Illuminate\Database\Seeder;

class CitizenTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'OAP',
                'name' => 'Orang Asli Papua',
                'description' => 'Penduduk asli Papua yang merupakan keturunan dari suku-suku asli Papua',
                'is_active' => true,
            ],
            [
                'code' => 'PORTNUMBAY',
                'name' => 'Port Numbay',
                'description' => 'Penduduk asli Port Numbay (Jayapura) yang merupakan suku asli Teluk Numbay',
                'is_active' => true,
            ],
            [
                'code' => 'WNA',
                'name' => 'Warga Negara Asing',
                'description' => 'Warga negara asing yang tinggal dan berdomisili di wilayah Papua',
                'is_active' => true,
            ],
            [
                'code' => 'PENDATANG',
                'name' => 'Pendatang',
                'description' => 'Warga pendatang dari luar Papua yang berdomisili di wilayah Papua',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            CitizenType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
