<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "📦 Exporting Billy Wasom data...\n\n";

// Find Billy Wasom
$billy = DB::table('residents')->where('nik', '9171042310680001')->first();

if (!$billy) {
    echo "❌ Billy Wasom not found!\n";
    exit;
}

echo "✅ Found Billy Wasom (ID: {$billy->id})\n";

// Get family
$family = DB::table('families')->where('id', $billy->family_id)->first();
echo "✅ Found family (ID: {$family->id}, No KK: {$family->no_kk})\n";

// Get all family members
$familyMembers = DB::table('residents')->where('family_id', $family->id)->get();
echo '✅ Found ' . $familyMembers->count() . " family members\n";

// Get responses
$responses = DB::table('responses')->where('resident_id', $billy->id)->get();
echo '✅ Found ' . $responses->count() . " responses\n";

// Get family health responses
$familyHealthResponses = DB::table('family_health_responses')
    ->where('family_id', $family->id)
    ->get();
echo '✅ Found ' . $familyHealthResponses->count() . " family health responses\n";

// Get resident health responses
$residentHealthResponses = DB::table('resident_health_responses')
    ->whereIn('resident_id', $familyMembers->pluck('id'))
    ->get();
echo '✅ Found ' . $residentHealthResponses->count() . " resident health responses\n";

// Get answers
$answerIds = [];
foreach ($responses as $response) {
    $answers = DB::table('answers')->where('response_id', $response->id)->get();
    $answerIds = array_merge($answerIds, $answers->pluck('id')->toArray());
    echo "  - Response {$response->id}: " . $answers->count() . " answers\n";
}

// Create export data
$exportData = [
    'family' => json_decode(json_encode($family), true),
    'residents' => json_decode(json_encode($familyMembers), true),
    'responses' => json_decode(json_encode($responses), true),
    'answers' => [],
    'family_health_responses' => json_decode(json_encode($familyHealthResponses), true),
    'resident_health_responses' => json_decode(json_encode($residentHealthResponses), true),
];

foreach ($responses as $response) {
    $answers = DB::table('answers')->where('response_id', $response->id)->get();
    foreach ($answers as $answer) {
        $exportData['answers'][] = json_decode(json_encode($answer), true);
    }
}

// Save to JSON file
$jsonData = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents('billy_wasom_export.json', $jsonData);

echo "\n✅ Data exported to billy_wasom_export.json\n";
echo "📊 Total data:\n";
echo "   - Families: 1\n";
echo '   - Residents: ' . count($exportData['residents']) . "\n";
echo '   - Responses: ' . count($exportData['responses']) . "\n";
echo '   - Answers: ' . count($exportData['answers']) . "\n";
echo '   - Family Health: ' . count($exportData['family_health_responses']) . "\n";
echo '   - Resident Health: ' . count($exportData['resident_health_responses']) . "\n";
