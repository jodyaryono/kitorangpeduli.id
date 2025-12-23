<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\District;
use App\Models\Village;

echo "Adding kelurahan Imbi to database...\n\n";

// Check if district Jayapura Utara exists
$district = District::where('code', '9471040')->first();

if (!$district) {
    echo "ERROR: District Jayapura Utara (9471040) not found!\n";
    echo "Available districts:\n";
    $districts = District::where('code', 'like', '9471%')->get();
    foreach ($districts as $d) {
        echo "  - {$d->code}: {$d->name}\n";
    }
    exit(1);
}

echo "District found: {$district->name} ({$district->code})\n\n";

// Check if Imbi already exists by name
$imbi = Village::where('district_id', $district->id)->where('name', 'IMBI')->first();

if ($imbi) {
    echo "Kelurahan Imbi already exists:\n";
    echo "  Code: {$imbi->code}\n";
    echo "  Name: {$imbi->name}\n";
    echo "  District: {$imbi->district->name}\n";
} else {
    echo "Creating kelurahan Imbi...\n";

    // Use code 9471040006 (seems to be missing from the sequence)
    Village::create([
        'district_id' => $district->id,
        'code' => '9471040006',
        'name' => 'IMBI',
    ]);

    echo "✓ Kelurahan Imbi created successfully!\n";
}

echo "\nAll villages in Jayapura Utara:\n";
$villages = Village::where('district_id', $district->id)->orderBy('code')->get();
foreach ($villages as $v) {
    echo "  - {$v->code}: {$v->name}\n";
}

echo "\nDone!\n";
