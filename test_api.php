<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Province;

$province = Province::find('36');  // ID is the code (CHAR)

if ($province) {
    echo "Province found: {$province->name} (ID: {$province->id})\n";
    $regencies = $province->regencies()->get();
    echo 'Regencies count: ' . $regencies->count() . "\n";
    echo "First 3 regencies:\n";
    foreach ($regencies->take(3) as $r) {
        echo "  - {$r->name}\n";
    }
} else {
    echo "Province not found\n";
}
