<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Cek pertanyaan Q2 dan Q6 dari questionnaire ID 9...\n\n";

$questions = DB::table('questions')
    ->where('questionnaire_id', 9)
    ->orderBy('order')
    ->get();

foreach ($questions as $index => $q) {
    $qNum = $index + 1;
    echo "Q{$qNum} (ID: {$q->id}): {$q->question_text}\n";
    echo "  Type: {$q->question_type}\n";
    echo '  Required: ' . ($q->is_required ? 'Yes' : 'No') . "\n";

    // Check options
    $options = DB::table('question_options')
        ->where('question_id', $q->id)
        ->get();

    echo "  Options: {$options->count()}\n";

    if ($options->count() > 0) {
        foreach ($options as $opt) {
            echo "    - {$opt->option_text} (value: {$opt->option_value})\n";
        }
    }

    echo "\n";
}
