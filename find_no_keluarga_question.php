<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Looking for 'No. Bangunan' and 'No. Keluarga' questions\n";
echo str_repeat('=', 80) . "\n\n";

$questions = App\Models\Question::where('questionnaire_id', 8)
    ->where(function ($q) {
        $q
            ->where('question_text', 'LIKE', '%Bangunan%')
            ->orWhere('question_text', 'LIKE', '%No. Keluarga%')
            ->orWhere('question_text', 'LIKE', '%Kartu Keluarga%')
            ->orWhere('question_text', 'LIKE', '%No KK%')
            ->orWhere('question_text', 'LIKE', '%Nomor KK%');
    })
    ->orderBy('order')
    ->get(['id', 'order', 'question_text', 'question_type']);

foreach ($questions as $q) {
    echo "Question ID: {$q->id}\n";
    echo "Order: {$q->order}\n";
    echo "Type: {$q->question_type}\n";
    echo "Text: {$q->question_text}\n";
    echo str_repeat('-', 80) . "\n\n";
}

// Check Response 2 answers for these questions
echo "\nChecking answers in Response 2:\n";
echo str_repeat('-', 80) . "\n";

$questionIds = $questions->pluck('id')->toArray();
$answers = App\Models\Answer::where('response_id', 2)
    ->whereIn('question_id', $questionIds)
    ->get();

foreach ($answers as $answer) {
    $question = $questions->firstWhere('id', $answer->question_id);
    echo "Q{$answer->question_id} ({$question->question_text}): {$answer->answer_text}\n";
}

echo "\nDone!\n";
