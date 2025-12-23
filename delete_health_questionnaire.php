<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Questionnaire;

// Delete existing questionnaire
$questionnaire = Questionnaire::where('title', 'Data Keluarga dan Anggota Keluarga Sehat')->first();

if ($questionnaire) {
    echo "🗑️  Deleting questionnaire ID: {$questionnaire->id}\n";
    echo "   Questions: {$questionnaire->questions()->count()}\n";

    // Delete questions and their options (cascade should handle this)
    $questionnaire->delete();

    echo "✅ Questionnaire deleted!\n";
} else {
    echo "⚠️  No questionnaire found.\n";
}
