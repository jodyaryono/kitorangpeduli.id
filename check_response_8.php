<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== DETAILED CHECK FOR RESPONSE #8 ===\n\n";

$response = DB::table('responses')->where('id', 8)->first();

echo "Response #8:\n";
echo "  questionnaire_id: {$response->questionnaire_id}\n";
echo '  resident_id: ' . ($response->resident_id ?? 'NULL') . "\n";
echo "  status: {$response->status}\n\n";

// Check health responses for this response
$healthResponses = DB::table('resident_health_responses')
    ->where('response_id', 8)
    ->get();

echo 'Health Responses: ' . $healthResponses->count() . "\n\n";

foreach ($healthResponses as $hr) {
    echo "  resident_id: {$hr->resident_id}\n";
    echo "  question_code: {$hr->question_code}\n";
    echo "  answer: {$hr->answer}\n\n";

    // Get resident info
    $resident = DB::table('residents')->where('id', $hr->resident_id)->first();
    if ($resident) {
        echo "  Resident: {$resident->nama_lengkap} (NIK: {$resident->nik})\n";
        echo "  Family ID: {$resident->family_id}\n";
        echo "  Family Relation ID: {$resident->family_relation_id}\n\n";
    }
}

// Find family members for response 8
echo "\n=== FINDING FAMILY FOR RESPONSE #8 ===\n\n";

$firstHealth = DB::table('resident_health_responses')
    ->where('response_id', 8)
    ->first();

if ($firstHealth) {
    $resident = DB::table('residents')->where('id', $firstHealth->resident_id)->first();

    if ($resident && $resident->family_id) {
        echo "Family ID: {$resident->family_id}\n\n";

        $allMembers = DB::table('residents')
            ->where('family_id', $resident->family_id)
            ->orderBy('family_relation_id')
            ->get();

        echo "All Family Members:\n";
        foreach ($allMembers as $member) {
            $isKepala = $member->family_relation_id == 1 ? ' ← KEPALA KELUARGA' : '';
            echo "  - ID: {$member->id}, {$member->nama_lengkap}, NIK: {$member->nik}, Relation: {$member->family_relation_id}{$isKepala}\n";
        }
    }
}
