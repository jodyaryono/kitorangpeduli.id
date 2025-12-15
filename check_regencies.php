<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Province;
use App\Models\Regency;

try {
    // Check province Banten
    $province = Province::where('code', '36')->first();

    if (!$province) {
        echo "Province with code 36 not found!\n";

        // Show all provinces
        echo "\nAll provinces:\n";
        Province::orderBy('id')->get()->each(function ($p) {
            echo "  ID: {$p->id}, Code: {$p->code}, Name: {$p->name}\n";
        });
        exit;
    }

    echo "Province: {$province->name} (ID: {$province->id}, Code: {$province->code})\n";

    // Check regencies
    $regenciesCount = $province->regencies()->count();
    echo "Regencies count: {$regenciesCount}\n";

    // Check total regencies in database
    $totalRegencies = Regency::count();
    echo "Total regencies in DB: {$totalRegencies}\n";

    // Check if there are regencies with province_id = 16
    $regenciesP16 = Regency::where('province_id', $province->id)->count();
    echo "Regencies with province_id={$province->id}: {$regenciesP16}\n";

    // Show first 5 regencies for this province
    echo "\nRegencies for {$province->name}:\n";
    $regencies = $province->regencies()->limit(5)->get();
    foreach ($regencies as $r) {
        echo "  - {$r->name} (ID: {$r->id}, Code: {$r->code})\n";
    }
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
