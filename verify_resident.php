<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$resident = App\Models\Resident::orderBy('id', 'desc')->first();

if ($resident) {
    echo "Latest Resident:\n";
    echo str_repeat('=', 80) . "\n";
    echo "ID: {$resident->id}\n";
    echo "Nama: {$resident->nama_lengkap}\n";
    echo "Jenis Kelamin: '{$resident->jenis_kelamin}'\n";
    echo "Family ID: {$resident->family_id}\n";
    echo "Hubungan: {$resident->hubungan_keluarga}\n";
    echo "\nVerification:\n";
    echo "✅ jenis_kelamin = '{$resident->jenis_kelamin}' "
        . ($resident->jenis_kelamin === 'L'
            ? '(Correct! L = Laki-laki/Pria)'
            : ($resident->jenis_kelamin === 'P'
                ? '(Correct! P = Perempuan/Wanita)'
                : '(WRONG! Should be L or P)')) . "\n";
} else {
    echo "No residents found\n";
}
