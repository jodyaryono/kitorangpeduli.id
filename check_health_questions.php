<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\HealthQuestion;
use App\Models\HealthQuestionCategory;
use App\Models\HealthQuestionOption;
use App\Models\HealthQuestionTableRow;

echo "================================================\n";
echo "   KATEGORI KUESIONER KESEHATAN VI\n";
echo "================================================\n\n";

$categories = HealthQuestionCategory::withCount('questions')->get();

foreach ($categories as $cat) {
    echo sprintf("%-30s: %d pertanyaan\n", $cat->name, $cat->questions_count);
}

echo "\n================================================\n";
echo "   RINGKASAN DATA\n";
echo "================================================\n";
echo 'Total Kategori    : ' . HealthQuestionCategory::count() . "\n";
echo 'Total Pertanyaan  : ' . HealthQuestion::count() . "\n";
echo 'Total Opsi        : ' . HealthQuestionOption::count() . "\n";
echo 'Total Table Rows  : ' . HealthQuestionTableRow::count() . "\n";

echo "\n================================================\n";
echo "   DETAIL PER KATEGORI\n";
echo "================================================\n\n";

foreach ($categories as $cat) {
    echo "📁 {$cat->name}\n";
    echo '   Target: ' . json_encode($cat->target_criteria) . "\n";

    $questions = HealthQuestion::where('category_id', $cat->id)
        ->orderBy('order')
        ->take(5)
        ->get(['code', 'question_text', 'input_type']);

    foreach ($questions as $q) {
        $text = mb_strlen($q->question_text) > 50
            ? mb_substr($q->question_text, 0, 50) . '...'
            : $q->question_text;
        echo "   - [{$q->code}] ({$q->input_type}) {$text}\n";
    }

    $remaining = HealthQuestion::where('category_id', $cat->id)->count() - 5;
    if ($remaining > 0) {
        echo "   ... dan {$remaining} pertanyaan lainnya\n";
    }
    echo "\n";
}
