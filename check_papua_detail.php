<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Province 34 Detail ===\n";
$prov34 = DB::table('provinces')->where('id', 34)->first();
print_r($prov34);

echo "\n=== Regencies under province_id 34 ===\n";
$reg34 = DB::table('regencies')->where('province_id', 34)->get();
foreach ($reg34 as $r) {
    echo sprintf("ID:%s Code:%s Name:%s\n", $r->id, $r->code, $r->name);
}

echo "\n=== Search for Bantul regency ===\n";
$bantul = DB::table('regencies')->where('name', 'like', '%BANTUL%')->get();
foreach ($bantul as $r) {
    echo sprintf("ID:%s Code:%s Name:%s Province_ID:%s\n", $r->id, $r->code, $r->name, $r->province_id);
}

echo "\n=== All regencies with code 92xx (Papua Barat) ===\n";
$reg92 = DB::table('regencies')->where('code', 'like', '92%')->get();
echo "Count: " . $reg92->count() . "\n";
foreach ($reg92 as $r) {
    echo sprintf("ID:%s Code:%s Name:%s Province_ID:%s\n", $r->id, $r->code, $r->name, $r->province_id);
}

echo "\n=== All regencies with code 34xx (Yogyakarta) ===\n";
$reg34code = DB::table('regencies')->where('code', 'like', '34%')->get();
echo "Count: " . $reg34code->count() . "\n";
foreach ($reg34code as $r) {
    echo sprintf("ID:%s Code:%s Name:%s Province_ID:%s\n", $r->id, $r->code, $r->name, $r->province_id);
}
