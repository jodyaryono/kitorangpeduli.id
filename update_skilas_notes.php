<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 Updating SKILAS notes...\n\n";

$updates = [
    'skilas_1' => '1. Mengingat tiga kata: bunga, pintu, nasi (sebagai contoh)',
    'skilas_2' => '2. Orientasi terhadap waktu dan tempat: Tanggal berapa sekarang? Di mana kamu berada sekarang (rumah, klinik, dsb.)?',
    'skilas_3' => '3. Ulangi ketiga kata tadi',
    'skilas_4' => 'Tes berdiri dari kursi: berdiri dari kursi lima kali tanpa menggunakan tangan. Apakah orang tersebut dapat berdiri di kursi sebanyak 5 kali',
    'skilas_5' => '1. Apakah berat badan Anda berkurang >3 kg dalam 3 bulan terakhir atau pakaian menjadi lebih longgar?',
    'skilas_6' => '2. Apakah Anda hilang nafsu makan atau mengalami kesulitan makan (misal batuk atau tersedak saat makan, menggunakan selang makan/sonde)?',
    'skilas_7' => '3. Apakah ukuran lingkar lengan atas (LILA) <21 cm?',
    'skilas_8' => '1. Apakah anda mengalami masalah pada mata: kesulitan melihat jauh, membaca, penyakit mata, atau sedang dalam pengobatan medis (diabetes, tekanan darah tinggi)? Jika tidak, lakukan TES MELIHAT',
    'skilas_9' => '2. TES MELIHAT: Apakah jawaban hitung jari benar dalam 3 kali berturut turut?',
    'skilas_10' => 'Mendengar bisikan saat TES BISIK',
    'skilas_11' => 'Selama dua minggu terakhir, apakah Anda merasa terganggu oleh:',
    'skilas_11a' => '1. Perasaan sedih, tertekan, atau putus asa',
    'skilas_11b' => '2. Sedikit minat atau kesenangan dalam melakukan sesuatu',
];

foreach ($updates as $rowCode => $note) {
    $updated = DB::table('health_question_table_rows')
        ->where('row_code', $rowCode)
        ->update(['note' => $note]);

    if ($updated) {
        echo "✅ Updated {$rowCode}\n";
    } else {
        echo "❌ Failed {$rowCode}\n";
    }
}

echo "\n✅ All SKILAS notes updated!\n";
