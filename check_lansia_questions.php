<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$category = \App\Models\HealthQuestionCategory::where('code', 'lansia')->first();

if (!$category) {
    die("Kategori lansia tidak ditemukan\n");
}

echo "=== KATEGORI LANSIA ===\n";
echo "ID: {$category->id}\n";
echo "Name: {$category->name}\n\n";

$questions = \App\Models\HealthQuestion::where('category_id', $category->id)
    ->orderBy('order')
    ->get();

foreach ($questions as $q) {
    echo "Question {$q->code}: {$q->question_text}\n";
    echo "Type: {$q->input_type}\n";

    if (in_array($q->input_type, ['table_radio', 'table_checkbox'])) {
        $rows = \App\Models\HealthQuestionTableRow::where('question_id', $q->id)
            ->orderBy('order')
            ->get();
        echo "Table Rows: {$rows->count()}\n";
        foreach ($rows as $row) {
            echo "  - {$row->row_code}: {$row->row_label}\n";
        }
    }

    $options = \App\Models\HealthQuestionOption::where('question_id', $q->id)
        ->orderBy('order')
        ->get();
    echo "Options: {$options->count()}\n";
    foreach ($options as $opt) {
        echo "  - {$opt->value}: {$opt->label}\n";
    }

    echo "---\n";
}
