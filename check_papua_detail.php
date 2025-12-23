<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ALL PAPUA PROVINCES ===\n";
$papuas = DB::table('provinces')->where('name', 'like', '%PAPUA%')->get();
foreach ($papuas as $p) {
    echo sprintf("\nID: %s, Code: %s, Name: %s\n", $p->id, $p->code, $p->name);

    // Count regencies
    $regCount = DB::table('regencies')->where('province_id', $p->id)->count();
    echo "  Total regencies: $regCount\n";

    // Get Jayapura regencies
    $jays = DB::table('regencies')
        ->where('province_id', $p->id)
        ->where('name', 'like', '%JAYAPURA%')
        ->get();

    if ($jays->count() > 0) {
        echo "  Jayapura regencies:\n";
        foreach ($jays as $j) {
            echo "    - {$j->code}: {$j->name}\n";
        }
    }
}

echo "\n=== DIRECT CHECK: JAYAPURA REGENCIES ===\n";
$jayapuras = DB::table('regencies')->where('name', 'like', '%JAYAPURA%')->get();
foreach ($jayapuras as $j) {
    echo sprintf("%s: %s (province_id: %s)\n", $j->code, $j->name, $j->province_id);
}
