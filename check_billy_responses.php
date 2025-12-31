<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== BILLY WASOM RESPONSES ===\n\n";

// Get Billy Wasom resident
$billy = \App\Models\Resident::find(28);
if ($billy) {
    echo "Billy Wasom - NIK: {$billy->nik}\n";
    echo "Family ID: {$billy->family_id}\n\n";

    // Get all responses for Billy Wasom
    $responses = \App\Models\Response::where('resident_id', $billy->id)
        ->with('questionnaire')
        ->orderBy('id')
        ->get();

    echo 'Total responses: ' . $responses->count() . "\n\n";

    foreach ($responses as $response) {
        echo "Response ID: {$response->id}\n";
        echo "Questionnaire: {$response->questionnaire->title}\n";
        echo "Status: {$response->status}\n";
        echo "Entered by: {$response->entered_by_user_id}\n";
        echo "Started: {$response->started_at}\n";
        echo "Completed: {$response->completed_at}\n";
        echo "---\n";
    }
} else {
    echo "Billy Wasom (resident_id=28) tidak ditemukan\n";
}

// Check response #11 and #12
echo "\n=== RESPONSE #11 ===\n";
$r11 = \App\Models\Response::find(11);
if ($r11) {
    echo "ID: {$r11->id}\n";
    echo "Resident ID: {$r11->resident_id}\n";
    if ($r11->resident) {
        echo "Resident Name: {$r11->resident->nama_lengkap}\n";
    }
    echo "Status: {$r11->status}\n";
    echo "Entered by: {$r11->entered_by_user_id}\n";
} else {
    echo "Response #11 tidak ditemukan\n";
}

echo "\n=== RESPONSE #12 ===\n";
$r12 = \App\Models\Response::find(12);
if ($r12) {
    echo "ID: {$r12->id}\n";
    echo "Resident ID: {$r12->resident_id}\n";
    if ($r12->resident) {
        echo "Resident Name: {$r12->resident->nama_lengkap}\n";
    }
    echo "Status: {$r12->status}\n";
    echo "Entered by: {$r12->entered_by_user_id}\n";
} else {
    echo "Response #12 tidak ditemukan\n";
}
