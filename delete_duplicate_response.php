<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\Response;

// Delete Response ID 3 (the duplicate with NULL resident_id)
$responseId = 3;

$response = Response::find($responseId);

if (!$response) {
    echo "❌ Response ID {$responseId} not found\n";
    exit;
}

echo "🗑️ Deleting Response ID {$responseId}...\n";
echo "  - Questionnaire ID: {$response->questionnaire_id}\n";
echo '  - Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
echo "  - Created: {$response->created_at}\n\n";

// Delete related answers first
$answerCount = Answer::where('response_id', $responseId)->count();
if ($answerCount > 0) {
    Answer::where('response_id', $responseId)->delete();
    echo "✅ Deleted {$answerCount} related answers\n";
}

// Delete the response
$response->delete();
echo "✅ Deleted Response ID {$responseId}\n\n";

// Verify
$remainingResponses = Response::all();
echo '📋 Remaining Responses: ' . $remainingResponses->count() . "\n\n";

foreach ($remainingResponses as $r) {
    echo "Response ID: {$r->id}\n";
    echo "  - Questionnaire ID: {$r->questionnaire_id}\n";
    echo '  - Resident ID: ' . ($r->resident_id ?? 'NULL') . "\n";
    if ($r->resident_id && $r->resident) {
        echo "  - Resident Name: {$r->resident->nama_lengkap}\n";
    }
    echo "\n";
}
