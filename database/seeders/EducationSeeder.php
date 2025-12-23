<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table to ensure clean state
        DB::table('educations')->truncate();

        // Insert educations with exact IDs as per Kode Kolom 10
        $educations = [
            ['id' => 1, 'code' => '1', 'name' => 'Tidak pernah sekolah'],
            ['id' => 2, 'code' => '2', 'name' => 'Tidak tamat SD/MI'],
            ['id' => 3, 'code' => '3', 'name' => 'Tamat SD/MI'],
            ['id' => 4, 'code' => '4', 'name' => 'Tamat SLTP/MTS'],
            ['id' => 5, 'code' => '5', 'name' => 'Tamat SLTA/MA'],
            ['id' => 6, 'code' => '6', 'name' => 'Tamat D1/D2/D3'],
            ['id' => 7, 'code' => '7', 'name' => 'Tamat PT'],
        ];

        foreach ($educations as $education) {
            DB::statement("INSERT INTO educations (id, code, name) VALUES ({$education['id']}, '{$education['code']}', '{$education['name']}')");
        }

        $this->command->info('✅ Educations seeded successfully with exact IDs');
    }
}
