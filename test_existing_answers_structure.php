<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Answer;

echo "\n=== CHECKING existingAnswers STRUCTURE ===\n\n";

$existingAnswers = Answer::where('response_id', 10)
    ->get()
    ->keyBy('question_id');

echo "Collection keys (question_ids): " . implode(', ', $existingAnswers->keys()->toArray()) . "\n\n";

foreach ($existingAnswers as $questionId => $answer) {
    echo "Key: {$questionId}\n";
    echo "  answer_text: " . ($answer->answer_text ?? 'NULL') . "\n";
    echo "  selected_options: " . ($answer->selected_options ?? 'NULL') . "\n";

    $savedValue = $answer->selected_options ?? $answer->answer_text ?? $answer->answer_numeric;
    echo "  Resulting savedValue: {$savedValue}\n\n";
}

echo "\n=== TEST GET METHOD ===\n";
$test = $existingAnswers->get(277);
if ($test) {
    echo "✅ Get question 277 works!\n";
    echo "  answer_text: {$test->answer_text}\n";
} else {
    echo "❌ Get question 277 returns null\n";
}
