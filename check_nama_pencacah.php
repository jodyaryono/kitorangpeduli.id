<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Question;

$question = Question::where('order', 21)->first();

if ($question) {
    echo "Question 21 (Nama Pencacah):\n";
    echo "Type: {$question->question_type}\n";
    echo "Text: {$question->question_text}\n";
    echo 'Settings: ' . json_encode($question->settings) . "\n";
    echo "\n✅ Tipe pertanyaan sudah field_officer!\n";
} else {
    echo "⚠️  Question tidak ditemukan\n";
}
