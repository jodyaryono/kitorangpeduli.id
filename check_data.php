<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking provinces and users...\n\n";
echo 'Provinces: ' . DB::table('provinces')->count() . "\n";
echo 'Users (field_officer): ' . DB::table('users')->where('role', 'field_officer')->count() . "\n\n";

echo "First 5 provinces:\n";
$provinces = DB::table('provinces')->limit(5)->get();
foreach ($provinces as $province) {
    echo "- {$province->nama} ({$province->kode})\n";
}
