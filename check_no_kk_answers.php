<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking no_kk and kepala_keluarga answers\n";
echo str_repeat('=', 60) . "\n\n";

$answers = App\Models\Answer::where('response_id', 2)
    ->whereIn('question_id', [223, 269])
    ->get();

if ($answers->count() > 0) {
    foreach ($answers as $answer) {
        $qMap = [223 => 'No KK', 269 => 'Kepala Keluarga'];
        echo "Question: {$qMap[$answer->question_id]} (ID: {$answer->question_id})\n";
        echo "Answer Text: '" . ($answer->answer_text ?? 'NULL') . "'\n";
        echo 'Answer Numeric: ' . ($answer->answer_numeric ?? 'NULL') . "\n";
        echo 'Media Path: ' . ($answer->media_path ?? 'NULL') . "\n";
        echo str_repeat('-', 60) . "\n";
    }
} else {
    echo "No answers found for questions 223 (no_kk) and 269 (kepala_keluarga)\n";
}

echo "\nDone!\n";
