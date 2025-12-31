<?php

namespace Database\Seeders;

use App\Models\HealthQuestion;
use App\Models\HealthQuestionCategory;
use App\Models\HealthQuestionOption;
use App\Models\HealthQuestionTableRow;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk Kategori II.B: Ibu Melahirkan
 * 6 pertanyaan untuk WANITA USIA PRODUKTIF yang sudah pernah melahirkan
 */
class HealthQuestions_IbuMelahirkan_Seeder extends Seeder
{
    public function run(): void
    {
        $category = HealthQuestionCategory::where('code', 'ibu_melahirkan')->first();

        if (!$category) {
            $this->command->error('Category Ibu Melahirkan not found! Run main seeder first.');
            return;
        }

        $questions = $this->getQuestions();

        foreach ($questions as $qData) {
            $options = $qData['options'] ?? [];
            $tableRows = $qData['table_rows'] ?? [];
            unset($qData['options'], $qData['table_rows']);

            $qData['category_id'] = $category->id;

            $question = HealthQuestion::updateOrCreate(
                ['code' => $qData['code']],
                $qData
            );

            // Seed options
            foreach ($options as $index => $opt) {
                HealthQuestionOption::updateOrCreate(
                    ['question_id' => $question->id, 'value' => $opt['value']],
                    array_merge($opt, ['question_id' => $question->id, 'order' => $index + 1])
                );
            }

            // Seed table rows
            foreach ($tableRows as $index => $row) {
                HealthQuestionTableRow::updateOrCreate(
                    ['question_id' => $question->id, 'row_code' => $row['row_code']],
                    array_merge($row, ['question_id' => $question->id, 'order' => $index + 1])
                );
            }
        }
    }

    private function getQuestions(): array
    {
        return [
            // ========== BAGIAN II.B: IBU MELAHIRKAN ==========
            [
                'code' => 'ibu_melahirkan_1',
                'question_text' => 'Apakah Ibu mengalami kelahiran dalam 42 hari terakhir?',
                'question_note' => '(untuk Wanita Usia Produktif)',
                'input_type' => 'radio',
                'order' => 1,
                'is_required' => true,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ibu_melahirkan_2',
                'question_text' => 'Bila menjawab ya pada pertanyaan nomor 1, dimana tempat melahirkan?',
                'question_note' => null,
                'input_type' => 'radio',
                'order' => 2,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_melahirkan_1', 'depends_value' => '1'],
                'options' => [
                    ['value' => '1', 'label' => 'Fasilitas Kesehatan (Puskesmas, RS, Klinik, dll)'],
                    ['value' => '2', 'label' => 'Non Fasilitas Kesehatan (Rumah, dll)'],
                ],
            ],
            [
                'code' => 'ibu_melahirkan_3',
                'question_text' => 'Siapa penolong persalinan terakhir?',
                'question_note' => null,
                'input_type' => 'radio',
                'order' => 3,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_melahirkan_1', 'depends_value' => '1'],
                'options' => [
                    ['value' => '1', 'label' => 'Tenaga Kesehatan (Dokter, Bidan, Perawat)'],
                    ['value' => '2', 'label' => 'Bukan Tenaga Kesehatan (Dukun, Keluarga, dll)'],
                ],
            ],
            [
                'code' => 'ibu_melahirkan_4',
                'question_text' => 'Bagaimana jenis kelahiran terakhir?',
                'question_note' => null,
                'input_type' => 'radio',
                'order' => 4,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_melahirkan_1', 'depends_value' => '1'],
                'options' => [
                    ['value' => '1', 'label' => 'Normal'],
                    ['value' => '2', 'label' => 'Caesar/Operasi'],
                ],
            ],
            [
                'code' => 'ibu_melahirkan_5',
                'question_text' => 'Apakah sudah melakukan kunjungan nifas?',
                'question_note' => 'Kunjungan nifas dilakukan minimal 4 kali selama 42 hari setelah melahirkan',
                'input_type' => 'radio',
                'order' => 5,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_melahirkan_1', 'depends_value' => '1'],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ibu_melahirkan_6',
                'question_text' => 'Jika menjawab Ya pada nomor 5, Berapa kali sudah melakukan kunjungan nifas?',
                'question_note' => 'KN1 (6 jam - 2 hari), KN2 (3-7 hari), KN3 (8-28 hari), KN4 (29-42 hari)',
                'input_type' => 'number',
                'order' => 6,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_melahirkan_5', 'depends_value' => '1'],
                'validation_rules' => ['min' => 1, 'max' => 10],
                'settings' => ['unit' => 'kali'],
            ],
        ];
    }
}
