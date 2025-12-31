<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HealthQuestion;
use App\Models\HealthQuestionTableRow;

echo "🔄 Updating lansia question data...\n";

// Update H.3 table rows
$q3 = HealthQuestion::where('code', 'H3')->first();
if ($q3) {
    echo "Updating H.3 table rows...\n";

    $row1 = HealthQuestionTableRow::where('question_id', $q3->id)
        ->where('row_code', 'gula_darah')
        ->first();
    if ($row1) {
        $row1->update([
            'row_label' => 'Gula Darah',
            'reference_value' => 'Kurang dari 200 mg/dl (Sewaktu)'
        ]);
        echo "✅ Updated gula_darah row\n";
    }

    $row2 = HealthQuestionTableRow::where('question_id', $q3->id)
        ->where('row_code', 'asam_urat')
        ->first();
    if ($row2) {
        $row2->update([
            'row_label' => 'Asam Urat',
            'reference_value' => 'Pria : ≤ 7 mg/dl / Wanita : ≤ 6 mg/dl'
        ]);
        echo "✅ Updated asam_urat row\n";
    }
}

// Update H.4 table rows with KETERANGAN
$q4 = HealthQuestion::where('code', 'H4')->first();
if ($q4) {
    echo "\nUpdating H.4 table rows...\n";

    $keteranganData = [
        'aks_1' => 'Tidak terkendali / tak teratur (perlu pencahar)|Kadang-kadang tak terkendali (1 x / minggu)|Terkendali teratur',
        'aks_2' => 'Tidak terkendali atau pakai kateter|Kadang-kadang tak terkendali (hanya 1 x / 24 jam)|Mandiri',
        'aks_3' => 'Butuh pertolongan orang lain|Mandiri',
        'aks_4' => 'Tergantung pertolongan orang lain|Perlu pertolongan pada beberapa kegiatan tetapi dapat mengerjakan sendiri beberapa kegiatan yang lain|Mandiri',
        'aks_5' => 'Tidak mampu|Perlu pertolongan memotong makanan|Mandiri',
        'aks_6' => 'Tidak mampu|Perlu banyak bantuan untuk bisa duduk (2 orang)|Bantuan minimal 1 orang|Mandiri',
        'aks_7' => 'Tidak mampu|Bisa (pindah) dengan kursi roda|Berjalan dengan bantuan 1 orang|Mandiri',
        'aks_8' => 'Tergantung orang lain|Sebagian dibantu (misalnya: mengancing baju)|Mandiri',
        'aks_9' => 'Tidak mampu|Butuh pertolongan (alat bantu)|Mandiri',
        'aks_10' => 'Tergantung orang lain|Mandiri',
    ];

    $fungsiData = [
        'aks_1' => 'Mengendalikan rangsang Buang Air Besar (BAB)',
        'aks_2' => 'Mengendalikan rangsang Buang Air Kecil (BAK)',
        'aks_3' => 'Membersihkan diri (mencuci wajah, menyikat rambut, mencukur kumis, sikat gigi)',
        'aks_4' => 'Penggunaan WC (keluar masuk WC, melepas/memakai celana, cebok, menyiram)',
        'aks_5' => 'Makan minum (jika makan harus berupa potongan, dianggap dibantu)',
        'aks_6' => 'Bergerak dari kursi roda ke tempat tidur dan sebaliknya (termasuk duduk di tempat tidur)',
        'aks_7' => 'Berjalan di tempat rata (atau jika tidak bisa berjalan, menjalankan kursi roda)',
        'aks_8' => 'Berpakaian (termasuk memasang tali sepatu, mengencangkan sabuk)',
        'aks_9' => 'Naik turun tangga',
        'aks_10' => 'Mandi',
    ];

    foreach ($keteranganData as $rowCode => $keterangan) {
        $row = HealthQuestionTableRow::where('question_id', $q4->id)
            ->where('row_code', $rowCode)
            ->first();
        if ($row) {
            $row->update([
                'row_label' => $fungsiData[$rowCode],
                'note' => $keterangan
            ]);
            echo "✅ Updated {$rowCode}\n";
        }
    }
}

echo "\n✅ All lansia question data updated!\n";
