<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Response;
use App\Models\User;

echo "🔍 Checking All In-Progress Responses for Questionnaire #8\n";
echo str_repeat('=', 60) . "\n\n";

$responses = Response::where('status', 'in_progress')
    ->where('questionnaire_id', 8)
    ->with('enteredBy')
    ->get();

echo 'Found ' . $responses->count() . " in-progress response(s) for Q8\n\n";

foreach ($responses as $response) {
    echo "Response #{$response->id}:\n";
    echo '   Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
    echo '   Entered by: ' . ($response->enteredBy ? $response->enteredBy->name : 'Unknown') . " (ID: {$response->entered_by_user_id})\n";
    echo "   Started: {$response->started_at}\n";
    echo "   Updated: {$response->updated_at}\n";

    // Count answers
    $answerCount = \App\Models\Answer::where('response_id', $response->id)->count();
    echo "   Answers: {$answerCount}\n";

    // Check for family members
    if ($response->resident_id) {
        $resident = \App\Models\Resident::find($response->resident_id);
        if ($resident && $resident->family_id) {
            $familyMembers = \App\Models\Resident::where('family_id', $resident->family_id)->count();
            echo "   Family members: {$familyMembers}\n";
        }
    }

    echo "\n";
}

echo str_repeat('=', 60) . "\n";
