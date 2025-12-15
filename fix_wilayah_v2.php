<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Finding mismatched regencies ===\n";
$mismatched = DB::select("
    SELECT r.id, r.code, r.name, r.province_id, p.code as province_code, p.name as province_name,
           SUBSTRING(r.code, 1, 2) as regency_province_code
    FROM regencies r
    JOIN provinces p ON r.province_id = p.id
    WHERE SUBSTRING(r.code, 1, 2) != p.code
    LIMIT 30
");

echo "Found " . count($mismatched) . " mismatched regencies\n\n";
foreach ($mismatched as $m) {
    echo sprintf("ID:%s Code:%s %s -> Currently in: %s (code:%s) | Should be in province code: %s\n", 
        $m->id, $m->code, $m->name, $m->province_name, $m->province_code, $m->regency_province_code);
}

echo "\n=== Applying fix ===\n";
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

echo "Fixed: $fixed regencies\n\n";

echo "=== Verification - Papua Barat ===\n";
$pb = DB::select("
    SELECT p.id, p.code, p.name, COUNT(r.id) as regency_count
    FROM provinces p
    LEFT JOIN regencies r ON p.id = r.province_id
    WHERE p.code = '92'
    GROUP BY p.id, p.code, p.name
");
foreach ($pb as $p) {
    echo "Province: {$p->name} (code:{$p->code}, id:{$p->id}) - Regencies: {$p->regency_count}\n";
}

$pb_reg = DB::table('regencies')->where('code', 'like', '92%')->get(['code', 'name', 'province_id']);
echo "Regencies with code 92xx:\n";
foreach ($pb_reg as $r) {
    echo "  {$r->code} - {$r->name} (province_id: {$r->province_id})\n";
}

echo "\n=== Verification - DI Yogyakarta ===\n";
$ygy = DB::select("
    SELECT p.id, p.code, p.name, COUNT(r.id) as regency_count
    FROM provinces p
    LEFT JOIN regencies r ON p.id = r.province_id
    WHERE p.code = '34'
    GROUP BY p.id, p.code, p.name
");
foreach ($ygy as $p) {
    echo "Province: {$p->name} (code:{$p->code}, id:{$p->id}) - Regencies: {$p->regency_count}\n";
}

$ygy_reg = DB::table('regencies')->where('code', 'like', '34%')->get(['code', 'name', 'province_id']);
echo "Regencies with code 34xx:\n";
foreach ($ygy_reg as $r) {
    echo "  {$r->code} - {$r->name} (province_id: {$r->province_id})\n";
}
