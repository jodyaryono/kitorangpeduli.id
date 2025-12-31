<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HealthQuestion;
use App\Models\HealthQuestionTableRow;

echo "🔄 Updating SKILAS (H6) data...\n\n";

$q6 = HealthQuestion::where('code', 'H6')->first();

if ($q6) {
    // Update to table_mixed type untuk support checkbox dan text input
    $q6->update(['input_type' => 'table_mixed']);

    $skilasData = [
        'skilas_1' => [
            'label' => 'Penurunan kognitif',
            'note' => 'Mengingat tiga kata',
            'input_type' => 'checkbox'
        ],
        'skilas_2' => [
            'label' => 'Penurunan kognitif',
            'note' => 'Orientasi waktu dan tempat',
            'input_type' => 'checkbox'
        ],
        'skilas_3' => [
            'label' => 'Penurunan kognitif',
            'note' => 'Ulangi ketiga kata tadi',
            'input_type' => 'checkbox'
        ],
        'skilas_4' => [
            'label' => 'Keterbatasan Mobilisasi',
            'note' => 'Tes berdiri dari kursi',
            'input_type' => 'checkbox'
        ],
        'skilas_5' => [
            'label' => 'Malnutrisi',
            'note' => '1. Apakah berat badan Anda berkurang >3 kg dalam 3 bulan terakhir atau pakaian menjadi lebih longgar?',
            'input_type' => 'checkbox'
        ],
        'skilas_6' => [
            'label' => 'Malnutrisi',
            'note' => '2. Apakah Anda hilang nafsu makan atau mengalami kesulitan makan (misal batuk atau tersedak saat makan, menggunakan selang makan/sonde)?',
            'input_type' => 'checkbox'
        ],
        'skilas_7' => [
            'label' => 'Malnutrisi',
            'note' => '3. Apakah ukuran lingkar lengan atas (LILA) <21 cm?',
            'input_type' => 'checkbox'
        ],
        'skilas_8' => [
            'label' => 'Gangguan Penglihatan',
            'note' => '1. Apakah anda mengalami masalah pada mata: kesulitan melihat jauh, membaca, penyakit mata, atau sedang dalam pengobatan medis (diabetes, tekanan darah tinggi)? Jika tidak, lakukan TES MELIHAT',
            'input_type' => 'radio',
            'reference_value' => 'Ya,|Ya Jika tidak, lakukan TES MELIHAT'
        ],
        'skilas_9' => [
            'label' => 'Gangguan Penglihatan',
            'note' => '2. TES MELIHAT: Apakah jawaban hitungi jari benar dalam 3 kali berturut turut?',
            'input_type' => 'radio',
            'reference_value' => 'HASIL TES MELIHAT|Tidak, Kemungkinan ada gangguan penglihatan berat, hingga buta'
        ],
        'skilas_10' => [
            'label' => 'Gangguan pendengaran',
            'note' => 'Mendengar bisikan saat TES BISIK',
            'input_type' => 'radio',
            'reference_value' => 'Tidak|Jika tidak dapat dilakukan tes bisik, rujuk puskesmas'
        ],
        'skilas_11' => [
            'label' => 'Gejala depresi',
            'note' => 'Selama dua minggu terakhir, apakah Anda merasa terganggu oleh: 1. Perasaan sedih, tertekan, atau putus asa',
            'input_type' => 'checkbox'
        ],
        'skilas_12' => [
            'label' => 'Gejala depresi',
            'note' => '2. Sedikit minat atau kesenangan dalam melakukan sesuatu',
            'input_type' => 'checkbox'
        ],
    ];

    foreach ($skilasData as $rowCode => $data) {
        $row = HealthQuestionTableRow::where('question_id', $q6->id)
            ->where('row_code', $rowCode)
            ->first();
        if ($row) {
            $row->update([
                'row_label' => $data['label'],
                'note' => $data['note'],
                'input_type' => $data['input_type'],
                'reference_value' => $data['reference_value'] ?? null
            ]);
            echo "✅ Updated {$rowCode}\n";
        }
    }

    echo "\n✅ SKILAS data updated!\n";
} else {
    echo "❌ H6 question not found!\n";
}
