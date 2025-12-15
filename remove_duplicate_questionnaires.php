<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Questionnaire;

echo "Removing duplicate questionnaires...\n\n";

$questionnaires = Questionnaire::select('id', 'title', 'opd_id')
    ->orderBy('id')
    ->get();

$seen = [];
$toDelete = [];

foreach ($questionnaires as $q) {
    $key = $q->title . '|' . $q->opd_id;

    if (isset($seen[$key])) {
        // Sudah ada, ini duplicate - delete yang ID lebih besar
        $toDelete[] = $q->id;
        echo "Marking for deletion: ID {$q->id}, Title: {$q->title}\n";
    } else {
        $seen[$key] = $q->id;
        echo "Keeping: ID {$q->id}, Title: {$q->title}\n";
    }
}

echo "\n\nTotal to delete: " . count($toDelete) . "\n";

if (count($toDelete) > 0) {
    echo "Deleting duplicate questionnaires...\n";
    Questionnaire::whereIn('id', $toDelete)->delete();
    echo 'Done! Deleted ' . count($toDelete) . " duplicate questionnaires.\n";
}

echo "\n\nRemaining questionnaires: " . Questionnaire::count() . "\n";
