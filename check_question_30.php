<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Question;
use App\Models\Questionnaire;

$questionnaire = Questionnaire::find(8);

echo "Questionnaire: {$questionnaire->title}\n\n";

// Get all questions in order
$questions = Question::where('questionnaire_id', 8)
    ->whereNull('parent_section_id')
    ->orderBy('order')
    ->with(['childQuestions' => function ($q) {
        $q->orderBy('order');
    }])
    ->get();

foreach ($questions as $section) {
    echo "SECTION {$section->order}: {$section->question_text}\n";

    foreach ($section->childQuestions as $child) {
        echo "  - Question {$child->order}: {$child->question_text}\n";
        echo "    Type: {$child->question_type}\n";

        if ($child->question_type === 'family_members') {
            echo "    ⭐ THIS IS THE FAMILY MEMBERS QUESTION!\n";
        }
    }
    echo "\n";
}
