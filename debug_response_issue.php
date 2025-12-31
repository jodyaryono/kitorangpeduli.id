<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING RESPONSE ISSUE ===\n\n";

// Check all responses including soft deleted
$responses = \App\Models\Response::withTrashed()->orderBy('id')->get();

echo 'Total responses (including deleted): ' . $responses->count() . "\n\n";

foreach ($responses as $r) {
    echo "Response ID: {$r->id}\n";
    echo '  Resident ID: ' . ($r->resident_id ?? 'NULL') . "\n";
    echo "  Questionnaire ID: {$r->questionnaire_id}\n";
    echo "  Status: {$r->status}\n";
    echo "  Entered by: {$r->entered_by_user_id}\n";
    echo '  Deleted: ' . ($r->deleted_at ? 'YES at ' . $r->deleted_at : 'NO') . "\n";
    echo "---\n";
}

// Check logs
echo "\n=== RECENT LOGS ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $recentLines = array_slice($lines, -50);
    echo implode('', $recentLines);
}
