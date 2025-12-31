<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HealthQuestion;
use App\Models\HealthQuestionTableRow;

echo "🔍 Checking H4 AKS data...\n\n";

$q4 = HealthQuestion::where('code', 'H4')->first();

if ($q4) {
    echo "Question H4: {$q4->question_text}\n";

    $rows = HealthQuestionTableRow::where('question_id', $q4->id)->orderBy('order')->get();
    echo 'Table rows count: ' . $rows->count() . "\n\n";

    foreach ($rows as $row) {
        echo "Row {$row->order}: {$row->row_code}\n";
        echo "  Label: {$row->row_label}\n";
        echo '  Note: ' . ($row->note ?: '[EMPTY]') . "\n\n";
    }
} else {
    echo "❌ H4 question not found!\n";
}
