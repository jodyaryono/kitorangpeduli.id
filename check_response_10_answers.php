<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\Response;

echo "🔍 Checking Response #10 Answers\n";
echo str_repeat('=', 60) . "\n\n";

$response = Response::find(10);

if (!$response) {
    echo "❌ Response #10 not found!\n";
    exit;
}

echo "📋 Response Details:\n";
echo "   ID: {$response->id}\n";
echo "   Status: {$response->status}\n";
echo '   Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";

if ($response->resident) {
    echo "   Resident Name: {$response->resident->nama_lengkap}\n";
    echo "   Resident NIK: {$response->resident->nik}\n";
    echo "   Family ID: {$response->resident->family_id}\n";
}

echo "\n📝 Saved Answers:\n";
$answers = Answer::where('response_id', 10)->get();

echo '   Total answers: ' . $answers->count() . "\n\n";

foreach ($answers as $answer) {
    $value = $answer->selected_options ?? $answer->answer_text ?? $answer->answer_numeric;
    echo "   Q{$answer->question_id}: {$value}\n";
}

if ($answers->isEmpty()) {
    echo "   ❌ NO ANSWERS FOUND!\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
