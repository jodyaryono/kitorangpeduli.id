<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Exporting provinces with UPSERT ===\n";
$provinces = DB::table('provinces')->orderBy('id')->get(['id', 'name']);
$sql = "-- Provinces data (UPSERT)\n";
foreach ($provinces as $p) {
    $name = addslashes($p->name);
    $sql .= "INSERT INTO provinces (id, name, created_at, updated_at) VALUES ('$p->id', '$name', NOW(), NOW()) ON CONFLICT (id) DO UPDATE SET name = '$name', updated_at = NOW();\n";
}
file_put_contents('provinces_upsert.sql', $sql);
echo "Exported " . $provinces->count() . " provinces\n";

echo "\n=== Exporting regencies with UPSERT ===\n";
$regencies = DB::table('regencies')->orderBy('id')->get(['id', 'province_id', 'name']);
$sql = "-- Regencies data (UPSERT)\n";
$sql .= "-- First, delete regencies without code that are causing problems\n";
$sql .= "DELETE FROM regencies WHERE code IS NULL OR code = '';\n\n";
foreach ($regencies as $r) {
    $name = addslashes($r->name);
    $code = str_pad($r->id, 4, '0', STR_PAD_LEFT);
    $sql .= "INSERT INTO regencies (id, province_id, code, name, created_at, updated_at) VALUES ($r->id, '$r->province_id', '$code', '$name', NOW(), NOW()) ON CONFLICT (id) DO UPDATE SET province_id = '$r->province_id', code = '$code', name = '$name', updated_at = NOW();\n";
}
file_put_contents('regencies_upsert.sql', $sql);
echo "Exported " . $regencies->count() . " regencies\n";
