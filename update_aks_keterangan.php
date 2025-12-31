<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔄 Updating AKS table rows with KETERANGAN...\n\n";

$updates = [
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

foreach ($updates as $rowCode => $note) {
    DB::table('health_question_table_rows')
        ->where('row_code', $rowCode)
        ->update(['note' => $note]);
    echo "✅ Updated {$rowCode}\n";
}

echo "\n✅ All AKS KETERANGAN updated!\n";
