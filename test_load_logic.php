<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;
use App\Models\ResidentHealthResponse;
use App\Models\Response;

$response = Response::find(7);

echo "Response ID: {$response->id}\n";
echo "Resident ID: {$response->resident_id}\n";
echo "Entered by: {$response->entered_by_user_id}\n\n";

// Simulate controller logic
$familyId = null;

// Try to get family_id from response->resident
if ($response->resident && $response->resident->family_id) {
    $familyId = $response->resident->family_id;
    echo "✅ Found family_id from response->resident: {$familyId}\n";
} else {
    echo "⚠️ response->resident is NULL or has no family_id\n";

    // Try to find from health responses
    $healthResponse = ResidentHealthResponse::where('response_id', $response->id)
        ->with('resident')
        ->first();

    if ($healthResponse && $healthResponse->resident && $healthResponse->resident->family_id) {
        $familyId = $healthResponse->resident->family_id;
        echo "✅ Found family_id from health responses: {$familyId}\n";
    } else {
        echo "❌ No family_id found from health responses\n";
    }
}

if ($familyId) {
    $savedResidents = Resident::where('family_id', $familyId)->get();
    echo "\n📋 Residents with family_id {$familyId}:\n";
    foreach ($savedResidents as $r) {
        echo "  - ID: {$r->id}, NIK: {$r->nik}, Name: {$r->nama_lengkap}\n";
    }

    echo "\n✅ Total residents to load: " . $savedResidents->count() . "\n";
}
