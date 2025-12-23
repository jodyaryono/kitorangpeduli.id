<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;

echo "=== CHECKING JAYAPURA DATA ===\n\n";

// 1. Check provinces
echo "1. Provinces in database:\n";
$provinces = Province::orderBy('name')->get();
foreach ($provinces as $p) {
    echo "   {$p->code}: {$p->name}\n";
}
echo '   Total: ' . $provinces->count() . " provinces\n\n";

// 2. Check Papua province specifically
echo "2. Papua province:\n";
$papua = Province::where('code', '94')->orWhere('name', 'like', '%PAPUA%')->get();
foreach ($papua as $p) {
    echo "   {$p->code}: {$p->name}\n";
}
if ($papua->isEmpty()) {
    echo "   ⚠️ NO PAPUA PROVINCE FOUND!\n";
}
echo "\n";

// 3. Check Jayapura regencies
echo "3. Jayapura regencies:\n";
$jayapuraRegencies = Regency::where('name', 'like', '%JAYAPURA%')->get();
foreach ($jayapuraRegencies as $r) {
    $provinceName = $r->province ? $r->province->name : 'NULL';
    echo "   {$r->code}: {$r->name} (Province: {$provinceName})\n";
}
if ($jayapuraRegencies->isEmpty()) {
    echo "   ⚠️ NO JAYAPURA REGENCIES FOUND!\n";
}
echo "\n";

// 4. Check all regencies
echo "4. All regencies in database:\n";
$regencies = Regency::orderBy('name')->get();
foreach ($regencies as $r) {
    echo "   {$r->code}: {$r->name}\n";
}
echo '   Total: ' . $regencies->count() . " regencies\n\n";

// 5. Check Jayapura Utara district
echo "5. Jayapura Utara district:\n";
$jayapuraUtara = District::where('code', '9471040')->first();
if ($jayapuraUtara) {
    echo "   Found: {$jayapuraUtara->code}: {$jayapuraUtara->name}\n";
    $regencyName = $jayapuraUtara->regency ? $jayapuraUtara->regency->name : 'NULL';
    echo "   Regency: {$regencyName}\n";

    echo "\n   Villages in Jayapura Utara:\n";
    $villages = Village::where('district_id', $jayapuraUtara->id)->orderBy('code')->get();
    foreach ($villages as $v) {
        echo "     - {$v->code}: {$v->name}\n";
    }
} else {
    echo "   ⚠️ JAYAPURA UTARA DISTRICT NOT FOUND!\n";
}
echo "\n";

// 6. Check if there's orphaned data
echo "6. Checking data integrity:\n";
$districtsWithoutRegency = District::whereNull('regency_id')->count();
$villagesWithoutDistrict = Village::whereNull('district_id')->count();
echo "   Districts without regency: {$districtsWithoutRegency}\n";
echo "   Villages without district: {$villagesWithoutDistrict}\n";

echo "\nDone!\n";
