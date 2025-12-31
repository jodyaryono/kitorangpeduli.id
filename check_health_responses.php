<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ResidentHealthResponse;

echo "\n=== CHECKING HEALTH RESPONSES ===\n\n";

$healthResponses = ResidentHealthResponse::where('response_id', 10)
    ->with('resident')
    ->get();

echo "Total health responses: " . $healthResponses->count() . "\n\n";

if ($healthResponses->count() > 0) {
    foreach ($healthResponses as $hr) {
        echo "Resident: " . ($hr->resident->nama_lengkap ?? 'N/A') . " (ID: {$hr->resident_id})\n";
        echo "  Question Code: {$hr->question_code}\n";
        echo "  Answer: {$hr->answer}\n\n";
    }
} else {
    echo "❌ NO HEALTH RESPONSES FOUND!\n";
    echo "Health data is not loading into the form.\n";
}
