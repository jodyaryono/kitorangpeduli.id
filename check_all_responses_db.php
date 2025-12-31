<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Response;
use Illuminate\Support\Facades\DB;

echo "🔍 Checking All Responses (including deleted)\n";
echo str_repeat('=', 60) . "\n\n";

// Direct DB query to see ALL responses
$allResponses = DB::table('responses')->get();

echo 'Total responses in database: ' . $allResponses->count() . "\n\n";

foreach ($allResponses as $r) {
    echo "Response #{$r->id}:\n";
    echo "   Questionnaire: {$r->questionnaire_id}\n";
    echo "   Status: {$r->status}\n";
    echo '   User: ' . ($r->entered_by_user_id ?? 'NULL') . "\n";
    echo '   Resident: ' . ($r->resident_id ?? 'NULL') . "\n";
    echo "   Started: {$r->started_at}\n";
    echo '   Deleted: ' . ($r->deleted_at ?? 'NO') . "\n";
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
