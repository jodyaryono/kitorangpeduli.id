<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Question;
use App\Models\Questionnaire;

echo "🔍 Finding questions related to responden/NIK in Questionnaire #8\n";
echo str_repeat('=', 60) . "\n\n";

$questionnaire = Questionnaire::find(8);

if (!$questionnaire) {
    echo "❌ Questionnaire #8 not found!\n";
    exit;
}

echo "📋 Questionnaire: {$questionnaire->title}\n\n";

// Find all questions with "responden", "nik", or "nama" in text
$allQuestions = Question::where('questionnaire_id', 8)
    ->where('is_section', false)
    ->where(function ($q) {
        $q
            ->where('question_text', 'LIKE', '%responden%')
            ->orWhere('question_text', 'LIKE', '%NIK%')
            ->orWhere('question_text', 'LIKE', '%Nama%');
    })
    ->get();

echo 'Found ' . $allQuestions->count() . " question(s):\n\n";

foreach ($allQuestions as $q) {
    echo "Q{$q->id}: {$q->question_text}\n";
    echo "   Type: {$q->question_type}\n";
    echo '   Settings: ' . json_encode($q->settings) . "\n";
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
