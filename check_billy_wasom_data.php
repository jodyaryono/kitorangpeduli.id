<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Family;
use App\Models\Resident;
use App\Models\ResidentHealthResponse;
use App\Models\Response;

echo "🔍 Checking Response #11 Full Details\n";
echo str_repeat('=', 60) . "\n\n";

$response = Response::find(11);

if (!$response) {
    echo "❌ Response #11 not found!\n";
    exit;
}

echo "📋 Response Details:\n";
echo "   ID: {$response->id}\n";
echo "   Status: {$response->status}\n";
echo "   Questionnaire ID: {$response->questionnaire_id}\n";
echo '   Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n\n";

// Check if there's a family linked through health responses
echo "🔍 Searching for linked family...\n\n";

$healthResponses = ResidentHealthResponse::where('response_id', 11)->get();
echo '   Health responses count: ' . $healthResponses->count() . "\n";

if ($healthResponses->isNotEmpty()) {
    $firstHealthResponse = $healthResponses->first();
    $resident = Resident::find($firstHealthResponse->resident_id);

    if ($resident) {
        echo "\n👤 Found Resident from health response:\n";
        echo "   ID: {$resident->id}\n";
        echo "   Name: {$resident->nama_lengkap}\n";
        echo "   NIK: {$resident->nik}\n";
        echo '   Family ID: ' . ($resident->family_id ?? 'NULL') . "\n";

        if ($resident->family_id) {
            $family = Family::find($resident->family_id);
            if ($family) {
                echo "\n👨‍👩‍👧‍👦 Family Details:\n";
                echo "   ID: {$family->id}\n";
                echo "   No KK: {$family->no_kk}\n";
                echo "   Alamat: {$family->alamat}\n";

                $residents = Resident::where('family_id', $family->id)->get();
                echo "\n   Family Members:\n";
                foreach ($residents as $r) {
                    echo "   - {$r->nama_lengkap} (NIK: {$r->nik})\n";
                }
            }
        }
    }
}

// Check for existing families with BILLY WASOM
echo "\n🔍 Searching for BILLY WASOM in database...\n";
$billy = Resident::where('nama_lengkap', 'LIKE', '%BILLY%')->get();

if ($billy->isNotEmpty()) {
    echo "\n   Found " . $billy->count() . " resident(s) with name containing BILLY:\n";
    foreach ($billy as $b) {
        echo "   - ID: {$b->id}, Name: {$b->nama_lengkap}, NIK: {$b->nik}, Family ID: {$b->family_id}\n";
    }
} else {
    echo "   ❌ No resident found with BILLY in name\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
