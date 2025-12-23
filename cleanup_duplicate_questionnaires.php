<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Questionnaire;

echo "🔍 Searching for duplicate questionnaires...\n";

$questionnaires = Questionnaire::where('title', 'Data Keluarga dan Anggota Keluarga Sehat')
    ->orderBy('id', 'asc')
    ->get();

echo 'Total questionnaires found: ' . $questionnaires->count() . "\n";

if ($questionnaires->count() > 1) {
    $keepId = $questionnaires->first()->id;
    echo "✅ Keeping questionnaire ID: {$keepId}\n";

    $toDelete = $questionnaires->skip(1);

    foreach ($toDelete as $questionnaire) {
        echo "🗑️  Deleting questionnaire ID {$questionnaire->id}...\n";
        $questionnaire->delete();
    }

    echo "✅ Duplicate questionnaires deleted!\n";

    $remaining = Questionnaire::where('title', 'Data Keluarga dan Anggota Keluarga Sehat')->count();
    echo "📊 Remaining questionnaires: {$remaining}\n";
} else {
    echo "✅ Only 1 questionnaire found, no duplicates to delete.\n";
}
