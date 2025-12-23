<?php

namespace Database\Seeders;

use App\Models\FamilyRelation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FamilyRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table to ensure clean state
        DB::table('family_relations')->truncate();

        // Insert family relations with exact IDs as per Kode Kolom 3
        $relations = [
            ['id' => 1, 'code' => '1', 'name' => 'Kepala Keluarga'],
            ['id' => 2, 'code' => '2', 'name' => 'Istri/Suami'],
            ['id' => 3, 'code' => '3', 'name' => 'Anak'],
            ['id' => 4, 'code' => '4', 'name' => 'Menantu'],
            ['id' => 5, 'code' => '5', 'name' => 'Cucu'],
            ['id' => 6, 'code' => '6', 'name' => 'Orang Tua'],
            ['id' => 7, 'code' => '7', 'name' => 'Famili lain'],
            ['id' => 8, 'code' => '8', 'name' => 'Pembantu'],
            ['id' => 9, 'code' => '9', 'name' => 'Lainnya'],
        ];

        foreach ($relations as $relation) {
            DB::statement('ALTER SEQUENCE family_relations_id_seq RESTART WITH ' . $relation['id']);
            FamilyRelation::create([
                'code' => $relation['code'],
                'name' => $relation['name'],
            ]);
        }

        $this->command->info('✅ Family Relations seeded successfully with exact IDs');
    }
}
