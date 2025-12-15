<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Question;

$questionId = 68;
$question = Question::with('options')->find($questionId);

if ($question) {
    echo "Question ID: {$question->id}\n";
    echo "Text: {$question->question_text}\n";
    echo "Type: {$question->type}\n";
    echo "Options Count: {$question->options->count()}\n\n";

    if ($question->options->count() > 0) {
        echo "Options:\n";
        foreach ($question->options as $option) {
            echo "  - {$option->option_text} (value: {$option->option_value})\n";
        }
    } else {
        echo "❌ NO OPTIONS FOUND!\n";
        echo "\nChecking question_options table...\n";
        $allOptions = DB::table('question_options')->where('question_id', $questionId)->get();
        echo "Raw count: {$allOptions->count()}\n";
        foreach ($allOptions as $opt) {
            print_r($opt);
        }
    }
} else {
    echo "Question not found!\n";
}
