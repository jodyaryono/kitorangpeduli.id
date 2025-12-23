<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Questionnaire;

$questionnaires = Questionnaire::all();

echo 'Total questionnaires: ' . $questionnaires->count() . "\n";

foreach ($questionnaires as $q) {
    echo "ID: {$q->id} - {$q->title} - Questions: {$q->questions()->count()}\n";
}

// Delete duplicates, keeping only the first one
$duplicates = Questionnaire::where('title', 'Data Keluarga dan Anggota Keluarga Sehat')
    ->orderBy('id', 'desc')
    ->skip(1)
    ->get();

if ($duplicates->count() > 0) {
    echo "\n🗑️  Deleting duplicates...\n";
    foreach ($duplicates as $dup) {
        echo "Deleting ID: {$dup->id}\n";
        $dup->delete();
    }
    echo "✅ Duplicates deleted!\n";
}
