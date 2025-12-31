<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Family;

$family = Family::find(1);

if ($family) {
    echo "Family ID: {$family->id}\n";
    echo "No KK: {$family->no_kk}\n";
    echo "Alamat: {$family->alamat}\n";
    echo "RT: {$family->rt}\n";
    echo "RW: {$family->rw}\n";
    echo "No Bangunan: {$family->no_bangunan}\n\n";

    echo "Wilayah:\n";
    echo "  Province ID: {$family->province_id}\n";
    echo "  Regency ID: {$family->regency_id}\n";
    echo "  District ID: {$family->district_id}\n";
    echo "  Village ID: {$family->village_id}\n";
    echo "  Puskesmas ID: {$family->puskesmas_id}\n\n";

    if ($family->province) {
        echo "  Province Name: {$family->province->name}\n";
    }
    if ($family->regency) {
        echo "  Regency Name: {$family->regency->name}\n";
    }
    if ($family->district) {
        echo "  District Name: {$family->district->name}\n";
    }
    if ($family->village) {
        echo "  Village Name: {$family->village->name}\n";
    }
} else {
    echo "Family not found\n";
}
