<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;
use App\Models\ResidentHealthResponse;
use App\Models\Response;

$response = Response::find(2);

echo "Response ID: {$response->id}\n";
echo 'Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
echo "Entered by: {$response->entered_by_user_id}\n\n";

// Find all residents
$allResidents = Resident::all();
echo "All Residents:\n";
foreach ($allResidents as $r) {
    echo "  - ID: {$r->id}, NIK: {$r->nik}, Name: {$r->nama_lengkap}, Family ID: {$r->family_id}\n";
}

// Find health responses for this response
echo "\nHealth Responses for Response ID 2:\n";
$healthResponses = ResidentHealthResponse::where('response_id', 2)->with('resident')->get();
foreach ($healthResponses as $hr) {
    echo "  - Resident: {$hr->resident->nama_lengkap} (ID: {$hr->resident_id}, Family ID: {$hr->resident->family_id})\n";
    echo "    Question: {$hr->question_code}, Answer: {$hr->answer}\n";
}
