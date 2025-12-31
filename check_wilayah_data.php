<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== CHECKING WILAYAH DATA IN FAMILIES TABLE ===\n\n";

$family = DB::table('families')->where('id', 1)->first();

echo "FAMILY ID: 1\n";
echo 'province_id: ' . ($family->province_id ?? 'NULL') . "\n";
echo 'regency_id: ' . ($family->regency_id ?? 'NULL') . "\n";
echo 'district_id: ' . ($family->district_id ?? 'NULL') . "\n";
echo 'village_id: ' . ($family->village_id ?? 'NULL') . "\n";
echo 'puskesmas_id: ' . ($family->puskesmas_id ?? 'NULL') . "\n\n";

// Lookup actual names
if ($family->province_id) {
    $province = DB::table('provinces')->where('id', $family->province_id)->first();
    echo 'Province: ' . ($province->name ?? 'NOT FOUND') . "\n";
}

if ($family->regency_id) {
    $regency = DB::table('regencies')->where('id', $family->regency_id)->first();
    echo 'Regency: ' . ($regency->name ?? 'NOT FOUND') . "\n";
}

if ($family->district_id) {
    $district = DB::table('districts')->where('id', $family->district_id)->first();
    echo 'District: ' . ($district->name ?? 'NOT FOUND') . "\n";
}

if ($family->village_id) {
    $village = DB::table('villages')->where('id', $family->village_id)->first();
    echo 'Village: ' . ($village->name ?? 'NOT FOUND') . "\n";
}

echo "\n=== CHECKING RESPONSE AND RESIDENT LINK ===\n\n";

$response = DB::table('responses')->where('id', 7)->first();
echo "Response ID: 7\n";
echo 'resident_id: ' . ($response->resident_id ?? 'NULL') . "\n";
echo 'respondent_id: ' . ($response->respondent_id ?? 'NULL') . "\n\n";

if ($response->resident_id) {
    $resident = DB::table('residents')
        ->where('id', $response->resident_id)
        ->first();

    if ($resident) {
        echo "Resident found:\n";
        echo "  id: {$resident->id}\n";
        echo "  nama_lengkap: {$resident->nama_lengkap}\n";
        echo "  family_id: {$resident->family_id}\n\n";

        if ($resident->family_id == 1) {
            echo "✅ Resident is linked to Family ID 1\n";
            echo "✅ Controller should load savedFamily data\n\n";
        }
    }
}

echo "=== CHECKING CONTROLLER LOGIC ===\n\n";
echo "Controller should:\n";
echo "1. Find resident via response->resident_id (✅ exists: {$response->resident_id})\n";
echo "2. Load family via resident->family_id (✅ exists: family_id=1)\n";
echo "3. Pass \$savedFamily to view with all fields\n";
echo "4. View should auto-fill province_id, regency_id, district_id, village_id\n\n";

echo "If fields are empty on page, check:\n";
echo "- Is JavaScript console showing errors?\n";
echo "- Are Choices.js dropdowns initialized?\n";
echo "- Is savedFamily data actually passed to blade?\n";
