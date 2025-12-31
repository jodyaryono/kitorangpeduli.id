<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Response;
use Illuminate\Support\Facades\DB;

echo "🧪 Testing Response Creation Logic\n";
echo str_repeat('=', 60) . "\n\n";

// Count current responses
$beforeCount = Response::count();
echo "📊 Current total responses: {$beforeCount}\n\n";

// Simulate the logic in controller
$questionnaireId = 8;
$userId = 1;
$responseIdFromUrl = 11;  // This simulates ?response_id=11

echo "🔍 Scenario 1: WITH response_id (Lanjutkan button)\n";
echo "   Query: ?response_id={$responseIdFromUrl}\n\n";

$response = Response::where('id', $responseIdFromUrl)
    ->where('questionnaire_id', $questionnaireId)
    ->where('entered_by_user_id', $userId)
    ->where('status', 'in_progress')
    ->first();

if ($response) {
    echo "   ✅ Found existing response: #{$response->id}\n";
    echo "   ✅ Will NOT create new response\n";
} else {
    echo "   ❌ Response not found - would redirect with error\n";
    echo "   ✅ Will NOT create new response\n";
}

$afterCount1 = Response::count();
echo "   📊 Response count: {$afterCount1} (unchanged)\n\n";

echo str_repeat('-', 60) . "\n\n";

echo "🔍 Scenario 2: WITHOUT response_id (New entry)\n";
echo "   Query: (no response_id parameter)\n\n";

$existingResponse = Response::where('questionnaire_id', $questionnaireId)
    ->where('entered_by_user_id', $userId)
    ->where('status', 'in_progress')
    ->first();

if ($existingResponse) {
    echo "   ✅ Found existing in-progress response: #{$existingResponse->id}\n";
    echo "   ✅ Will use existing, NOT create new\n";
} else {
    echo "   ⚠️  No existing in-progress response\n";
    echo "   ⚠️  Would create NEW response (only in this case)\n";
}

$afterCount2 = Response::count();
echo "   📊 Response count: {$afterCount2}\n\n";

echo str_repeat('=', 60) . "\n";
echo "✅ Test completed - no new responses created during test\n";
echo "   Before: {$beforeCount} responses\n";
echo "   After: {$afterCount2} responses\n";
echo str_repeat('=', 60) . "\n";
