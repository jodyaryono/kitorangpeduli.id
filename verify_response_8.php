<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Response;

echo "\n=== CHECKING RESPONSE #8 ===\n\n";

$response = Response::find(8);

if ($response) {
    echo "✅ Response #8 EXISTS\n";
    echo "  - ID: {$response->id}\n";
    echo "  - Questionnaire ID: {$response->questionnaire_id}\n";
    echo '  - Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
    echo "  - Status: {$response->status}\n";
    echo "  - Entered by: {$response->entered_by_user_id}\n";
    echo "  - Created: {$response->created_at}\n";
} else {
    echo "❌ Response #8 NOT FOUND\n";
    echo "\nLet's check the last few responses:\n";

    $responses = Response::orderBy('id', 'desc')->take(5)->get();
    foreach ($responses as $r) {
        echo "  Response #{$r->id} - Questionnaire #{$r->questionnaire_id} - Status: {$r->status}\n";
    }
}
