<?php

namespace Database\Seeders;

use App\Models\Religion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table to ensure clean state
        DB::table('religions')->truncate();

        // Insert religions with exact IDs as per Kode Kolom 9
        $religions = [
            ['id' => 1, 'code' => '1', 'name' => 'Islam'],
            ['id' => 2, 'code' => '2', 'name' => 'Kristen'],
            ['id' => 3, 'code' => '3', 'name' => 'Katolik'],
            ['id' => 4, 'code' => '4', 'name' => 'Hindu'],
            ['id' => 5, 'code' => '5', 'name' => 'Budha'],
            ['id' => 6, 'code' => '6', 'name' => 'Konghucu'],
        ];

        foreach ($religions as $religion) {
            DB::statement('ALTER SEQUENCE religions_id_seq RESTART WITH ' . $religion['id']);
            Religion::create([
                'code' => $religion['code'],
                'name' => $religion['name'],
            ]);
        }

        $this->command->info('✅ Religions seeded successfully with exact IDs');
    }
}
