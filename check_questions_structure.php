<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Question;

echo "\n=== CHECKING QUESTIONS IN QUESTIONNAIRE 8 ===\n\n";

$questions = Question::where('questionnaire_id', 8)
    ->orderBy('order')
    ->get();

echo 'Total questions: ' . $questions->count() . "\n\n";

// Group by order
$filtered = $questions->where('order', '>=', 20)->where('order', '<=', 28);

echo "Questions 20-28 (by order):\n";
foreach ($filtered as $q) {
    $type = $q->question_type;
    $section = $q->is_section ? 'SECTION' : 'question';
    echo "  Order {$q->order} - ID {$q->id} ({$section}, type: {$type}): " . substr($q->question_text, 0, 70) . "...\n";
}
