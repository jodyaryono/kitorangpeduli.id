<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Family;
use App\Models\Resident;
use App\Models\Response;

echo "\n=== CHECKING FAMILY & RESIDENTS ===\n\n";

// Check Family 1
$family = Family::find(1);

if ($family) {
    echo "✅ Family #1 exists\n";
    echo "  - No KK: {$family->no_kk}\n";
    echo "  - Alamat: {$family->alamat}\n\n";

    echo "Residents in Family #1:\n";
    $residents = Resident::where('family_id', 1)->get();

    if ($residents->count() > 0) {
        foreach ($residents as $resident) {
            echo "  - ID: {$resident->id}, Nama: {$resident->nama_lengkap}, NIK: {$resident->nik}, Jenis Kelamin: {$resident->jenis_kelamin}\n";
        }
    } else {
        echo "  ❌ No residents found in Family #1\n";
    }
} else {
    echo "❌ Family #1 not found\n";
}

echo "\n=== CHECKING RESPONSES ===\n";
$responses = Response::orderBy('id', 'desc')->take(5)->get();

if ($responses->count() > 0) {
    foreach ($responses as $r) {
        echo "Response #{$r->id} - Questionnaire #{$r->questionnaire_id} - Resident ID: " . ($r->resident_id ?? 'NULL') . " - Status: {$r->status}\n";
    }
} else {
    echo "No responses found\n";
}
