<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\District;
use App\Models\Regency;

echo "Searching for Jayapura districts...\n\n";

// Search for Jayapura regencies
$jayapuraRegencies = Regency::where('name', 'like', '%JAYAPURA%')->get();
echo "Jayapura Regencies:\n";
foreach ($jayapuraRegencies as $r) {
    echo "  {$r->code}: {$r->name}\n";
}

echo "\nDistricts in Jayapura:\n";
foreach ($jayapuraRegencies as $r) {
    $districts = District::where('regency_id', $r->id)->get();
    echo "\n  Regency: {$r->name}\n";
    foreach ($districts as $d) {
        echo "    - {$d->code}: {$d->name}\n";
    }
}

// Check seeder data
echo "\n\nChecking WilayahSeeder.php for Jayapura Utara district code...\n";
$seederContent = file_get_contents(__DIR__ . '/database/seeders/WilayahSeeder.php');
if (preg_match("/\['code' => '(\d+)', 'name' => 'JAYAPURA UTARA'/", $seederContent, $matches)) {
    echo "Found in seeder: District code = {$matches[1]}\n";
} else {
    echo "Not found in seeder file.\n";
}
