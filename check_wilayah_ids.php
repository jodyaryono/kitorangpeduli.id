<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find wilayah questions
echo "Wilayah Questions:\n";
$questions = DB::table('questions')
    ->where('questionnaire_id', 8)
    ->where(function ($q) {
        $q
            ->where('question_text', 'like', '%rovinsi%')
            ->orWhere('question_text', 'like', '%abupaten%')
            ->orWhere('question_text', 'like', '%ecamatan%')
            ->orWhere('question_text', 'like', '%elurahan%');
    })
    ->get(['id', 'question_text', 'question_type']);

foreach ($questions as $q) {
    echo "  ID {$q->id}: {$q->question_text} ({$q->question_type})\n";
}

// Check answers for a response
echo "\n\nRecent answers for wilayah:\n";
$response = App\Models\Response::latest()->first();
if ($response) {
    echo "Response ID: {$response->id}\n";
    $answers = DB::table('answers')
        ->where('response_id', $response->id)
        ->whereIn('question_id', [214, 215, 216, 217])
        ->get(['question_id', 'answer_text']);

    foreach ($answers as $a) {
        echo "  Q{$a->question_id}: '{$a->answer_text}'\n";
    }
}
