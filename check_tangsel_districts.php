<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\District;
use App\Models\Regency;

echo "=== Checking Kota Tangerang Selatan Districts ===\n\n";

// Find the regency
$regency = Regency::where('name', 'LIKE', '%TANGERANG SELATAN%')->first();

if (!$regency) {
    echo "❌ Regency 'Tangerang Selatan' not found!\n";

    // List all regencies in Banten
    echo "\nRegencies in Banten:\n";
    $regencies = Regency::whereHas('province', function ($q) {
        $q->where('name', 'LIKE', '%BANTEN%');
    })->get();

    foreach ($regencies as $reg) {
        echo "  - ID: {$reg->id} | Name: {$reg->name}\n";
    }
    exit(1);
}

echo "✓ Found regency: {$regency->name}\n";
echo "  - ID: {$regency->id}\n";
echo "  - Code: {$regency->code}\n";
echo "  - Province ID: {$regency->province_id}\n\n";

// Check districts
$districtsCount = $regency->districts()->count();
echo "Districts count: {$districtsCount}\n\n";

if ($districtsCount > 0) {
    echo "Districts:\n";
    $districts = $regency->districts()->orderBy('name')->get();
    foreach ($districts as $district) {
        echo "  - ID: {$district->id} | Code: {$district->code} | Name: {$district->name}\n";
    }
} else {
    echo "❌ NO DISTRICTS FOUND for {$regency->name}!\n\n";

    // Check if there are districts with wrong regency_id
    echo "Checking all districts with 'TANGERANG' in name:\n";
    $allDistricts = District::where('name', 'LIKE', '%TANGERANG%')->get();
    foreach ($allDistricts as $district) {
        echo "  - ID: {$district->id} | Regency ID: {$district->regency_id} | Name: {$district->name}\n";
    }
}
