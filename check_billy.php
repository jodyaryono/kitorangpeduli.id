<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find Billy Wasom
echo "=== Finding Billy Wasom ===\n\n";
$billy = DB::table('residents')->where('nama_lengkap', 'like', '%Billy%')->first();
if ($billy) {
    echo "Found: {$billy->nama_lengkap} (ID: {$billy->id}, NIK: {$billy->nik})\n\n";

    // Check all responses for Billy
    echo "=== All Responses for Billy Wasom ===\n\n";
    $billyResponses = DB::table('responses')
        ->where('resident_id', $billy->id)
        ->orderBy('id')
        ->get();

    foreach ($billyResponses as $r) {
        echo "ID: {$r->id} | Status: {$r->status} | Questionnaire: {$r->questionnaire_id} | Updated: {$r->updated_at}\n";
    }

    if ($billyResponses->isEmpty()) {
        echo "NO RESPONSES FOUND for Billy Wasom\n";
    }
} else {
    echo "Billy Wasom NOT FOUND\n";
}

echo "\n\n=== Checking Response #11 and #12 ===\n\n";

$responses = DB::table('responses')
    ->whereIn('id', [11, 12])
    ->orderBy('id')
    ->get();

if ($responses->isEmpty()) {
    echo "No responses found with ID 11 or 12\n";
}

foreach ($responses as $r) {
    echo "ID: {$r->id}\n";
    echo "Status: {$r->status}\n";
    echo 'Resident ID: ' . ($r->resident_id ?: 'NULL') . "\n";
    echo "Entered by User: {$r->entered_by_user_id}\n";
    echo "Questionnaire: {$r->questionnaire_id}\n";
    echo "Updated: {$r->updated_at}\n";

    if ($r->resident_id) {
        $resident = DB::table('residents')->where('id', $r->resident_id)->first();
        if ($resident) {
            echo "Resident Name: {$resident->nama_lengkap}\n";
        }
    }

    echo "\n---\n\n";
}

// Check all recent responses by user
echo "=== Recent Responses (Last 10) ===\n\n";
$recent = DB::table('responses')
    ->where('entered_by_user_id', 1)
    ->orderByDesc('updated_at')
    ->limit(10)
    ->get(['id', 'status', 'resident_id', 'questionnaire_id', 'updated_at']);

foreach ($recent as $r) {
    $residentName = 'NULL';
    if ($r->resident_id) {
        $resident = DB::table('residents')->where('id', $r->resident_id)->value('nama_lengkap');
        if ($resident) {
            $residentName = $resident;
        }
    }
    echo "ID: {$r->id} | Status: {$r->status} | Resident: {$residentName} | Updated: {$r->updated_at}\n";
}
