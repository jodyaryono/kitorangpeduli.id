<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;
use App\Models\Response;

// Get the response
$response = Response::find(2);
$resident = Resident::find(20);

if (!$response) {
    echo "❌ Response not found\n";
    exit;
}

if (!$resident) {
    echo "❌ Resident not found\n";
    exit;
}

echo "🔗 Linking Response ID {$response->id} with Resident ID {$resident->id} ({$resident->nama_lengkap})\n\n";

$response->resident_id = $resident->id;
$response->save();

echo "✅ Successfully linked!\n\n";

// Verify
$response->refresh();
echo "Verification:\n";
echo "  - Response ID: {$response->id}\n";
echo "  - Resident ID: {$response->resident_id}\n";
echo "  - Resident Name: {$response->resident->nama_lengkap}\n";
echo "  - Family ID: {$response->resident->family_id}\n";

$familyMembers = Resident::where('family_id', $response->resident->family_id)->get();
echo '  - Family Members: ' . $familyMembers->count() . "\n";
