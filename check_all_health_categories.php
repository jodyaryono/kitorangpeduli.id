<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ALL HEALTH QUESTION CATEGORIES ===\n\n";

$categories = \App\Models\HealthQuestionCategory::all();

foreach ($categories as $category) {
    echo "ID: {$category->id}\n";
    echo "Name: {$category->name}\n";
    echo "Code: {$category->code}\n";
    echo 'Target Criteria: ' . json_encode($category->target_criteria) . "\n";

    $questions = \App\Models\HealthQuestion::where('category_id', $category->id)->get();
    echo 'Questions: ' . $questions->count() . "\n";

    echo "---\n";
}

echo "\nTotal categories: " . $categories->count() . "\n";
