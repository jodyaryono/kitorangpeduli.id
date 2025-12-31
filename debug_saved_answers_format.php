<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Answer;
use App\Models\Question;

echo "\n=== CHECKING SAVED ANSWERS FORMAT ===\n\n";

$answers = Answer::where('response_id', 10)
    ->with('question')
    ->get();

echo 'Total: ' . $answers->count() . " answers\n\n";

foreach ($answers as $answer) {
    $question = $answer->question;
    if (!$question)
        continue;

    echo "Question ID {$answer->question_id} ({$question->question_type}):\n";
    echo '  Text: ' . substr($question->question_text, 0, 60) . "...\n";
    echo '  answer_text: ' . ($answer->answer_text ?? 'NULL') . "\n";
    echo '  answer_numeric: ' . ($answer->answer_numeric ?? 'NULL') . "\n";
    echo '  selected_options: ' . ($answer->selected_options ?? 'NULL') . "\n";

    // Show what should be used
    $savedValue = $answer->selected_options ?? $answer->answer_text ?? $answer->answer_numeric;
    echo '  -> Will be used as savedValue: ' . ($savedValue ?? 'NULL') . "\n";

    // Check if question has options
    if ($question->question_type === 'single_choice') {
        $options = $question->options;
        echo "  Available options:\n";
        foreach ($options as $opt) {
            $optionValue = $opt->option_value ?? $opt->value ?? $opt->option_text;
            $match = ($savedValue == $optionValue) ? '✅ MATCH' : '';
            echo "    - {$optionValue} {$match}\n";
        }
    }

    echo "\n";
}
