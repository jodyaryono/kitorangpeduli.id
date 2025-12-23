<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$questions = App\Models\Question::where('questionnaire_id', 8)
    ->where('question_type', 'family_members')
    ->get(['id', 'question_text', 'question_type', 'parent_section_id']);

echo "Family Members Questions in Questionnaire 8:\n";
echo json_encode($questions, JSON_PRETTY_PRINT);
echo "\n\nTotal: " . $questions->count();
