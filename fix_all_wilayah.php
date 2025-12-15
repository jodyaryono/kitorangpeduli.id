<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Backup current regencies count ===\n";
$total = DB::table('regencies')->count();
echo "Total regencies: $total\n\n";

echo "=== Finding mismatched data ===\n";
// Regencies should have code that matches their province code
// e.g., regency code 3401 should be in province with code 34

$mismatched = DB::select("
    SELECT r.id, r.code, r.name, r.province_id, p.code as province_code, p.name as province_name
    FROM regencies r
    JOIN provinces p ON r.province_id = p.id
    WHERE LEFT(r.code::text, 2) != p.code
    LIMIT 20
");

foreach ($mismatched as $m) {
    echo sprintf("Regency: %s (%s) in Province: %s (%s) - SHOULD BE IN PROVINCE CODE: %s\n", 
        $m->name, $m->code, $m->province_name, $m->province_code, substr($m->code, 0, 2));
}

echo "\n=== Fixing mismatched regencies ===\n";
$fixed = DB::update("
    UPDATE regencies
    SET province_id = (
        SELECT id FROM provinces 
        WHERE code = LEFT(regencies.code::text, 2)
    )
    WHERE EXISTS (
        SELECT 1 FROM provinces 
        WHERE code = LEFT(regencies.code::text, 2)
        AND provinces.id != regencies.province_id
    )
");

echo "Fixed $fixed regencies\n\n";

echo "=== Verification ===\n";
echo "Checking Papua Barat (code 92):\n";
$papbar_prov = DB::table('provinces')->where('code', '92')->first();
if ($papbar_prov) {
    $papbar_reg = DB::table('regencies')->where('province_id', $papbar_prov->id)->get(['code', 'name']);
    echo "Province ID: {$papbar_prov->id}, Name: {$papbar_prov->name}\n";
    echo "Regencies ({$papbar_reg->count()}):\n";
    foreach ($papbar_reg->take(10) as $r) {
        echo "  {$r->code} - {$r->name}\n";
    }
}

echo "\nChecking DI Yogyakarta (code 34):\n";
$yogya_prov = DB::table('provinces')->where('code', '34')->first();
if ($yogya_prov) {
    $yogya_reg = DB::table('regencies')->where('province_id', $yogya_prov->id)->get(['code', 'name']);
    echo "Province ID: {$yogya_prov->id}, Name: {$yogya_prov->name}\n";
    echo "Regencies ({$yogya_reg->count()}):\n";
    foreach ($yogya_reg as $r) {
        echo "  {$r->code} - {$r->name}\n";
    }
}
