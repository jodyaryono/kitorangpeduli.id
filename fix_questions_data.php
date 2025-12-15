<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Fixing question types and option values...\n\n";

// Fix question types that are empty
$emptyTypes = DB::table('questions')
    ->whereNull('type')
    ->orWhere('type', '')
    ->get();

echo "Found {$emptyTypes->count()} questions with empty type\n";

foreach ($emptyTypes as $q) {
    // Check if it has options
    $hasOptions = DB::table('question_options')->where('question_id', $q->id)->exists();

    $newType = $hasOptions ? 'single_choice' : 'text';

    DB::table('questions')
        ->where('id', $q->id)
        ->update(['type' => $newType]);

    echo "  - Question {$q->id}: Set type to '{$newType}'\n";
}

// Fix option values that are empty
echo "\nFixing option values...\n";
$emptyValues = DB::table('question_options')
    ->whereNull('option_value')
    ->orWhere('option_value', '')
    ->get();

echo "Found {$emptyValues->count()} options with empty value\n";

foreach ($emptyValues as $opt) {
    DB::table('question_options')
        ->where('id', $opt->id)
        ->update(['option_value' => $opt->option_text]);

    echo "  - Option {$opt->id}: Set value to '{$opt->option_text}'\n";
}

echo "\n✅ Done!\n";
