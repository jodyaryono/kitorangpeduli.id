<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Resident;
use App\Models\Response;

echo "\n=== LINKING RESPONSE #8 TO FAMILY ===\n\n";

// Get Response 8
$response = Response::find(8);
if (!$response) {
    echo "Response #8 not found\n";
    exit;
}

// Get kepala keluarga from Family ID 1
$kepala = Resident::where('family_id', 1)
    ->where('family_relation_id', 1)
    ->first();

if (!$kepala) {
    echo "Kepala keluarga tidak ditemukan\n";
    // Try any member from family
    $kepala = Resident::where('family_id', 1)
        ->orderBy('id')
        ->first();
}

if ($kepala) {
    echo "Found resident: {$kepala->nama_lengkap} (NIK: {$kepala->nik})\n";
    echo "Linking to Response #8...\n\n";

    $response->resident_id = $kepala->id;
    $response->save();

    echo "✅ SUCCESS!\n\n";
    echo "Response #8 sekarang linked ke:\n";
    echo "  - Resident ID: {$kepala->id}\n";
    echo "  - Nama: {$kepala->nama_lengkap}\n";
    echo "  - NIK: {$kepala->nik}\n";
    echo "  - Family ID: {$kepala->family_id}\n\n";

    echo "Sekarang refresh halaman officer entry untuk melihat nama dan NIK!\n";
} else {
    echo "❌ Tidak ada resident di Family ID 1\n";
}
