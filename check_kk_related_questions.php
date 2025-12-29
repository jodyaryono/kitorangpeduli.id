<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking all questions in Questionnaire ID 8 (Family/KK related)\n";
echo str_repeat('=', 80) . "\n\n";

// Get all questions from questionnaire 8
$questions = App\Models\Question::where('questionnaire_id', 8)
    ->orderBy('order')
    ->get(['id', 'order', 'question_text', 'question_type', 'applies_to']);

echo "Total questions: {$questions->count()}\n\n";

// Find KK related questions
$kkRelated = ['Kartu Keluarga', 'KK', 'Kepala Keluarga', 'Alamat', 'RT', 'RW', 'Provinsi', 'Kabupaten', 'Kecamatan', 'Kelurahan', 'Desa'];

echo "KK-related questions:\n";
echo str_repeat('-', 80) . "\n";

foreach ($questions as $question) {
    foreach ($kkRelated as $keyword) {
        if (stripos($question->question_text, $keyword) !== false) {
            echo "Q{$question->order} [ID:{$question->id}] ({$question->question_type}) [{$question->applies_to}]: {$question->question_text}\n";
            break;
        }
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "Done!\n";
