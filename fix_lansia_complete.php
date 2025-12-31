<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HealthQuestion;
use App\Models\HealthQuestionTableRow;

echo "🔄 Updating lansia questions...\n\n";

// 1. Add Kolestrol to H.3
$q3 = HealthQuestion::where('code', 'H3')->first();
if ($q3) {
    echo "Adding Kolestrol to H.3...\n";

    // Check if already exists
    $existing = HealthQuestionTableRow::where('question_id', $q3->id)
        ->where('row_code', 'kolestrol')
        ->first();

    if (!$existing) {
        HealthQuestionTableRow::create([
            'question_id' => $q3->id,
            'row_code' => 'kolestrol',
            'row_label' => 'Kolestrol',
            'reference_value' => 'Kurang dari 200 mg/dl',
            'order' => 3
        ]);
        echo "✅ Added Kolestrol row\n";
    } else {
        echo "⏭️  Kolestrol already exists\n";
    }
}

// 2. Update SKILAS with detailed questions from paste3 and paste4
$q6 = HealthQuestion::where('code', 'H6')->first();
if ($q6) {
    echo "\nUpdating SKILAS (H.6) with detailed structure...\n";

    // Delete existing rows
    DB::table('health_question_table_rows')->where('question_id', $q6->id)->delete();

    $skilasDetailedData = [
        [
            'row_code' => 'skilas_1',
            'row_label' => 'Penurunan kognitif',
            'note' => '1. Mengingat tiga kata: bunga, pintu, nasi (sebagai contoh)',
            'input_type' => 'checkbox',
            'order' => 1
        ],
        [
            'row_code' => 'skilas_2',
            'row_label' => 'Penurunan kognitif',
            'note' => '2. Orientasi terhadap waktu dan tempat: Tanggal berapa sekarang? Di mana kamu berada sekarang (rumah, klinik, dsb.)?',
            'input_type' => 'checkbox',
            'reference_value' => 'Salah pada salah satu pertanyaan',
            'order' => 2
        ],
        [
            'row_code' => 'skilas_3',
            'row_label' => 'Penurunan kognitif',
            'note' => '3. Ulangi ketiga kata tadi',
            'input_type' => 'checkbox',
            'reference_value' => 'Tidak dapat mengulang ketiga kata',
            'order' => 3
        ],
        [
            'row_code' => 'skilas_4',
            'row_label' => 'Keterbatasan Mobilisasi',
            'note' => 'Tes berdiri dari kursi: berdiri dari kursi lima kali tanpa menggunakan tangan. Apakah orang tersebut dapat berdiri di kursi sebanyak 5 kali',
            'input_type' => 'checkbox',
            'reference_value' => 'Tidak',
            'order' => 4
        ],
        [
            'row_code' => 'skilas_5',
            'row_label' => 'Malnutrisi',
            'note' => '1. Apakah berat badan Anda berkurang >3 kg dalam 3 bulan terakhir atau pakaian menjadi lebih longgar?',
            'input_type' => 'checkbox',
            'reference_value' => 'Ya',
            'order' => 5
        ],
        [
            'row_code' => 'skilas_6',
            'row_label' => 'Malnutrisi',
            'note' => '2. Apakah Anda hilang nafsu makan atau mengalami kesulitan makan (misal batuk atau tersedak saat makan, menggunakan selang makan/sonde)?',
            'input_type' => 'checkbox',
            'reference_value' => 'Ya',
            'order' => 6
        ],
        [
            'row_code' => 'skilas_7',
            'row_label' => 'Malnutrisi',
            'note' => '3. Apakah ukuran lingkar lengan atas (LILA) <21 cm?',
            'input_type' => 'checkbox',
            'reference_value' => 'Ya',
            'order' => 7
        ],
        [
            'row_code' => 'skilas_8',
            'row_label' => 'Gangguan Penglihatan',
            'note' => '1. Apakah anda mengalami masalah pada mata: kesulitan melihat jauh, membaca, penyakit mata, atau sedang dalam pengobatan medis (diabetes, tekanan darah tinggi)? Jika tidak, lakukan TES MELIHAT',
            'input_type' => 'checkbox',
            'reference_value' => 'Ya,|Jika tidak, lakukan TES MELIHAT',
            'order' => 8
        ],
        [
            'row_code' => 'skilas_9',
            'row_label' => 'Gangguan Penglihatan',
            'note' => '2. TES MELIHAT: Apakah jawaban hitung jari benar dalam 3 kali berturut turut?',
            'input_type' => 'checkbox',
            'reference_value' => 'HASIL TES MELIHAT|Tidak, Kemungkinan ada gangguan penglihatan berat, hingga buta',
            'order' => 9
        ],
        [
            'row_code' => 'skilas_10',
            'row_label' => 'Gangguan pendengaran',
            'note' => 'Mendengar bisikan saat TES BISIK',
            'input_type' => 'checkbox',
            'reference_value' => 'Tidak|Jika tidak dapat dilakukan tes bisik, rujuk puskesmas',
            'order' => 10
        ],
        [
            'row_code' => 'skilas_11',
            'row_label' => 'Gejala depresi',
            'note' => 'Selama dua minggu terakhir, apakah Anda merasa terganggu oleh:',
            'input_type' => 'text',
            'reference_value' => '',
            'order' => 11
        ],
        [
            'row_code' => 'skilas_11a',
            'row_label' => 'Gejala depresi',
            'note' => '1. Perasaan sedih, tertekan, atau putus asa',
            'input_type' => 'checkbox',
            'reference_value' => 'Ya',
            'order' => 12
        ],
        [
            'row_code' => 'skilas_11b',
            'row_label' => 'Gejala depresi',
            'note' => '2. Sedikit minat atau kesenangan dalam melakukan sesuatu',
            'input_type' => 'checkbox',
            'reference_value' => 'Ya',
            'order' => 13
        ],
    ];

    foreach ($skilasDetailedData as $data) {
        HealthQuestionTableRow::create([
            'question_id' => $q6->id,
            'row_code' => $data['row_code'],
            'row_label' => $data['row_label'],
            'note' => $data['note'],
            'input_type' => $data['input_type'],
            'reference_value' => $data['reference_value'] ?? null,
            'order' => $data['order']
        ]);
        echo "✅ Created {$data['row_code']}\n";
    }
}

// 3. Change H.5 to info type (keterangan only)
$q5 = HealthQuestion::where('code', 'H5')->first();
if ($q5) {
    echo "\nChanging H.5 to info type...\n";
    $q5->update([
        'input_type' => 'info',
        'question_text' => 'Berdasarkan penilaian di atas, bagaimana status kemandirian Lansia?',
        'question_note' => 'Kriteria: Mandiri (A) - 20 | Ketergantungan ringan (B) - 12 – 19 | Ketergantungan sedang (C) - 9 – 11 | Ketergantungan berat (D) - 5 – 8 | Ketergantungan total (E) - 0 – 4'
    ]);
    // Delete options
    DB::table('health_question_options')->where('question_id', $q5->id)->delete();
    echo "✅ H.5 changed to info type\n";
}

echo "\n✅ All updates completed!\n";
