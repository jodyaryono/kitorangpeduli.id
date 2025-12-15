<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Updating regencies code based on ID pattern ===\n";
// Regency ID follows pattern: PPXX where PP = province code, XX = regency number
// e.g., ID 3402 = Province 34, Regency 02
// But we need to map this to proper province_id first

$updated = DB::update("
    UPDATE regencies r
    SET code = LPAD(r.id::text, 4, '0')
    WHERE (code IS NULL OR code = '')
    AND id >= 1101 AND id <= 9499
");

echo "Updated $updated regencies with code\n\n";

echo "=== Now fix province_id based on code ===\n";
$fixed = DB::update("
    UPDATE regencies
    SET province_id = (
        SELECT id FROM provinces 
        WHERE code = SUBSTRING(regencies.code, 1, 2)
    )
    WHERE SUBSTRING(code, 1, 2) != (
        SELECT code FROM provinces WHERE id = regencies.province_id
    )
    AND EXISTS (
        SELECT 1 FROM provinces 
        WHERE code = SUBSTRING(regencies.code, 1, 2)
    )
");

echo "Fixed province_id for $fixed regencies\n\n";

echo "=== Verification ===\n";
echo "Papua Barat:\n";
$pb = DB::table('regencies')->where('code', 'like', '92%')->get(['code', 'name']);
echo "Count: " . $pb->count() . "\n";
foreach ($pb->take(5) as $r) {
    echo "  {$r->code} - {$r->name}\n";
}

echo "\nYogyakarta:\n";
$ygy = DB::table('regencies')->where('code', 'like', '34%')->get(['code', 'name']);
echo "Count: " . $ygy->count() . "\n";
foreach ($ygy as $r) {
    echo "  {$r->code} - {$r->name}\n";
}
