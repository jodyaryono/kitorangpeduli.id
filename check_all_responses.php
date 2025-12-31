<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;
use App\Models\Response;

// Get all responses
$responses = Response::orderBy('id', 'desc')->get();

echo '📋 Total responses: ' . $responses->count() . "\n\n";

foreach ($responses as $response) {
    echo "Response ID: {$response->id}\n";
    echo '  - Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
    echo "  - Questionnaire ID: {$response->questionnaire_id}\n";

    if ($response->resident_id) {
        $resident = $response->resident;
        if ($resident) {
            echo "  - Resident Name: {$resident->nama_lengkap}\n";
            echo "  - Family ID: {$resident->family_id}\n";

            $familyCount = Resident::where('family_id', $resident->family_id)->count();
            echo "  - Family Members: {$familyCount}\n";
        }
    }
    echo "\n";
}

// Check all residents
echo "\n👥 All Residents in database:\n\n";
$allResidents = Resident::all();
echo 'Total: ' . $allResidents->count() . "\n\n";

foreach ($allResidents as $resident) {
    echo "Resident ID: {$resident->id}\n";
    echo "  - NIK: {$resident->nik}\n";
    echo "  - Nama: {$resident->nama_lengkap}\n";
    echo "  - Family ID: {$resident->family_id}\n";
    echo "  - Phone: {$resident->phone}\n\n";
}
