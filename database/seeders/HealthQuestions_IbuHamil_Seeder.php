<?php

namespace Database\Seeders;

use App\Models\HealthQuestion;
use App\Models\HealthQuestionCategory;
use App\Models\HealthQuestionOption;
use App\Models\HealthQuestionTableRow;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk Kategori II.A: Ibu Hamil
 * 21 pertanyaan untuk WANITA USIA PRODUKTIF dengan status sedang hamil
 */
class HealthQuestions_IbuHamil_Seeder extends Seeder
{
    public function run(): void
    {
        $category = HealthQuestionCategory::where('code', 'ibu_hamil')->first();

        if (!$category) {
            $this->command->error('Category Ibu Hamil not found! Run main seeder first.');
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
            // ========== BAGIAN AWAL: STATUS KEHAMILAN ==========
            [
                'code' => 'ibu_hamil_1',
                'question_text' => 'Apakah Ibu saat ini sedang Hamil?',
                'question_note' => '(Diisi bila responden adalah wanita dan usia produktif)',
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
                'code' => 'ibu_hamil_2',
                'question_text' => 'Apakah pernah dilakukan periksa Hb?',
                'question_note' => '(Jika pertanyaan nomor 1 Ya, maka lanjut pertanyaan 2)',
                'input_type' => 'radio',
                'order' => 2,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ibu_hamil_3',
                'question_text' => 'Jika pernah, berapa hasil pemeriksaan Hb terakhir?',
                'question_note' => 'Normal: Trimester I: 11,6-13,9 g/dl; Trimester II: 9,7-14,8 g/dl; Trimester III: 9,5-15 g/dl',
                'input_type' => 'number',
                'order' => 3,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_2', 'depends_value' => '1'],
                'validation_rules' => ['min' => 1, 'max' => 20, 'decimal' => 1],
                'settings' => ['unit' => 'g/dl'],
            ],
            [
                'code' => 'ibu_hamil_4',
                'question_text' => 'Apakah ibu mengalami 4T? (Jawaban boleh lebih dari 1)',
                'question_note' => '4T = Terlalu tua (>35th), Terlalu muda (<20th), Terlalu banyak anak (>3), Terlalu dekat jarak kehamilan (<2th)',
                'input_type' => 'checkbox',
                'order' => 4,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'options' => [
                    ['value' => 'terlalu_tua', 'label' => 'Terlalu tua (>35 Tahun)'],
                    ['value' => 'terlalu_muda', 'label' => 'Terlalu muda (<20 Tahun)'],
                    ['value' => 'terlalu_banyak', 'label' => 'Terlalu banyak anak (>3)'],
                    ['value' => 'terlalu_dekat', 'label' => 'Terlalu dekat jarak kehamilan (<2 Tahun)'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak mengalami 4T'],
                ],
            ],
            [
                'code' => 'ibu_hamil_5',
                'question_text' => 'Berapa hasil pengukuran LILA (Lingkar Lengan Atas)?',
                'question_note' => 'KEK (Kekurangan Energi Kronis) jika LILA < 23,5 cm',
                'input_type' => 'number',
                'order' => 5,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'validation_rules' => ['min' => 10, 'max' => 50, 'decimal' => 1],
                'settings' => ['unit' => 'cm'],
            ],
            [
                'code' => 'ibu_hamil_6',
                'question_text' => 'Berapa hasil pengukuran IMT sebelum hamil?',
                'question_note' => 'IMT = Berat Badan (kg) / [Tinggi Badan (m)]². Normal: 18,5-24,9',
                'input_type' => 'number',
                'order' => 6,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'validation_rules' => ['min' => 10, 'max' => 50, 'decimal' => 1],
                'settings' => ['unit' => 'kg/m²'],
            ],
            // ========== SRQ-20 (Self Reporting Questionnaire) - 20 Pertanyaan Kesehatan Mental ==========
            [
                'code' => 'ibu_hamil_srq_intro',
                'question_text' => 'DETEKSI DINI KESEHATAN JIWA IBU HAMIL (SRQ-20)',
                'question_note' => 'Berikut ini ada beberapa pertanyaan yang berhubungan dengan keluhan yang mungkin Anda alami. Pertanyaan dijawab berdasarkan kondisi dalam 30 hari terakhir.',
                'input_type' => 'info',
                'order' => 7,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
            ],
            [
                'code' => 'srq_1',
                'question_text' => 'Apakah Anda sering menderita sakit kepala?',
                'input_type' => 'radio',
                'order' => 8,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_2',
                'question_text' => 'Apakah nafsu makan Anda kurang?',
                'input_type' => 'radio',
                'order' => 9,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_3',
                'question_text' => 'Apakah tidur Anda tidak nyenyak?',
                'input_type' => 'radio',
                'order' => 10,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_4',
                'question_text' => 'Apakah Anda mudah merasa takut?',
                'input_type' => 'radio',
                'order' => 11,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_5',
                'question_text' => 'Apakah Anda merasa cemas, tegang atau khawatir?',
                'input_type' => 'radio',
                'order' => 12,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_6',
                'question_text' => 'Apakah tangan Anda gemetar?',
                'input_type' => 'radio',
                'order' => 13,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_7',
                'question_text' => 'Apakah Anda mengalami gangguan pencernaan?',
                'input_type' => 'radio',
                'order' => 14,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_8',
                'question_text' => 'Apakah Anda merasa sulit untuk berpikir dengan jernih?',
                'input_type' => 'radio',
                'order' => 15,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_9',
                'question_text' => 'Apakah Anda merasa tidak bahagia?',
                'input_type' => 'radio',
                'order' => 16,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_10',
                'question_text' => 'Apakah Anda menangis lebih sering dari biasanya?',
                'input_type' => 'radio',
                'order' => 17,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_11',
                'question_text' => 'Apakah Anda merasa sulit untuk menikmati kegiatan sehari-hari?',
                'input_type' => 'radio',
                'order' => 18,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_12',
                'question_text' => 'Apakah Anda sulit untuk mengambil keputusan?',
                'input_type' => 'radio',
                'order' => 19,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_13',
                'question_text' => 'Apakah aktivitas/pekerjaan sehari-hari Anda terganggu?',
                'input_type' => 'radio',
                'order' => 20,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_14',
                'question_text' => 'Apakah Anda tidak mampu untuk berperan di dalam kehidupan ini?',
                'input_type' => 'radio',
                'order' => 21,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_15',
                'question_text' => 'Apakah Anda kehilangan minat terhadap berbagai hal?',
                'input_type' => 'radio',
                'order' => 22,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_16',
                'question_text' => 'Apakah Anda merasa tidak berharga/berguna?',
                'input_type' => 'radio',
                'order' => 23,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_17',
                'question_text' => 'Apakah Anda mempunyai pikiran untuk mengakhiri hidup Anda?',
                'input_type' => 'radio',
                'order' => 24,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1, 'is_critical' => true],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_18',
                'question_text' => 'Apakah Anda merasa lelah sepanjang waktu?',
                'input_type' => 'radio',
                'order' => 25,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_19',
                'question_text' => 'Apakah Anda mengalami rasa tidak enak di perut?',
                'input_type' => 'radio',
                'order' => 26,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_20',
                'question_text' => 'Apakah Anda mudah lelah?',
                'input_type' => 'radio',
                'order' => 27,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['group' => 'srq20', 'score_if_yes' => 1],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '0', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'srq_score',
                'question_text' => 'Hasil Skor SRQ-20',
                'question_note' => 'Dihitung otomatis. Skor ≥ 6 = perlu pemeriksaan lanjutan ke faskes',
                'input_type' => 'calculated',
                'order' => 28,
                'is_required' => false,
                'show_conditions' => ['gender' => 'P', 'min_age' => 15, 'max_age' => 49, 'depends_on' => 'ibu_hamil_1', 'depends_value' => '1'],
                'settings' => ['calculate_from' => 'srq20', 'threshold' => 6],
            ],
        ];
    }
}
