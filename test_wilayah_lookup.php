<?php
// Test wilayah lookup functionality
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== TESTING WILAYAH LOOKUP ===\n\n";

    // Test Regency lookup
    $regencyName = 'KOTA JAYAPURA';
    echo "Testing Regency lookup for: '$regencyName'\n";
    $regency = DB::table('regencies')->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($regencyName) . '%'])->first();
    if ($regency) {
        echo "  Found: ID=$regency->id, Name=$regency->name\n";
    } else {
        echo "  NOT FOUND\n";
    }
    $regencyId = $regency->id ?? null;
    echo "\n";

    // Test District lookup
    $districtName = 'JAYAPURA UTARA';
    echo "Testing District lookup for: '$districtName' (regency_id=$regencyId)\n";
    $query = DB::table('districts')->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($districtName) . '%']);
    if ($regencyId) {
        $query->where('regency_id', $regencyId);
    }
    $district = $query->first();
    if ($district) {
        echo "  Found: ID=$district->id, Name=$district->name\n";
    } else {
        echo "  NOT FOUND\n";
    }
    $districtId = $district->id ?? null;
    echo "\n";

    // Test Village lookup
    $villageName = 'IMBI';
    echo "Testing Village lookup for: '$villageName' (district_id=$districtId)\n";
    $query = DB::table('villages')->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($villageName) . '%']);
    if ($districtId) {
        $query->where('district_id', $districtId);
    }
    $village = $query->first();
    if ($village) {
        echo "  Found: ID=$village->id, Name=$village->name\n";
    } else {
        echo "  NOT FOUND\n";
    }
    echo "\n";

    // Summary
    echo "=== SUMMARY ===\n";
    echo "Province ID: 94 (from answers)\n";
    echo 'Regency ID: ' . ($regencyId ?? 'NULL') . " (looked up from '$regencyName')\n";
    echo 'District ID: ' . ($districtId ?? 'NULL') . " (looked up from '$districtName')\n";
    echo 'Village ID: ' . ($village->id ?? 'NULL') . " (looked up from '$villageName')\n";
    echo "\n";

    // Check current residents data
    echo "=== CURRENT RESIDENTS DATA ===\n";
    $residents = DB::table('residents')
        ->select('id', 'nama', 'province_id', 'regency_id', 'district_id', 'village_id')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get();

    foreach ($residents as $r) {
        echo "ID: $r->id, Nama: $r->nama\n";
        echo "  Province: $r->province_id, Regency: $r->regency_id, District: $r->district_id, Village: $r->village_id\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
