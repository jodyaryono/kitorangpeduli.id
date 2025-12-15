<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Sample Regencies Data ===\n";
$sample = DB::table('regencies')->take(10)->get();
print_r($sample->toArray());

echo "\n=== Columns in regencies table ===\n";
$columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'regencies'");
foreach ($columns as $col) {
    echo "{$col->column_name} ({$col->data_type})\n";
}
