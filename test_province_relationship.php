<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Province;

echo "Testing Province → Regency relationship...\n\n";

// Get Papua province
$papua = Province::where('code', '94')->first();

if (!$papua) {
    echo "ERROR: Papua province (code 94) not found!\n";
    exit(1);
}

echo "Province: {$papua->name} (ID: {$papua->id}, Code: {$papua->code})\n";
echo 'Province ID type: ' . gettype($papua->id) . "\n\n";

// Get regencies via relationship
echo "Regencies via relationship:\n";
$regenciesViaRelation = $papua->regencies()->orderBy('name')->get();
echo 'Count: ' . $regenciesViaRelation->count() . "\n";
foreach ($regenciesViaRelation->take(10) as $r) {
    echo "  - {$r->code}: {$r->name} (province_id: {$r->province_id})\n";
}

// Get regencies via where clause
echo "\nRegencies via WHERE clause:\n";
$regenciesViaWhere = \App\Models\Regency::where('province_id', $papua->id)->orderBy('name')->get();
echo 'Count: ' . $regenciesViaWhere->count() . "\n";
foreach ($regenciesViaWhere->take(10) as $r) {
    echo "  - {$r->code}: {$r->name} (province_id: {$r->province_id})\n";
}

// Check Jayapura specifically
echo "\nJayapura regencies:\n";
$jayapura = $papua->regencies()->where('name', 'like', '%JAYAPURA%')->get();
foreach ($jayapura as $r) {
    echo "  - {$r->code}: {$r->name} (province_id: {$r->province_id})\n";
}

echo "\nDone!\n";
