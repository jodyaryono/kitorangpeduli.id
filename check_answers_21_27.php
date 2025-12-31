<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Answer;
use App\Models\Question;
use App\Models\Response;

echo "\n=== CHECKING ANSWERS FOR RESPONSE #10 ===\n\n";

$response = Response::find(10);

if (!$response) {
    echo "❌ Response #10 not found\n";
    exit;
}

echo "Response #10:\n";
echo "  - Questionnaire ID: {$response->questionnaire_id}\n";
echo "  - Status: {$response->status}\n";
echo '  - Officer Notes: ' . ($response->officer_notes ?? 'NULL') . "\n";
echo '  - Notes: ' . ($response->notes ?? 'NULL') . "\n\n";

// Get all answers
$answers = Answer::where('response_id', 10)
    ->with('question')
    ->orderBy('question_id')
    ->get();

echo 'Total answers: ' . $answers->count() . "\n\n";

if ($answers->count() > 0) {
    echo "Answers:\n";
    foreach ($answers as $answer) {
        $questionId = $answer->question_id;
        $questionText = $answer->question ? substr($answer->question->question_text, 0, 60) : 'Question not found';
        $value = $answer->answer_text ?? $answer->answer_numeric ?? $answer->selected_options ?? 'NULL';

        echo "  Q{$questionId}: {$questionText}... = {$value}\n";
    }
} else {
    echo "No answers found\n";
}

// Check questions 21-27 specifically
echo "\n=== CHECKING QUESTIONS 21-27 ===\n\n";
$questions = Question::whereBetween('id', [21, 27])
    ->orWhere(function ($q) {
        $q
            ->where('questionnaire_id', 8)
            ->where('order', '>=', 21)
            ->where('order', '<=', 27);
    })
    ->get();

if ($questions->count() > 0) {
    foreach ($questions as $q) {
        $hasAnswer = $answers->where('question_id', $q->id)->first();
        $status = $hasAnswer ? '✅ HAS ANSWER' : '❌ NO ANSWER';
        echo "  Q{$q->id} (order {$q->order}): {$status} - " . substr($q->question_text, 0, 50) . "...\n";
    }
} else {
    echo "Questions 21-27 not found\n";
}
