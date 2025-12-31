<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;
use App\Models\Response;

// Get all responses
$responses = Response::orderBy('id', 'desc')->get();

echo "📋 All Responses:\n\n";

foreach ($responses as $response) {
    echo "Response ID: {$response->id}\n";
    echo "  - Questionnaire ID: {$response->questionnaire_id}\n";
    echo '  - Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
    echo "  - Created: {$response->created_at}\n";

    if ($response->resident_id) {
        $resident = $response->resident;
        if ($resident) {
            echo "  - Resident Name: {$resident->nama_lengkap}\n";
            echo "  - NIK: {$resident->nik}\n";
        }
    }
    echo "\n";
}

// Ask which one to delete
echo "\n❓ Which Response ID do you want to DELETE? ";
