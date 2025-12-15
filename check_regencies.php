<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DI Yogyakarta (Province ID: 14) ===\n";
$yogya = DB::table('regencies')->where('province_id', 14)->get(['id', 'code', 'name']);
foreach ($yogya as $r) {
    echo sprintf("%s - %s - %s\n", $r->id, $r->code, $r->name);
}

echo "\n=== Papua Barat (Province ID: 34) ===\n";
$papbar = DB::table('regencies')->where('province_id', 34)->get(['id', 'code', 'name']);
foreach ($papbar as $r) {
    echo sprintf("%s - %s - %s\n", $r->id, $r->code, $r->name);
}
