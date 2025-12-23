<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING DUPLICATE PAPUA PROVINCES ===\n\n";

// Get both Papua provinces
$papua91 = DB::table('provinces')->where('id', 91)->first();
$papua94 = DB::table('provinces')->where('id', 94)->first();

if (!$papua91 || !$papua94) {
    echo "ERROR: Could not find both Papua provinces!\n";
    exit(1);
}

echo "Found 2 Papua provinces:\n";
echo "  ID 91: {$papua91->name} (Code: {$papua91->code})\n";
echo "  ID 94: {$papua94->name} (Code: {$papua94->code})\n\n";

// Count regencies for each
$count91 = DB::table('regencies')->where('province_id', 91)->count();
$count94 = DB::table('regencies')->where('province_id', 94)->count();

echo "Regency counts:\n";
echo "  Province 91: $count91 regencies\n";
echo "  Province 94: $count94 regencies\n\n";

// Check if there are Jayapura in each
$jay91 = DB::table('regencies')->where('province_id', 91)->where('name', 'like', '%JAYAPURA%')->count();
$jay94 = DB::table('regencies')->where('province_id', 94)->where('name', 'like', '%JAYAPURA%')->count();

echo "Jayapura regencies:\n";
echo "  Province 91: $jay91\n";
echo "  Province 94: $jay94\n\n";

// Province 94 has more data and has Jayapura, so keep that one
echo "Decision: Keep province ID 94, delete province ID 91\n\n";

// Delete province 91
echo "Deleting province 91...\n";
DB::table('provinces')->where('id', 91)->delete();
echo "✓ Deleted\n\n";

// Verify
echo "Verification:\n";
$remaining = DB::table('provinces')->where('name', 'like', '%PAPUA%')->where('name', 'not like', '%BARAT%')->get();
echo 'Remaining Papua provinces: ' . $remaining->count() . "\n";
foreach ($remaining as $p) {
    echo "  ID {$p->id}: {$p->name} (Code: {$p->code})\n";
    $regCount = DB::table('regencies')->where('province_id', $p->id)->count();
    echo "    → $regCount regencies\n";
}

echo "\n✓ Done!\n";
