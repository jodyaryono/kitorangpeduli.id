<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\HealthQuestion;

echo "🔍 Checking H.6 SKILAS data...\n\n";

$q6 = HealthQuestion::where('code', 'H6')->first();

if ($q6) {
    echo "Question H6:\n";
    echo "Code: {$q6->code}\n";
    echo "Type: {$q6->input_type}\n";
    echo "Text: {$q6->question_text}\n\n";

    $rows = DB::table('health_question_table_rows')
        ->where('question_id', $q6->id)
        ->orderBy('order')
        ->get();

    echo 'Total rows: ' . $rows->count() . "\n\n";

    foreach ($rows as $row) {
        echo "Row {$row->order}: {$row->row_code}\n";
        echo "  Label: {$row->row_label}\n";
        echo "  Note: {$row->note}\n";
        echo "  Type: {$row->input_type}\n";
        echo "  Ref: {$row->reference_value}\n\n";
    }
} else {
    echo "❌ H6 not found!\n";
}
