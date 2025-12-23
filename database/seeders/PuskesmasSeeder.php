<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PuskesmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Jayapura regency ID
        $jayapuraRegency = \DB::table('regencies')->where('name', 'LIKE', '%JAYAPURA%')->first();

        if (!$jayapuraRegency) {
            $this->command->info('Jayapura regency not found, skipping Puskesmas seeder');
            return;
        }

        $puskesmasList = [
            ['code' => 'PKM001', 'name' => 'Puskesmas Abepura', 'regency_id' => $jayapuraRegency->id, 'address' => 'Abepura, Jayapura', 'phone' => '0967532001'],
            ['code' => 'PKM002', 'name' => 'Puskesmas Sentani', 'regency_id' => $jayapuraRegency->id, 'address' => 'Sentani, Jayapura', 'phone' => '0967532002'],
            ['code' => 'PKM003', 'name' => 'Puskesmas Kotaraja', 'regency_id' => $jayapuraRegency->id, 'address' => 'Kotaraja, Jayapura', 'phone' => '0967532003'],
            ['code' => 'PKM004', 'name' => 'Puskesmas Waena', 'regency_id' => $jayapuraRegency->id, 'address' => 'Waena, Jayapura', 'phone' => '0967532004'],
            ['code' => 'PKM005', 'name' => 'Puskesmas Heram', 'regency_id' => $jayapuraRegency->id, 'address' => 'Heram, Jayapura', 'phone' => '0967532005'],
        ];

        foreach ($puskesmasList as $puskesmas) {
            \DB::table('puskesmas')->insert($puskesmas);
        }
    }
}
