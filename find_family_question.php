<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Question;

$familyQuestion = Question::where('questionnaire_id', 8)
    ->where('question_type', 'family_members')
    ->first();

if ($familyQuestion) {
    echo "✅ Found family_members question:\n";
    echo "  - ID: {$familyQuestion->id}\n";
    echo "  - Order: {$familyQuestion->order}\n";
    echo "  - Text: {$familyQuestion->question_text}\n";
    echo "  - Parent Section ID: {$familyQuestion->parent_section_id}\n";

    if ($familyQuestion->parentSection) {
        echo "  - Parent Section: {$familyQuestion->parentSection->question_text}\n";
    }
} else {
    echo "❌ family_members question NOT FOUND in questionnaire 8!\n";
}
