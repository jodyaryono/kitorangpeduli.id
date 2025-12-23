<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING ORPHANED REGENCIES ===\n\n";

// Check for regencies with province_id 91 (which no longer exists)
$orphaned = DB::table('regencies')->where('province_id', 91)->get();

if ($orphaned->count() > 0) {
    echo "Found {$orphaned->count()} orphaned regencies from deleted province 91:\n";
    foreach ($orphaned as $r) {
        echo "  - {$r->code}: {$r->name}\n";
    }

    echo "\nUpdating them to province 94 (Papua)...\n";
    DB::table('regencies')->where('province_id', 91)->update(['province_id' => 94]);
    echo "✓ Updated\n\n";
} else {
    echo "No orphaned regencies found.\n\n";
}

// Verify final state
echo "Final verification:\n";
$papua = DB::table('provinces')->where('id', 94)->first();
echo "Province: {$papua->name} (ID: {$papua->id})\n";

$regCount = DB::table('regencies')->where('province_id', 94)->count();
echo "Total regencies: $regCount\n\n";

$jayapuras = DB::table('regencies')
    ->where('province_id', 94)
    ->where('name', 'like', '%JAYAPURA%')
    ->get();

echo "Jayapura regencies:\n";
foreach ($jayapuras as $j) {
    echo "  - {$j->code}: {$j->name}\n";
}

echo "\n✓ Done! Silakan refresh browser dan coba lagi.\n";
