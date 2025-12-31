<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Family;
use App\Models\Resident;
use App\Models\Response;

echo "🔍 Checking ALL Data for Questionnaire #8\n";
echo str_repeat('=', 60) . "\n\n";

// All responses for Q8
$allResponses = Response::where('questionnaire_id', 8)->get();
echo '📋 All Responses for Q8: ' . $allResponses->count() . "\n\n";

foreach ($allResponses as $response) {
    echo "Response #{$response->id}:\n";
    echo "   Status: {$response->status}\n";
    echo '   Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
    echo '   Entered by user ID: ' . ($response->entered_by_user_id ?? 'NULL') . "\n";
    echo "   Started: {$response->started_at}\n";
    echo '   Answers: ' . \App\Models\Answer::where('response_id', $response->id)->count() . "\n";
    echo "\n";
}

// All families
echo "\n👨‍👩‍👧‍👦 All Families:\n";
$families = Family::all();
echo 'Total: ' . $families->count() . "\n\n";

foreach ($families as $family) {
    echo "Family #{$family->id}:\n";
    echo "   No KK: {$family->no_kk}\n";
    echo "   Alamat: {$family->alamat}\n";

    $members = Resident::where('family_id', $family->id)->get();
    echo '   Members (' . $members->count() . "):\n";
    foreach ($members as $member) {
        echo "     - {$member->nama_lengkap} (NIK: {$member->nik})\n";
    }
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
