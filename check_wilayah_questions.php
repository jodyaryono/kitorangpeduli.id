<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking wilayah question types\n";
echo str_repeat('=', 80) . "\n\n";

$questions = App\Models\Question::whereIn('id', [214, 215, 216, 217])->get();

foreach ($questions as $q) {
    echo "Question ID: {$q->id}\n";
    echo "Type: {$q->question_type}\n";
    echo "Text: {$q->question_text}\n";
    echo str_repeat('-', 40) . "\n";
}

// Check what answers look like
echo "\nChecking answer values:\n";
echo str_repeat('-', 80) . "\n";

$answers = App\Models\Answer::where('response_id', 2)
    ->whereIn('question_id', [214, 215, 216, 217])
    ->get();

foreach ($answers as $answer) {
    echo "Question ID: {$answer->question_id}\n";
    echo "Answer Text: {$answer->answer_text}\n";
    echo 'Answer Numeric: ' . ($answer->answer_numeric ?? 'NULL') . "\n";
    echo str_repeat('-', 40) . "\n";
}

echo "\nDone!\n";
