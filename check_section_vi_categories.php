<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== SECTION VI CATEGORIES ===\n\n";

$categories = \App\Models\HealthQuestionCategory::where('section', 'VI')->get();

foreach ($categories as $category) {
    echo "Category: {$category->name}\n";
    echo "Code: {$category->code}\n";
    echo "Target Criteria: {$category->target_criteria}\n";

    $questions = \App\Models\HealthQuestion::where('category_id', $category->id)->get();
    echo 'Questions: ' . $questions->count() . "\n";

    echo "---\n";
}

echo "\nTotal Section VI categories: " . $categories->count() . "\n";
