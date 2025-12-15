<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Questionnaire;

echo "Checking for duplicate questionnaires...\n\n";

$questionnaires = Questionnaire::select('id', 'title', 'opd_id')
    ->withCount(['questions', 'responses' => fn($q) => $q->where('status', 'completed')])
    ->orderBy('title')
    ->get();

echo 'Total Questionnaires: ' . $questionnaires->count() . "\n\n";

$grouped = $questionnaires->groupBy('title');

foreach ($grouped as $title => $group) {
    if ($group->count() > 1) {
        echo "DUPLICATE FOUND: {$title}\n";
        echo 'Count: ' . $group->count() . "\n";
        foreach ($group as $q) {
            echo "  - ID: {$q->id}, OPD: {$q->opd_id}, Questions: {$q->questions_count}, Responses: {$q->responses_count}\n";
        }
        echo "\n";
    }
}

echo "\nQuestionnaires by ID:\n";
foreach ($questionnaires as $q) {
    echo "ID: {$q->id}, Title: {$q->title}, OPD: {$q->opd_id}\n";
}
