<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FIXING BILLY WASOM RESPONSE ===\n\n";

// Get response #12
$response = \App\Models\Response::find(12);
if (!$response) {
    die("Response #12 tidak ditemukan\n");
}

echo "Response #12 sebelum update:\n";
echo "Resident ID: " . ($response->resident_id ?? 'NULL') . "\n";
echo "Status: {$response->status}\n\n";

// Update resident_id ke Billy Wasom (id=28)
$response->resident_id = 28;
$response->save();

echo "Response #12 setelah update:\n";
echo "Resident ID: {$response->resident_id}\n";
echo "Resident Name: {$response->resident->nama_lengkap}\n";
echo "Status: {$response->status}\n\n";

echo "✅ Billy Wasom sekarang muncul di list Aktivitas Terbaru!\n";
