<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Question;

$questionnaire = \App\Models\Questionnaire::latest()->first();

if (!$questionnaire) {
    echo "⚠️  No questionnaire found\n";
    exit;
}

echo "📋 Questionnaire: {$questionnaire->title}\n";
echo '=' . str_repeat('=', 70) . "\n\n";

$sections = Question::where('questionnaire_id', $questionnaire->id)
    ->where('is_section', true)
    ->orderBy('order')
    ->get();

echo "📁 Total Sections: {$sections->count()}\n\n";

foreach ($sections as $section) {
    $childCount = Question::where('parent_section_id', $section->id)->count();
    echo "Section {$section->order}: {$section->question_text}\n";
    echo "   └─ Child questions: {$childCount}\n";
}

echo "\n";

$orphanQuestions = Question::where('questionnaire_id', $questionnaire->id)
    ->where('is_section', false)
    ->whereNull('parent_section_id')
    ->get();

if ($orphanQuestions->count() > 0) {
    echo "⚠️  Questions WITHOUT parent_section (orphans): {$orphanQuestions->count()}\n";
    foreach ($orphanQuestions as $q) {
        echo "   - Order {$q->order}: {$q->question_text}\n";
    }
} else {
    echo "✅ All questions are properly grouped!\n";
}
