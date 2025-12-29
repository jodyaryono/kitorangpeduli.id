<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\Family;
use App\Models\Resident;
use App\Models\Response;

echo "Manually syncing KK data for existing response\n";
echo str_repeat('=', 80) . "\n\n";

$response = Response::find(2);

if (!$response) {
    echo "Response not found!\n";
    exit;
}

echo "Processing Response ID: {$response->id}\n";
echo "Questionnaire ID: {$response->questionnaire_id}\n";
echo "Status: {$response->status}\n\n";

// Question IDs for family data
$familyQuestionMap = [
    214 => 'province_id',
    215 => 'regency_id',
    216 => 'district_id',
    217 => 'village_id',
    220 => 'rt',
    219 => 'rw',
    225 => 'alamat',
    269 => 'kepala_keluarga',
    223 => 'no_kk',
    266 => 'kk_image_path',
];

// Get all answers for this response
$answers = Answer::where('response_id', $response->id)
    ->whereIn('question_id', array_keys($familyQuestionMap))
    ->get()
    ->keyBy('question_id');

echo "Found {$answers->count()} KK-related answers\n\n";

// Prepare family data
$familyData = [];

foreach ($familyQuestionMap as $questionId => $column) {
    $answer = $answers->get($questionId);
    if (!$answer)
        continue;

    if ($questionId == 266) {
        // File upload
        if ($answer->media_path) {
            $familyData[$column] = $answer->media_path;
        }
    } else {
        $value = $answer->answer_text ?? $answer->answer_numeric;
        if ($value && $value != '-') {
            $familyData[$column] = $value;
        }
    }
}

echo "Family data to save:\n";
print_r($familyData);
echo "\n";

if (empty($familyData)) {
    echo "No valid family data found!\n";
    exit;
}

// Try to find or create family
$family = Family::create($familyData);

echo "Created family ID: {$family->id}\n";
echo "Family data:\n";
echo '  No KK: ' . ($family->no_kk ?? 'NULL') . "\n";
echo '  Kepala Keluarga: ' . ($family->kepala_keluarga ?? 'NULL') . "\n";
echo '  Alamat: ' . ($family->alamat ?? 'NULL') . "\n";
echo "  Province ID: {$family->province_id}\n";
echo "  Regency ID: {$family->regency_id}\n";
echo "  District ID: {$family->district_id}\n";
echo "  Village ID: {$family->village_id}\n\n";

echo "Done!\n";
