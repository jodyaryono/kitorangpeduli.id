<?php
// Script to sync wilayah data from local to production
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Exporting provinces from local ===\n";
$provinces = DB::table('provinces')->orderBy('id')->get(['id', 'name']);
$sql = "-- Provinces data\nDELETE FROM provinces WHERE id IN (" . $provinces->pluck('id')->map(fn($id) => "'$id'")->implode(',') . ");\n";
foreach ($provinces as $p) {
    $name = addslashes($p->name);
    $sql .= "INSERT INTO provinces (id, name, created_at, updated_at) VALUES ('$p->id', '$name', NOW(), NOW());\n";
}
file_put_contents('provinces_export.sql', $sql);
echo "Exported " . $provinces->count() . " provinces\n";

echo "\n=== Exporting regencies from local ===\n";
$regencies = DB::table('regencies')->orderBy('id')->get(['id', 'province_id', 'name']);
$sql = "-- Regencies data\n";
foreach ($regencies as $r) {
    $name = addslashes($r->name);
    $sql .= "INSERT INTO regencies (id, province_id, code, name, created_at, updated_at) VALUES ($r->id, '$r->province_id', LPAD('$r->id', 4, '0'), '$name', NOW(), NOW()) ON CONFLICT (id) DO UPDATE SET province_id = '$r->province_id', name = '$name';\n";
}
file_put_contents('regencies_export.sql', $sql);
echo "Exported " . $regencies->count() . " regencies\n";

echo "\nFiles created: provinces_export.sql, regencies_export.sql\n";
