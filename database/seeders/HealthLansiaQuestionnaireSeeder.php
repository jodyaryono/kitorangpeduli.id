<?php

namespace Database\Seeders;

use App\Models\HealthQuestion;
use App\Models\HealthQuestionCategory;
use App\Models\HealthQuestionOption;
use App\Models\HealthQuestionTableRow;
use Illuminate\Database\Seeder;

class HealthLansiaQuestionnaireSeeder extends Seeder
{
    public function run(): void
    {
        // Create category for Lansia (≥60 years)
        $category = HealthQuestionCategory::create([
            'code' => 'lansia',
            'name' => 'II.I. Pertanyaan untuk Lansia (≥60 tahun)',
            'description' => 'Ditanyakan bila dalam Keluarga terdapat usia lanjut (≥ 60 tahun)',
            'target_criteria' => json_encode(['min_age' => 60, 'max_age' => 999]),
            'order' => 10,
        ]);

        // H.1. Berapa usia Bapak/Ibu
        HealthQuestion::create([
            'category_id' => $category->id,
            'code' => 'H1',
            'question_text' => 'Berapa usia Bapak/Ibu',
            'input_type' => 'text',
            'order' => 1,
        ]);

        // H.2. Jenis kelamin
        $q2 = HealthQuestion::create([
            'category_id' => $category->id,
            'code' => 'H2',
            'question_text' => 'Jenis kelamin',
            'input_type' => 'radio',
            'order' => 2,
        ]);
        HealthQuestionOption::create(['question_id' => $q2->id, 'value' => 'a', 'label' => 'Laki-laki', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q2->id, 'value' => 'b', 'label' => 'Perempuan', 'order' => 2]);

        // H.3. Bagaimana hasil pemeriksaan darah
        $q3 = HealthQuestion::create([
            'category_id' => $category->id,
            'code' => 'H3',
            'question_text' => 'Bagaimana hasil pemeriksaan darah',
            'input_type' => 'table_radio',
            'order' => 3,
        ]);
        HealthQuestionTableRow::create(['question_id' => $q3->id, 'row_code' => 'gula_darah', 'row_label' => 'Gula Darah', 'reference_value' => 'Kurang dari 200 mg/dl (Sewaktu)', 'order' => 1]);
        HealthQuestionTableRow::create(['question_id' => $q3->id, 'row_code' => 'asam_urat', 'row_label' => 'Asam Urat', 'reference_value' => 'Pria : ≤ 7 mg/dl / Wanita : ≤ 6 mg/dl', 'order' => 2]);
        HealthQuestionOption::create(['question_id' => $q3->id, 'value' => 'd', 'label' => 'Kurang', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q3->id, 'value' => 'e', 'label' => 'Normal', 'order' => 2]);
        HealthQuestionOption::create(['question_id' => $q3->id, 'value' => 'f', 'label' => 'Tinggi', 'order' => 3]);

        // H.4. AKS (Aktivitas Kehidupan Sehari-hari)
        $q4 = HealthQuestion::create([
            'category_id' => $category->id,
            'code' => 'H4',
            'question_text' => 'Menilai tingkat kemandirian lansia dengan penilaian Aktivitas Kehidupan Sehari-hari (AKS) berikut:',
            'question_note' => 'SKOR: 0, 1, 2 atau 3 sesuai keterangan',
            'input_type' => 'table_radio',
            'order' => 4,
        ]);

        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_1', 'row_label' => 'Mengendalikan rangsang Buang Air Besar (BAB)', 'note' => 'Tidak terkendali / tak teratur (perlu pencahar)|Kadang-kadang tak terkendali (1 x / minggu)|Terkendali teratur', 'order' => 1]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_2', 'row_label' => 'Mengendalikan rangsang Buang Air Kecil (BAK)', 'note' => 'Tidak terkendali atau pakai kateter|Kadang-kadang tak terkendali (hanya 1 x / 24 jam)|Mandiri', 'order' => 2]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_3', 'row_label' => 'Membersihkan diri (mencuci wajah, menyikat rambut, mencukur kumis, sikat gigi)', 'note' => 'Butuh pertolongan orang lain|Mandiri', 'order' => 3]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_4', 'row_label' => 'Penggunaan WC (keluar masuk WC, melepas/memakai celana, cebok, menyiram)', 'note' => 'Tergantung pertolongan orang lain|Perlu pertolongan pada beberapa kegiatan tetapi dapat mengerjakan sendiri beberapa kegiatan yang lain|Mandiri', 'order' => 4]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_5', 'row_label' => 'Makan minum (jika makan harus berupa potongan, dianggap dibantu)', 'note' => 'Tidak mampu|Perlu pertolongan memotong makanan|Mandiri', 'order' => 5]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_6', 'row_label' => 'Bergerak dari kursi roda ke tempat tidur dan sebaliknya (termasuk duduk di tempat tidur)', 'note' => 'Tidak mampu|Perlu banyak bantuan untuk bisa duduk (2 orang)|Bantuan minimal 1 orang|Mandiri', 'order' => 6]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_7', 'row_label' => 'Berjalan di tempat rata (atau jika tidak bisa berjalan, menjalankan kursi roda)', 'note' => 'Tidak mampu|Bisa (pindah) dengan kursi roda|Berjalan dengan bantuan 1 orang|Mandiri', 'order' => 7]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_8', 'row_label' => 'Berpakaian (termasuk memasang tali sepatu, mengencangkan sabuk)', 'note' => 'Tergantung orang lain|Sebagian dibantu (misalnya: mengancing baju)|Mandiri', 'order' => 8]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_9', 'row_label' => 'Naik turun tangga', 'note' => 'Tidak mampu|Butuh pertolongan (alat bantu)|Mandiri', 'order' => 9]);
        HealthQuestionTableRow::create(['question_id' => $q4->id, 'row_code' => 'aks_10', 'row_label' => 'Mandi', 'note' => 'Tergantung orang lain|Mandiri', 'order' => 10]);

        HealthQuestionOption::create(['question_id' => $q4->id, 'value' => '0', 'label' => '0', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q4->id, 'value' => '1', 'label' => '1', 'order' => 2]);
        HealthQuestionOption::create(['question_id' => $q4->id, 'value' => '2', 'label' => '2', 'order' => 3]);
        HealthQuestionOption::create(['question_id' => $q4->id, 'value' => '3', 'label' => '3', 'order' => 4]);

        // H.5. Status kemandirian Lansia
        $q5 = HealthQuestion::create([
            'category_id' => $category->id,
            'code' => 'H5',
            'question_text' => 'Berdasarkan penilaian di atas, bagaimana status kemandirian Lansia?',
            'input_type' => 'radio',
            'order' => 5,
        ]);
        HealthQuestionOption::create(['question_id' => $q5->id, 'value' => 'a', 'label' => 'Mandiri (A) - 20', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q5->id, 'value' => 'b', 'label' => 'Ketergantungan ringan (B) - 12 – 19', 'order' => 2]);
        HealthQuestionOption::create(['question_id' => $q5->id, 'value' => 'c', 'label' => 'Ketergantungan sedang (C) - 9 – 11', 'order' => 3]);
        HealthQuestionOption::create(['question_id' => $q5->id, 'value' => 'd', 'label' => 'Ketergantungan berat (D) - 5 – 8', 'order' => 4]);
        HealthQuestionOption::create(['question_id' => $q5->id, 'value' => 'e', 'label' => 'Ketergantungan total (E) - 0 – 4', 'order' => 5]);

        // H.6. SKILAS
        $q6 = HealthQuestion::create([
            'category_id' => $category->id,
            'code' => 'H6',
            'question_text' => 'Menilai perlu tidaknya lansia ditangani lebih lanjut (dirujuk) ke Puskesmas, dengan menggunakan instrumen Skrining Lansia Sederhana (SKILAS). Berikut:',
            'question_note' => 'Beri tanda centang sesuai hasil pemeriksaan',
            'input_type' => 'table_checkbox',
            'order' => 6,
        ]);

        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_1', 'row_label' => 'Penurunan kognitif - Mengingat tiga kata', 'order' => 1]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_2', 'row_label' => 'Penurunan kognitif - Orientasi waktu dan tempat', 'order' => 2]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_3', 'row_label' => 'Penurunan kognitif - Ulangi ketiga kata tadi', 'order' => 3]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_4', 'row_label' => 'Keterbatasan Mobilisasi - Tes berdiri dari kursi', 'order' => 4]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_5', 'row_label' => 'Malnutrisi - Berat badan berkurang >3 kg', 'order' => 5]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_6', 'row_label' => 'Malnutrisi - Hilang nafsu makan', 'order' => 6]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_7', 'row_label' => 'Malnutrisi - LILA <21 cm', 'order' => 7]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_8', 'row_label' => 'Gangguan Penglihatan - Masalah mata', 'order' => 8]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_9', 'row_label' => 'Gangguan Penglihatan - TES MELIHAT', 'order' => 9]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_10', 'row_label' => 'Gangguan pendengaran - TES BISIK', 'order' => 10]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_11', 'row_label' => 'Gejala depresi - Perasaan sedih/tertekan', 'order' => 11]);
        HealthQuestionTableRow::create(['question_id' => $q6->id, 'row_code' => 'skilas_12', 'row_label' => 'Gejala depresi - Sedikit minat melakukan sesuatu', 'order' => 12]);

        HealthQuestionOption::create(['question_id' => $q6->id, 'value' => 'ya', 'label' => 'Ya', 'order' => 1]);

        // H.7. Perlu dirujuk ke Puskesmas?
        $q7 = HealthQuestion::create([
            'category_id' => $category->id,
            'code' => 'H7',
            'question_text' => 'Berdasarkan pemeriksaan di atas, apakah lansia perlu dirujuk ke Puskesmas?',
            'question_note' => '(dirujuk, bila ada kolom hasil yang diberi tanda centang sesuai hasil pemeriksaan)',
            'input_type' => 'radio',
            'order' => 7,
        ]);
        HealthQuestionOption::create(['question_id' => $q7->id, 'value' => 'a', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q7->id, 'value' => 'b', 'label' => 'Tidak', 'order' => 2]);

        $this->command->info('✅ Created Lansia category with 7 questions');
    }
}
