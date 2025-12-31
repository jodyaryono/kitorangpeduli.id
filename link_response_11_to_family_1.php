<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Family;
use App\Models\Resident;
use App\Models\Response;

echo "🔧 Linking Response #11 to Existing Family #1\n";
echo str_repeat('=', 60) . "\n\n";

$response = Response::find(11);
$family = Family::find(1);

if (!$response) {
    echo "❌ Response #11 not found!\n";
    exit;
}

if (!$family) {
    echo "❌ Family #1 not found!\n";
    exit;
}

echo "📋 Current State:\n";
echo '   Response #11 resident_id: ' . ($response->resident_id ?? 'NULL') . "\n";
echo '   Family #1 has ' . $family->residents->count() . " members\n\n";

// Find kepala keluarga in Family #1
$kepalaKeluarga = Resident::where('family_id', 1)
    ->where('family_relation_id', 1)
    ->first();

if (!$kepalaKeluarga) {
    echo "⚠️ No kepala keluarga found in Family #1\n";
    echo "   Looking for first member instead...\n";
    $kepalaKeluarga = Resident::where('family_id', 1)->first();
}

if ($kepalaKeluarga) {
    echo "👤 Found: {$kepalaKeluarga->nama_lengkap} (NIK: {$kepalaKeluarga->nik})\n\n";

    // Update response
    $response->update(['resident_id' => $kepalaKeluarga->id]);

    echo "✅ Response #11 updated!\n";
    echo "   resident_id is now: {$kepalaKeluarga->id}\n";
    echo "   Responden: {$kepalaKeluarga->nama_lengkap}\n";
    echo "   NIK: {$kepalaKeluarga->nik}\n\n";

    echo "🎉 Success! Now refresh the questionnaire page.\n";
} else {
    echo "❌ No residents found in Family #1\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
