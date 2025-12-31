<?php

namespace Database\Seeders;

use App\Models\HealthQuestion;
use App\Models\HealthQuestionCategory;
use App\Models\HealthQuestionOption;
use App\Models\HealthQuestionTableRow;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk Kategori II.C: Bayi (0-11 Bulan)
 * 16 pertanyaan + checklist imunisasi untuk BAYI usia 0-11 bulan
 */
class HealthQuestions_Bayi_Seeder extends Seeder
{
    public function run(): void
    {
        $category = HealthQuestionCategory::where('code', 'bayi')->first();

        if (!$category) {
            $this->command->error('Category Bayi not found! Run main seeder first.');
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
            // ========== BAGIAN II.C: BAYI (0-11 BULAN) ==========
            [
                'code' => 'bayi_1',
                'question_text' => 'Apakah bayi dilakukan IMD (Inisiasi Menyusu Dini)?',
                'question_note' => 'IMD adalah proses membiarkan bayi menyusu sendiri segera setelah lahir dalam 1 jam pertama',
                'input_type' => 'radio',
                'order' => 1,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                    ['value' => '3', 'label' => 'Tidak Tahu'],
                ],
            ],
            [
                'code' => 'bayi_2',
                'question_text' => 'Apakah bayi mendapatkan ASI Eksklusif sampai usia 6 bulan?',
                'question_note' => 'ASI Eksklusif adalah pemberian ASI saja tanpa makanan/minuman lain sampai usia 6 bulan',
                'input_type' => 'radio',
                'order' => 2,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                    ['value' => '3', 'label' => 'Masih dalam proses (belum 6 bulan)'],
                ],
            ],
            [
                'code' => 'bayi_3',
                'question_text' => 'Apakah bayi memiliki Buku KIA/KMS?',
                'question_note' => 'Buku Kesehatan Ibu dan Anak / Kartu Menuju Sehat',
                'input_type' => 'radio',
                'order' => 3,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_4',
                'question_text' => 'Apakah bayi sudah ditimbang bulan ini?',
                'question_note' => null,
                'input_type' => 'radio',
                'order' => 4,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_5',
                'question_text' => 'Jika menjawab Ya pada nomor 4, berapa berat badan bayi saat ini?',
                'question_note' => null,
                'input_type' => 'number',
                'order' => 5,
                'is_required' => false,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11, 'depends_on' => 'bayi_4', 'depends_value' => '1'],
                'validation_rules' => ['min' => 1, 'max' => 20, 'decimal' => 2],
                'settings' => ['unit' => 'kg'],
            ],
            [
                'code' => 'bayi_6',
                'question_text' => 'Apakah sudah diukur panjang/tinggi badan bulan ini?',
                'question_note' => null,
                'input_type' => 'radio',
                'order' => 6,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_7',
                'question_text' => 'Jika menjawab Ya pada nomor 6, berapa panjang/tinggi badan bayi saat ini?',
                'question_note' => null,
                'input_type' => 'number',
                'order' => 7,
                'is_required' => false,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11, 'depends_on' => 'bayi_6', 'depends_value' => '1'],
                'validation_rules' => ['min' => 30, 'max' => 100, 'decimal' => 1],
                'settings' => ['unit' => 'cm'],
            ],
            [
                'code' => 'bayi_8',
                'question_text' => 'Apakah sudah diukur lingkar kepala bulan ini?',
                'question_note' => null,
                'input_type' => 'radio',
                'order' => 8,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_9',
                'question_text' => 'Jika menjawab Ya pada nomor 8, berapa lingkar kepala bayi saat ini?',
                'question_note' => null,
                'input_type' => 'number',
                'order' => 9,
                'is_required' => false,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11, 'depends_on' => 'bayi_8', 'depends_value' => '1'],
                'validation_rules' => ['min' => 20, 'max' => 60, 'decimal' => 1],
                'settings' => ['unit' => 'cm'],
            ],
            [
                'code' => 'bayi_10',
                'question_text' => 'Apakah pertumbuhan bayi (BB/TB) naik atau tetap?',
                'question_note' => 'Berdasarkan pemantauan di Buku KIA/KMS',
                'input_type' => 'radio',
                'order' => 10,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Naik'],
                    ['value' => '2', 'label' => 'Tetap'],
                    ['value' => '3', 'label' => 'Turun'],
                    ['value' => '4', 'label' => 'Tidak Tahu'],
                ],
            ],
            [
                'code' => 'bayi_11',
                'question_text' => 'Apakah bayi pernah mengalami diare dalam 2 minggu terakhir?',
                'question_note' => null,
                'input_type' => 'radio',
                'order' => 11,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_12',
                'question_text' => 'Apakah bayi pernah mengalami ISPA/batuk pilek dalam 2 minggu terakhir?',
                'question_note' => 'ISPA = Infeksi Saluran Pernapasan Akut',
                'input_type' => 'radio',
                'order' => 12,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_13',
                'question_text' => 'Apakah bayi sudah mendapatkan Vitamin A?',
                'question_note' => 'Vitamin A diberikan pada bulan Februari dan Agustus setiap tahun',
                'input_type' => 'radio',
                'order' => 13,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11, 'min_months' => 6],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_14',
                'question_text' => 'Apakah bayi sudah mendapatkan MPASI (Makanan Pendamping ASI)?',
                'question_note' => 'MPASI diberikan mulai usia 6 bulan',
                'input_type' => 'radio',
                'order' => 14,
                'is_required' => false,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11, 'min_months' => 6],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_15',
                'question_text' => 'Apakah bayi sudah mendapatkan Kunjungan Neonatal Lengkap?',
                'question_note' => 'KN lengkap: KN1 (6-48 jam), KN2 (3-7 hari), KN3 (8-28 hari)',
                'input_type' => 'radio',
                'order' => 15,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => '1', 'label' => 'Ya, lengkap (3 kali)'],
                    ['value' => '2', 'label' => 'Ya, tidak lengkap'],
                    ['value' => '3', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'bayi_16',
                'question_text' => 'Status imunisasi bayi (centang yang sudah didapatkan):',
                'question_note' => 'Imunisasi dasar lengkap sesuai jadwal',
                'input_type' => 'checkbox',
                'order' => 16,
                'is_required' => true,
                'show_conditions' => ['min_age' => 0, 'max_age' => 0, 'max_months' => 11],
                'options' => [
                    ['value' => 'hb0', 'label' => 'Hepatitis B (HB-0) - saat lahir', 'settings' => ['target_age_months' => 0]],
                    ['value' => 'bcg', 'label' => 'BCG - 1 bulan', 'settings' => ['target_age_months' => 1]],
                    ['value' => 'polio1', 'label' => 'Polio 1 - 1 bulan', 'settings' => ['target_age_months' => 1]],
                    ['value' => 'dpt_hb_hib1', 'label' => 'DPT-HB-Hib 1 - 2 bulan', 'settings' => ['target_age_months' => 2]],
                    ['value' => 'polio2', 'label' => 'Polio 2 - 2 bulan', 'settings' => ['target_age_months' => 2]],
                    ['value' => 'pcv1', 'label' => 'PCV 1 - 2 bulan', 'settings' => ['target_age_months' => 2]],
                    ['value' => 'rotavirus1', 'label' => 'Rotavirus 1 - 2 bulan', 'settings' => ['target_age_months' => 2]],
                    ['value' => 'dpt_hb_hib2', 'label' => 'DPT-HB-Hib 2 - 3 bulan', 'settings' => ['target_age_months' => 3]],
                    ['value' => 'polio3', 'label' => 'Polio 3 - 3 bulan', 'settings' => ['target_age_months' => 3]],
                    ['value' => 'pcv2', 'label' => 'PCV 2 - 4 bulan', 'settings' => ['target_age_months' => 4]],
                    ['value' => 'rotavirus2', 'label' => 'Rotavirus 2 - 4 bulan', 'settings' => ['target_age_months' => 4]],
                    ['value' => 'dpt_hb_hib3', 'label' => 'DPT-HB-Hib 3 - 4 bulan', 'settings' => ['target_age_months' => 4]],
                    ['value' => 'polio4', 'label' => 'Polio 4 - 4 bulan', 'settings' => ['target_age_months' => 4]],
                    ['value' => 'ipv1', 'label' => 'IPV 1 - 4 bulan', 'settings' => ['target_age_months' => 4]],
                    ['value' => 'rotavirus3', 'label' => 'Rotavirus 3 - 6 bulan', 'settings' => ['target_age_months' => 6]],
                    ['value' => 'campak_rubella1', 'label' => 'Campak-Rubella (MR) 1 - 9 bulan', 'settings' => ['target_age_months' => 9]],
                    ['value' => 'ipv2', 'label' => 'IPV 2 - 9 bulan', 'settings' => ['target_age_months' => 9]],
                    ['value' => 'pcv3', 'label' => 'PCV 3 - 12 bulan', 'settings' => ['target_age_months' => 12]],
                ],
            ],
        ];
    }
}
