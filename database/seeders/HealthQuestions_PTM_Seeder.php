<?php

namespace Database\Seeders;

use App\Models\HealthQuestion;
use App\Models\HealthQuestionCategory;
use App\Models\HealthQuestionOption;
use App\Models\HealthQuestionTableRow;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk Kategori I: Penyakit Menular dan Tidak Menular
 * 12 pertanyaan untuk SEMUA golongan umur
 */
class HealthQuestions_PTM_Seeder extends Seeder
{
    public function run(): void
    {
        $category = HealthQuestionCategory::where('code', 'ptm')->first();

        if (!$category) {
            $this->command->error('Category PTM not found! Run main seeder first.');
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
            // ========== PERTANYAAN 1-4: UNTUK SEMUA GOLONGAN UMUR ==========
            [
                'code' => 'ptm_1',
                'question_text' => 'Apakah ada anggota keluarga yang memiliki bercak putih pada kulit dan tidak terasa, serta adanya pembengkakan pada wajah?',
                'question_note' => '(untuk semua golongan umur)',
                'input_type' => 'radio',
                'order' => 1,
                'is_required' => true,
                'show_conditions' => null,  // Semua umur
                'options' => [
                    ['value' => '1', 'label' => 'Ada'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ptm_2',
                'question_text' => 'Apakah ada anggota keluarga adanya pembengkakan pada bagian-bagian tubuh tertentu?',
                'question_note' => '(untuk semua golongan umur) Bila pada pertanyaan nomor 1 dijawab ada, maka lanjut pertanyaan nomor 2.',
                'input_type' => 'radio',
                'order' => 2,
                'is_required' => false,
                'show_conditions' => ['depends_on' => 'ptm_1', 'depends_value' => '1'],
                'options' => [
                    ['value' => '1', 'label' => 'Ada'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ptm_3',
                'question_text' => 'Apakah ada anggota keluarga yang memiliki tanda dan gejala penyakit filariasis/kaki gajah? (Bengkak pada salah satu atau lebih pada bagian tubuh)',
                'question_note' => '(untuk semua golongan umur)',
                'input_type' => 'radio',
                'order' => 3,
                'is_required' => true,
                'show_conditions' => null,
                'options' => [
                    ['value' => '1', 'label' => 'Ada'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ptm_4',
                'question_text' => 'Apakah terdapat luka lama yang tidak sembuh-sembuh dan menyebar ke permukaan kulit lainnya (luka yang bukan disebabkan oleh benda tajam)?',
                'question_note' => '(untuk semua golongan umur)',
                'input_type' => 'radio',
                'order' => 4,
                'is_required' => true,
                'show_conditions' => null,
                'options' => [
                    ['value' => '1', 'label' => 'Ada'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            // ========== PERTANYAAN 5-7: UNTUK USIA PRODUKTIF DAN LANSIA ==========
            [
                'code' => 'ptm_5',
                'question_text' => 'Apakah dilakukan pengukuran kadar gula darah?',
                'question_note' => '(untuk Usia produktif dan Lansia)',
                'input_type' => 'radio',
                'order' => 5,
                'is_required' => true,
                'show_conditions' => ['min_age' => 15, 'max_age' => 999],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ptm_6',
                'question_text' => 'Bagaimana hasil pemeriksaan darah',
                'question_note' => '(untuk Usia produktif dan Lansia)',
                'input_type' => 'table',
                'order' => 6,
                'is_required' => false,
                'show_conditions' => ['min_age' => 15, 'max_age' => 999, 'depends_on' => 'ptm_5', 'depends_value' => '1'],
                'settings' => ['reference_table' => true],
                'table_rows' => [
                    ['row_code' => 'gula_darah_puasa', 'row_label' => 'Gula Darah (Puasa)', 'input_type' => 'number', 'unit' => 'mg/dl', 'reference_value' => '70-100 mg/dl'],
                    ['row_code' => 'gula_darah_sewaktu', 'row_label' => 'Gula Darah (Sewaktu)', 'input_type' => 'number', 'unit' => 'mg/dl', 'reference_value' => 'Kurang dari 200 mg/dl'],
                    ['row_code' => 'asam_urat', 'row_label' => 'Asam Urat', 'input_type' => 'number', 'unit' => 'mg/dl', 'reference_value' => 'Pria: ≤7 mg/dl / Wanita: ≤6 mg/dl'],
                    ['row_code' => 'kolesterol', 'row_label' => 'Kolesterol', 'input_type' => 'number', 'unit' => 'mg/dl', 'reference_value' => 'Kurang dari 200 mg/dl'],
                ],
            ],
            [
                'code' => 'ptm_7',
                'question_text' => 'Apakah selama ini Saudara meminum obat diabetes melitus secara teratur?',
                'question_note' => null,
                'input_type' => 'radio',
                'order' => 7,
                'is_required' => false,
                'show_conditions' => ['min_age' => 15, 'max_age' => 999],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            // ========== PERTANYAAN 8-10: PENYAKIT DILUAR SPM ==========
            [
                'code' => 'ptm_8',
                'question_text' => 'Apakah Anda pernah menderita Stroke, Jantung, atau Penyakit Tidak Menular Lainnya?',
                'question_note' => '(untuk Usia produktif dan Lansia) Pertanyaan 8-10 terkait dengan penyakit-penyakit diluar SPM (ODGJ, Hipertensi, DM, TB dan HIV)',
                'input_type' => 'radio',
                'order' => 8,
                'is_required' => true,
                'show_conditions' => ['min_age' => 15, 'max_age' => 999],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ptm_9',
                'question_text' => 'Bila menjawab ya, pada pertanyaan nomor 8, maka, sebutkan penyakitnya (jawaban bisa lebih dari 1):',
                'question_note' => '(untuk Usia produktif dan Lansia)',
                'input_type' => 'checkbox',
                'order' => 9,
                'is_required' => false,
                'show_conditions' => ['min_age' => 15, 'max_age' => 999, 'depends_on' => 'ptm_8', 'depends_value' => '1'],
                'options' => [
                    ['value' => 'stroke', 'label' => 'Stroke'],
                    ['value' => 'jantung', 'label' => 'Jantung'],
                    ['value' => 'kanker', 'label' => 'Kanker'],
                    ['value' => 'gangguan_penglihatan', 'label' => 'Gangguan penglihatan'],
                    ['value' => 'gangguan_pendengaran', 'label' => 'Gangguan pendengaran'],
                    ['value' => 'lainnya', 'label' => 'Lainnya', 'settings' => ['is_other' => true]],
                ],
            ],
            [
                'code' => 'ptm_10',
                'question_text' => 'Bila ya, apakah selama ini saudara melakukan kontrol atau pengobatan secara teratur?',
                'question_note' => '(untuk Usia produktif dan Lansia)',
                'input_type' => 'radio',
                'order' => 10,
                'is_required' => false,
                'show_conditions' => ['min_age' => 15, 'max_age' => 999, 'depends_on' => 'ptm_8', 'depends_value' => '1'],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            // ========== PERTANYAAN 11-12: FAKTOR RISIKO PTM ==========
            [
                'code' => 'ptm_11',
                'question_text' => 'Apakah Anda dalam 6 bulan terakhir, mengecek Kesehatan anda ke fasilitas kesehatan tersedia (Puskesmas, Posbindu, Poslansia, Praktek Dokter atau RS) atau secara mandiri?',
                'question_note' => '(ditanyakan bagi responden yang hasil pemeriksaan pada nomor 6 tidak normal dan atau menjawab ya pada pertanyaan nomor 8) Untuk Pertanyaan Nomor 11-12 Guna Mendeteksi Faktor Risiko PTM.',
                'input_type' => 'radio',
                'order' => 11,
                'is_required' => false,
                'show_conditions' => ['min_age' => 15, 'max_age' => 999],
                'options' => [
                    ['value' => '1', 'label' => 'Ya'],
                    ['value' => '2', 'label' => 'Tidak'],
                ],
            ],
            [
                'code' => 'ptm_12',
                'question_text' => 'Bila pada pertanyaan nomor 10, menjawab tidak, apa alasan Anda?',
                'question_note' => null,
                'input_type' => 'textarea',
                'order' => 12,
                'is_required' => false,
                'show_conditions' => ['min_age' => 15, 'max_age' => 999, 'depends_on' => 'ptm_11', 'depends_value' => '2'],
            ],
        ];
    }
}
