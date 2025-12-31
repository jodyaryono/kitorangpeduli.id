<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Response;
use App\Models\User;

echo "🔍 Checking Officer Responses\n";
echo str_repeat('=', 60) . "\n\n";

// Find all officers
$officers = User::whereHas('roles', function ($q) {
    $q->where('name', 'officer');
})->get();

echo 'Found ' . $officers->count() . " officer(s)\n\n";

foreach ($officers as $officer) {
    echo "Officer: {$officer->name} (ID: {$officer->id})\n";

    $responses = Response::where('entered_by_user_id', $officer->id)
        ->where('status', 'in_progress')
        ->where('questionnaire_id', 8)
        ->get();

    echo '   In-progress responses for Q8: ' . $responses->count() . "\n";

    foreach ($responses as $response) {
        echo "   - Response #{$response->id}\n";
        echo '     Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
        echo "     Started: {$response->started_at}\n";
        echo "     Updated: {$response->updated_at}\n";
    }
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
