<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Response;

echo "🔍 Checking Response #8\n";
echo str_repeat('=', 60) . "\n\n";

$response = Response::find(8);

if (!$response) {
    echo "❌ Response #8 not found!\n\n";

    // Check all responses for this officer
    echo "📋 All Responses for User ID 1:\n\n";
    $allResponses = Response::where('entered_by_user_id', 1)->get();

    foreach ($allResponses as $r) {
        echo "Response #{$r->id}:\n";
        echo "   Questionnaire: {$r->questionnaire_id}\n";
        echo "   Status: {$r->status}\n";
        echo '   Resident ID: ' . ($r->resident_id ?? 'NULL') . "\n";
        echo "   Started: {$r->started_at}\n";
        echo '   Answers: ' . \App\Models\Answer::where('response_id', $r->id)->count() . "\n";
        echo "\n";
    }
} else {
    echo "✅ Found Response #8:\n";
    echo "   Questionnaire: {$response->questionnaire_id}\n";
    echo "   Status: {$response->status}\n";
    echo "   User: {$response->entered_by_user_id}\n";
    echo '   Resident: ' . ($response->resident_id ?? 'NULL') . "\n";
}

echo str_repeat('=', 60) . "\n";
