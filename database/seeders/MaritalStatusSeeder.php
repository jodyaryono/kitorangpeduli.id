<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table to ensure clean state
        DB::table('marital_statuses')->truncate();

        // Insert marital statuses with exact IDs as per Kode Kolom 7
        $statuses = [
            ['id' => 1, 'code' => '1', 'name' => 'Kawin'],
            ['id' => 2, 'code' => '2', 'name' => 'Belum Kawin'],
            ['id' => 3, 'code' => '3', 'name' => 'Cerai Hidup'],
            ['id' => 4, 'code' => '4', 'name' => 'Cerai Mati'],
        ];

        foreach ($statuses as $status) {
            DB::statement('ALTER SEQUENCE marital_statuses_id_seq RESTART WITH ' . $status['id']);
            MaritalStatus::create([
                'code' => $status['code'],
                'name' => $status['name'],
            ]);
        }

        $this->command->info('✅ Marital Statuses seeded successfully with exact IDs');
    }
}
