<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking questions related to Kartu Keluarga (KK)\n";
echo str_repeat('=', 60) . "\n\n";

$questions = App\Models\Question::where('question_text', 'like', '%Kartu Keluarga%')
    ->orWhere('question_text', 'like', '%KK%')
    ->orWhere('question_text', 'like', '%kartu keluarga%')
    ->get(['id', 'question_text', 'question_type', 'questionnaire_id']);

if ($questions->count() > 0) {
    echo "Found {$questions->count()} question(s):\n\n";
    foreach ($questions as $question) {
        echo "Question ID: {$question->id}\n";
        echo "Questionnaire ID: {$question->questionnaire_id}\n";
        echo "Type: {$question->question_type}\n";
        echo "Text: {$question->question_text}\n";

        // Check if there are any answers for this question
        $answerCount = App\Models\Answer::where('question_id', $question->id)
            ->whereNotNull('media_path')
            ->count();
        echo "Uploaded files: {$answerCount}\n";

        echo str_repeat('-', 60) . "\n";
    }
} else {
    echo "No questions found related to Kartu Keluarga.\n";
}

echo "\nDone!\n";
