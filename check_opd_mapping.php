<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Opd;
use App\Models\Questionnaire;

echo "=== VERIFIKASI MAPPING OPD - QUESTIONNAIRE ===\n\n";

$opds = Opd::with(['questionnaires' => fn($q) => $q->orderBy('title')])
    ->orderBy('name')
    ->get();

foreach ($opds as $opd) {
    echo "📌 OPD: {$opd->name} (ID: {$opd->id})\n";

    if ($opd->questionnaires->count() === 0) {
        echo "   ⚠️  TIDAK ADA QUESTIONNAIRE!\n";
    } else {
        foreach ($opd->questionnaires as $q) {
            echo "   ✅ {$q->title}\n";
        }
    }
    echo "\n";
}

echo "\n=== SUMMARY ===\n";
echo 'Total OPD: ' . $opds->count() . "\n";
echo 'Total Questionnaire: ' . Questionnaire::count() . "\n";
echo "Expected: 2 questionnaires x 11 OPDs = 22 questionnaires\n";
