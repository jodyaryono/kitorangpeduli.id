<?php

namespace Database\Seeders;

use App\Models\HealthQuestion;
use App\Models\HealthQuestionCategory;
use App\Models\HealthQuestionOption;
use App\Models\HealthQuestionTableRow;
use Illuminate\Database\Seeder;

class HealthQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Memanggil seeder per kategori secara modular
     */
    public function run(): void
    {
        $this->command->info('Seeding Health Question Categories...');
        $this->seedCategories();

        $this->command->info('Seeding I. Penyakit Menular & Tidak Menular...');
        $this->call(HealthQuestions_PTM_Seeder::class);

        $this->command->info('Seeding II.A. Ibu Hamil...');
        $this->call(HealthQuestions_IbuHamil_Seeder::class);

        $this->command->info('Seeding II.B. Ibu Melahirkan...');
        $this->call(HealthQuestions_IbuMelahirkan_Seeder::class);

        $this->command->info('Seeding II.C. Bayi 0-11 bulan...');
        $this->call(HealthQuestions_Bayi_Seeder::class);

        $this->command->info('✅ All Health Questions seeded successfully!');
    }

    /**
     * Seed kategori pertanyaan
     */
    private function seedCategories(): void
    {
        $categories = [
            [
                'code' => 'ptm',
                'name' => 'I. Pertanyaan tentang Penyakit Menular dan Tidak Menular',
                'description' => 'Pertanyaan untuk semua golongan umur tentang penyakit menular dan tidak menular',
                'order' => 1,
                'target_criteria' => ['min_age' => 0, 'max_age' => 999, 'gender' => 'all'],
            ],
            [
                'code' => 'ibu_hamil',
                'name' => 'II.A. Pertanyaan untuk Ibu SEDANG Hamil',
                'description' => 'Ditanyakan bila dalam keluarga terdapat ibu sedang hamil',
                'order' => 2,
                'target_criteria' => ['min_age' => 15, 'max_age' => 49, 'gender' => '2'],  // Perempuan
            ],
            [
                'code' => 'ibu_melahirkan',
                'name' => 'II.B. Pertanyaan untuk Ibu Melahirkan',
                'description' => 'Ditanyakan bila dalam keluarga terdapat ibu melahirkan (dalam kurun waktu < 12 bulan)',
                'order' => 3,
                'target_criteria' => ['min_age' => 15, 'max_age' => 49, 'gender' => '2'],
            ],
            [
                'code' => 'bayi',
                'name' => 'II.C. Pertanyaan untuk Bayi (0-11 bulan)',
                'description' => 'Ditanyakan bila dalam keluarga terdapat bayi usia 0-11 bulan',
                'order' => 4,
                'target_criteria' => ['min_age' => 0, 'max_age' => 0, 'gender' => 'all'],
            ],
        ];

        foreach ($categories as $category) {
            HealthQuestionCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }
    }
}
