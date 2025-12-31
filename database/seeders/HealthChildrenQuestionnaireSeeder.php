<?php

namespace Database\Seeders;

use App\Models\HealthQuestion;
use App\Models\HealthQuestionCategory;
use App\Models\HealthQuestionOption;
use App\Models\HealthQuestionTableRow;
use Illuminate\Database\Seeder;

class HealthChildrenQuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds for children and productive age health questions
     * Categories: balita, paud, sd, smp, produktif, lansia
     */
    public function run(): void
    {
        // ========================================
        // CATEGORY: BALITA (12-59 bulan / 1 < 5 tahun)
        // ========================================
        $balita = HealthQuestionCategory::create([
            'code' => 'balita',
            'name' => 'II.D. Balita (12-59 bulan atau 1 < 5 tahun)',
            'description' => 'Ditanyakan bila dalam keluarga terdapat Balita',
            'order' => 4,
            'target_criteria' => json_encode([
                'min_age' => 1,
                'max_age' => 4,
            ]),
        ]);

        // Q1: ASI sampai 2 tahun
        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_1',
            'question_text' => 'Apakah balita masih diberikan ASI sampai 2 tahun?',
            'input_type' => 'radio',
            'order' => 1,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Tidak', 'order' => 2]);

        // Q2: Vitamin A
        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_2',
            'question_text' => 'Apakah balita sudah minum kapsul vitamin A merah 2 kali dalam setahun?',
            'input_type' => 'radio',
            'order' => 2,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Sudah', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Belum', 'order' => 2]);

        // Q3: Buku KIA
        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_3',
            'question_text' => 'Apakah balita (sebutkan nama balitanya) memiliki buku KIA?',
            'input_type' => 'radio',
            'order' => 3,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Tidak', 'order' => 2]);

        // Q4: Umur balita
        HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_4',
            'question_text' => 'Berapa umur balita ibu sekarang?',
            'question_note' => '...... bulan (lahir tanggal.... bulan ..... tahun .......)',
            'input_type' => 'number',
            'order' => 4,
            'settings' => json_encode(['unit' => 'bulan']),
        ]);

        // Q5a: Berat badan
        HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_5a',
            'question_text' => 'Berapa berat badan sekarang?',
            'input_type' => 'number',
            'order' => 5,
            'settings' => json_encode(['unit' => 'kg', 'decimal' => 1]),
            'validation_rules' => json_encode(['min' => 0, 'max' => 50, 'decimal' => 1]),
        ]);

        // Q5b: Tinggi badan
        HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_5b',
            'question_text' => 'Berapa tinggi badan sekarang?',
            'input_type' => 'number',
            'order' => 6,
            'settings' => json_encode(['unit' => 'm', 'decimal' => 2]),
            'validation_rules' => json_encode(['min' => 0, 'max' => 2, 'decimal' => 2]),
        ]);

        // Q6-9: Status Gizi (BB/U, PB/U, BB/TB, IMT)
        $gizi_options = [
            ['value' => 'a', 'label' => 'Berat badan Sangat kurang'],
            ['value' => 'b', 'label' => 'Berat badan Kurang'],
            ['value' => 'c', 'label' => 'Berat badan Normal'],
            ['value' => 'd', 'label' => 'Berat badan lebih'],
        ];

        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_6',
            'question_text' => 'Berdasarkan jawaban pada pertanyaan nomor 4 dan 5 di atas, bagaimana status gizi dengan indeks BB/U bayi usia (0-60 bulan)?',
            'input_type' => 'radio',
            'order' => 7,
        ]);
        foreach ($gizi_options as $idx => $opt) {
            HealthQuestionOption::create(['question_id' => $q->id, 'value' => $opt['value'], 'label' => $opt['label'], 'order' => $idx + 1]);
        }

        $pb_options = [
            ['value' => 'a', 'label' => 'Sangat pendek'],
            ['value' => 'b', 'label' => 'Pendek'],
            ['value' => 'c', 'label' => 'Normal'],
            ['value' => 'd', 'label' => 'Tinggi'],
        ];

        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_7',
            'question_text' => 'Berdasarkan jawaban pada pertanyaan nomor 4 dan 5 di atas, bagaimana status gizi dengan indeks PB/U atau TB/U bayi (0-60 bulan)?',
            'input_type' => 'radio',
            'order' => 8,
        ]);
        foreach ($pb_options as $idx => $opt) {
            HealthQuestionOption::create(['question_id' => $q->id, 'value' => $opt['value'], 'label' => $opt['label'], 'order' => $idx + 1]);
        }

        $bb_tb_options = [
            ['value' => 'a', 'label' => 'Gizi buruk'],
            ['value' => 'b', 'label' => 'Gizi kurang'],
            ['value' => 'c', 'label' => 'Gizi Normal/baik'],
            ['value' => 'd', 'label' => 'Berisiko gizi lebih'],
            ['value' => 'e', 'label' => 'Gizi lebih'],
            ['value' => 'f', 'label' => 'Obesitas'],
        ];

        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_8',
            'question_text' => 'Berdasarkan jawaban pada pertanyaan nomor 4 dan 5 di atas, bagaimana status gizi dengan indeks BB/TB atau BB/TB bayi (0-60 bulan)?',
            'input_type' => 'radio',
            'order' => 9,
        ]);
        foreach ($bb_tb_options as $idx => $opt) {
            HealthQuestionOption::create(['question_id' => $q->id, 'value' => $opt['value'], 'label' => $opt['label'], 'order' => $idx + 1]);
        }

        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_9',
            'question_text' => 'Berdasarkan jawaban pada pertanyaan nomor 4 dan 5 di atas, bagaimana status gizi dengan indeks massa tubuh menurut umur (IMT/U) bayi (0-60 bulan)?',
            'input_type' => 'radio',
            'order' => 10,
        ]);
        foreach ($bb_tb_options as $idx => $opt) {
            HealthQuestionOption::create(['question_id' => $q->id, 'value' => $opt['value'], 'label' => $opt['label'], 'order' => $idx + 1]);
        }

        // Q10: Penyakit
        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_10',
            'question_text' => 'Apakah Balita memiliki penyakit, yang menyebabkan harus dirawat dan masih dalam proses pengobatan sampai sekarang?',
            'input_type' => 'radio',
            'order' => 11,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Tidak', 'order' => 2]);

        // Q11: Bila ya, sebutkan
        HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_11',
            'question_text' => 'Bila ya, sebutkan',
            'input_type' => 'textarea',
            'order' => 12,
            'show_conditions' => json_encode([
                'depends_on' => 'balita_10',
                'depends_value' => '1',
            ]),
        ]);

        // Q12: Obat cacing
        $q = HealthQuestion::create([
            'category_id' => $balita->id,
            'code' => 'balita_12',
            'question_text' => 'Apakah dalam tahun ini, balita ibu, sudah minum obat cacing?',
            'input_type' => 'radio',
            'order' => 13,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Sudah', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Belum', 'order' => 2]);

        // ========================================
        // CATEGORY: PAUD/TK (5-6 tahun)
        // ========================================
        $paud = HealthQuestionCategory::create([
            'code' => 'paud',
            'name' => 'II.E. Anak PAUD atau TK (5-6 tahun)',
            'description' => 'Ditanyakan bila dalam keluarga terdapat Anak PAUD atau TK',
            'order' => 5,
            'target_criteria' => json_encode([
                'min_age' => 5,
                'max_age' => 6,
            ]),
        ]);

        // Q1: Bersekolah
        $q = HealthQuestion::create([
            'category_id' => $paud->id,
            'code' => 'paud_1',
            'question_text' => 'Apakah anak Anda sudah bersekolah?',
            'input_type' => 'radio',
            'order' => 1,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Belum', 'order' => 2]);

        // Q2: Obat cacing
        $q = HealthQuestion::create([
            'category_id' => $paud->id,
            'code' => 'paud_2',
            'question_text' => 'Apakah dalam tahun ini, anak sudah minum obat cacing?',
            'input_type' => 'radio',
            'order' => 2,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Belum', 'order' => 2]);

        // ========================================
        // CATEGORY: SD (7-12 tahun)
        // ========================================
        $sd = HealthQuestionCategory::create([
            'code' => 'sd',
            'name' => 'II.F. Anak Usia Pendidikan Dasar (7-12 tahun)',
            'description' => 'Ditanyakan bila dalam keluarga terdapat anak usia Pendidikan dasar',
            'order' => 6,
            'target_criteria' => json_encode([
                'min_age' => 7,
                'max_age' => 12,
            ]),
        ]);

        // Q1: Umur anak
        HealthQuestion::create([
            'category_id' => $sd->id,
            'code' => 'sd_1',
            'question_text' => 'Berapa umur anak ibu sekarang?',
            'question_note' => '..... tahun (lahir tgl ..... bulan ..... tahun .......)',
            'input_type' => 'number',
            'order' => 1,
            'settings' => json_encode(['unit' => 'tahun']),
        ]);

        // Q2a: Berat badan
        HealthQuestion::create([
            'category_id' => $sd->id,
            'code' => 'sd_2a',
            'question_text' => 'Berapa berat badan Anda sekarang?',
            'input_type' => 'number',
            'order' => 2,
            'settings' => json_encode(['unit' => 'kg', 'decimal' => 1]),
        ]);

        // Q2b: Tinggi badan
        HealthQuestion::create([
            'category_id' => $sd->id,
            'code' => 'sd_2b',
            'question_text' => 'Berapa tinggi badan Anda sekarang?',
            'input_type' => 'number',
            'order' => 3,
            'settings' => json_encode(['unit' => 'm', 'decimal' => 2]),
        ]);

        // Q3: Status Gizi IMT
        $imt_options = [
            ['value' => 'a', 'label' => 'Gizi buruk'],
            ['value' => 'b', 'label' => 'Gizi kurang'],
            ['value' => 'c', 'label' => 'Gizi baik'],
            ['value' => 'd', 'label' => 'Gizi lebih'],
            ['value' => 'e', 'label' => 'Obesitas'],
        ];

        $q = HealthQuestion::create([
            'category_id' => $sd->id,
            'code' => 'sd_3',
            'question_text' => 'Berdasarkan jawaban pada pertanyaan nomor 1 dan 2 di atas, bagaimana status gizi dengan indeks massa tubuh menurut umur (IMT/U) anak?',
            'input_type' => 'radio',
            'order' => 4,
        ]);
        foreach ($imt_options as $idx => $opt) {
            HealthQuestionOption::create(['question_id' => $q->id, 'value' => $opt['value'], 'label' => $opt['label'], 'order' => $idx + 1]);
        }

        // Q4: Penyakit berat
        $q = HealthQuestion::create([
            'category_id' => $sd->id,
            'code' => 'sd_4',
            'question_text' => 'Apakah anak ibu, memiliki penyakit berat yang pernah dialami dan masih dirasakan sampai sekarang?',
            'input_type' => 'radio',
            'order' => 5,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Tidak', 'order' => 2]);

        // Q5: Bila ya, jelaskan
        HealthQuestion::create([
            'category_id' => $sd->id,
            'code' => 'sd_5',
            'question_text' => 'Bila ya, jelaskan',
            'input_type' => 'textarea',
            'order' => 6,
            'show_conditions' => json_encode([
                'depends_on' => 'sd_4',
                'depends_value' => '1',
            ]),
        ]);

        // ========================================
        // CATEGORY: SMP (13-15 tahun)
        // ========================================
        $smp = HealthQuestionCategory::create([
            'code' => 'smp',
            'name' => 'II.G. Anak Usia SMP (13-15 tahun)',
            'description' => 'Ditanyakan bila dalam keluarga terdapat anak usia SMP',
            'order' => 7,
            'target_criteria' => json_encode([
                'min_age' => 13,
                'max_age' => 15,
            ]),
        ]);

        // Q1: Umur
        HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_1',
            'question_text' => 'Berapa umur Anda sekarang?',
            'question_note' => '..... tahun (lahir tgl ..... bulan ..... tahun .......)',
            'input_type' => 'number',
            'order' => 1,
            'settings' => json_encode(['unit' => 'tahun']),
        ]);

        // Q2a: Berat badan
        HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_2a',
            'question_text' => 'Berapa berat badan Anda sekarang?',
            'input_type' => 'number',
            'order' => 2,
            'settings' => json_encode(['unit' => 'kg', 'decimal' => 1]),
        ]);

        // Q2b: Tinggi badan
        HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_2b',
            'question_text' => 'Berapa tinggi badan Anda sekarang?',
            'input_type' => 'number',
            'order' => 3,
            'settings' => json_encode(['unit' => 'm', 'decimal' => 2]),
        ]);

        // Q3: Status Gizi IMT
        $q = HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_3',
            'question_text' => 'Berdasarkan jawaban pada pertanyaan nomor 1 dan 2 di atas, bagaimana status gizi dengan indeks massa tubuh menurut umur (IMT/U) anak?',
            'input_type' => 'radio',
            'order' => 4,
        ]);
        foreach ($imt_options as $idx => $opt) {
            HealthQuestionOption::create(['question_id' => $q->id, 'value' => $opt['value'], 'label' => $opt['label'], 'order' => $idx + 1]);
        }

        // Q4: Penyakit berat
        $q = HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_4',
            'question_text' => 'Apakah anak ibu, memiliki penyakit berat yang pernah dialami dan masih dirasakan sampai sekarang?',
            'input_type' => 'radio',
            'order' => 5,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Tidak', 'order' => 2]);

        // Q5: Bila ya, jelaskan
        HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_5',
            'question_text' => 'Bila ya, jelaskan',
            'input_type' => 'textarea',
            'order' => 6,
            'show_conditions' => json_encode([
                'depends_on' => 'smp_4',
                'depends_value' => '1',
            ]),
        ]);

        // Q6: Haid (untuk anak perempuan)
        $q = HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_6',
            'question_text' => '(Ditanyakan untuk anak perempuan). Apakah Anda sudah haid?',
            'input_type' => 'radio',
            'order' => 7,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Tidak', 'order' => 2]);

        // Q7: Tablet tambah darah
        $q = HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_7',
            'question_text' => '(Ditanyakan untuk anak perempuan). Apakah setiap minggu, Anda meminum tablet tambah darah?',
            'input_type' => 'radio',
            'order' => 8,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Tidak', 'order' => 2]);

        // Q8: Hb remaja putri (Table)
        $q = HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_8',
            'question_text' => 'Periksa kadar Hb remaja putri',
            'input_type' => 'table',
            'order' => 9,
        ]);

        HealthQuestionTableRow::create([
            'question_id' => $q->id,
            'row_code' => 'hemoglobin',
            'row_label' => 'Hemoglobin',
            'unit' => 'gr/dL',
            'reference_value' => '≥ 12 gr/dL',
            'order' => 1,
        ]);

        // Q9: Kategori anemia
        $anemia_options = [
            ['value' => 'a', 'label' => 'Ringan (11 - 11,9 gr/Dl)'],
            ['value' => 'b', 'label' => 'Sedang (8 – 10,9 gr/dL)'],
            ['value' => 'c', 'label' => 'Berat (<8 gr/dL)'],
        ];

        $q = HealthQuestion::create([
            'category_id' => $smp->id,
            'code' => 'smp_9',
            'question_text' => '(berdasarkan hasil pemeriksaan pada nomor 8) kategori anemia remaja putri tersebut adalah',
            'input_type' => 'radio',
            'order' => 10,
        ]);
        foreach ($anemia_options as $idx => $opt) {
            HealthQuestionOption::create(['question_id' => $q->id, 'value' => $opt['value'], 'label' => $opt['label'], 'order' => $idx + 1]);
        }

        // ========================================
        // CATEGORY: PRODUKTIF (15-59 tahun)
        // ========================================
        $produktif = HealthQuestionCategory::create([
            'code' => 'produktif',
            'name' => 'II.H. Usia Produktif (15-59 tahun)',
            'description' => 'Ditanyakan bila dalam keluarga terdapat usia produktif',
            'order' => 8,
            'target_criteria' => json_encode([
                'min_age' => 15,
                'max_age' => 59,
            ]),
        ]);

        // Q1: Umur
        HealthQuestion::create([
            'category_id' => $produktif->id,
            'code' => 'produktif_1',
            'question_text' => 'Berapa umur Anda sekarang?',
            'question_note' => '..... tahun (lahir tgl ..... bulan ..... tahun .......)',
            'input_type' => 'number',
            'order' => 1,
            'settings' => json_encode(['unit' => 'tahun']),
        ]);

        // Q2a: Berat badan
        HealthQuestion::create([
            'category_id' => $produktif->id,
            'code' => 'produktif_2a',
            'question_text' => 'Berapa berat badan Anda sekarang?',
            'input_type' => 'number',
            'order' => 2,
            'settings' => json_encode(['unit' => 'kg', 'decimal' => 1]),
        ]);

        // Q2b: Tinggi badan
        HealthQuestion::create([
            'category_id' => $produktif->id,
            'code' => 'produktif_2b',
            'question_text' => 'Berapa tinggi badan Anda sekarang?',
            'input_type' => 'number',
            'order' => 3,
            'settings' => json_encode(['unit' => 'm', 'decimal' => 2]),
        ]);

        // Q3: IMT dan Status Gizi
        $imt_dewasa_options = [
            ['value' => 'a', 'label' => 'Sangat Kurus'],
            ['value' => 'b', 'label' => 'Kurus'],
            ['value' => 'c', 'label' => 'Normal'],
            ['value' => 'd', 'label' => 'Gemuk'],
            ['value' => 'e', 'label' => 'Obesitas'],
        ];

        HealthQuestion::create([
            'category_id' => $produktif->id,
            'code' => 'produktif_3_imt',
            'question_text' => 'Berapa nilai IMT Anda (diisi oleh petugas)',
            'question_note' => 'IMT Anda adalah ........',
            'input_type' => 'number',
            'order' => 4,
            'settings' => json_encode(['decimal' => 2]),
        ]);

        $q = HealthQuestion::create([
            'category_id' => $produktif->id,
            'code' => 'produktif_3_status',
            'question_text' => 'Status Gizi Anda berdasarkan IMT adalah:',
            'input_type' => 'radio',
            'order' => 5,
        ]);
        foreach ($imt_dewasa_options as $idx => $opt) {
            HealthQuestionOption::create(['question_id' => $q->id, 'value' => $opt['value'], 'label' => $opt['label'], 'order' => $idx + 1]);
        }

        // Q4: Penyakit
        $q = HealthQuestion::create([
            'category_id' => $produktif->id,
            'code' => 'produktif_4',
            'question_text' => 'Apakah Anda, memiliki penyakit menyebabkan harus dirawat dan masih dalam proses pengobatan sampai sekarang?',
            'input_type' => 'radio',
            'order' => 6,
        ]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '1', 'label' => 'Ya', 'order' => 1]);
        HealthQuestionOption::create(['question_id' => $q->id, 'value' => '2', 'label' => 'Tidak', 'order' => 2]);

        // Q5: Bila ya, sebutkan
        HealthQuestion::create([
            'category_id' => $produktif->id,
            'code' => 'produktif_5',
            'question_text' => 'Bila ya, sebutkan',
            'input_type' => 'textarea',
            'order' => 7,
            'show_conditions' => json_encode([
                'depends_on' => 'produktif_4',
                'depends_value' => '1',
            ]),
        ]);

        // Q6: Hasil pemeriksaan darah (Table)
        $q = HealthQuestion::create([
            'category_id' => $produktif->id,
            'code' => 'produktif_6',
            'question_text' => 'Bagaimana hasil pemeriksaan darah',
            'input_type' => 'table',
            'order' => 8,
        ]);

        $darah_rows = [
            ['code' => 'gula_darah', 'label' => 'Gula Darah', 'unit' => 'mg/dl', 'ref' => 'Kurang dari 200 mg/dl (Sewaktu)'],
            ['code' => 'asam_urat', 'label' => 'Asam Urat', 'unit' => 'mg/dl', 'ref' => 'Pria : ≤ 7 mg/dl / Wanita : ≤ 6 mg/dl'],
            ['code' => 'kolesterol', 'label' => 'Kolesterol', 'unit' => 'mg/dl', 'ref' => 'Kurang dari 200 mg/dl'],
        ];

        foreach ($darah_rows as $idx => $row) {
            HealthQuestionTableRow::create([
                'question_id' => $q->id,
                'row_code' => $row['code'],
                'row_label' => $row['label'],
                'unit' => $row['unit'],
                'reference_value' => $row['ref'],
                'order' => $idx + 1,
            ]);
        }

        $this->command->info('✅ Children & Productive Age Health Questions seeded successfully!');
    }
}
