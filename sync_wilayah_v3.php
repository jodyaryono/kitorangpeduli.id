<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Export provinces (no code column)
$provinces = DB::table('provinces')->orderBy('id')->get();
$sql = "-- Provinces data (UPSERT without code column)\n\n";
foreach ($provinces as $province) {
    $sql .= sprintf(
        "INSERT INTO provinces (id, name, created_at, updated_at) VALUES ('%s', '%s', NOW(), NOW()) ON CONFLICT (id) DO UPDATE SET name = '%s', updated_at = NOW();\n",
        $province->id,
        addslashes($province->name),
        addslashes($province->name)
    );
}
file_put_contents('provinces_upsert_v3.sql', $sql);
echo 'Exported ' . count($provinces) . " provinces\n";

// Export regencies (no code column)
$regencies = DB::table('regencies')->orderBy('id')->get();
$sql = "-- Regencies data (UPSERT without code column)\n";
$sql .= "-- First, delete regencies that might have issues\n";
$sql .= "DELETE FROM regencies WHERE code IS NULL OR code = '';\n\n";

foreach ($regencies as $regency) {
    $sql .= sprintf(
        "INSERT INTO regencies (id, province_id, name, created_at, updated_at) VALUES ('%s', '%s', '%s', NOW(), NOW()) ON CONFLICT (id) DO UPDATE SET province_id = '%s', name = '%s', updated_at = NOW();\n",
        $regency->id,
        $regency->province_id,
        addslashes($regency->name),
        $regency->province_id,
        addslashes($regency->name)
    );
}
file_put_contents('regencies_upsert_v3.sql', $sql);
echo 'Exported ' . count($regencies) . " regencies\n";

// Export districts (no code column)
$districts = DB::table('districts')->orderBy('id')->get();
$sql = "-- Districts data (UPSERT without code column)\n";
$sql .= "-- First, delete districts that might have issues\n";
$sql .= "DELETE FROM districts WHERE code IS NULL OR code = '';\n\n";

foreach ($districts as $district) {
    $name = str_replace("'", "''", $district->name);  // PostgreSQL escape
    $sql .= sprintf(
        "INSERT INTO districts (id, regency_id, name, created_at, updated_at) VALUES ('%s', '%s', '%s', NOW(), NOW()) ON CONFLICT (id) DO UPDATE SET regency_id = '%s', name = '%s', updated_at = NOW();\n",
        $district->id,
        $district->regency_id,
        $name,
        $district->regency_id,
        $name
    );
}
file_put_contents('districts_upsert_v3.sql', $sql);
echo 'Exported ' . count($districts) . " districts\n";

// Export villages (no code column)
$villages = DB::table('villages')->orderBy('id')->get();
$sql = "-- Villages data (UPSERT without code column)\n";
$sql .= "-- First, delete villages that might have issues\n";
$sql .= "DELETE FROM villages WHERE code IS NULL OR code = '';\n\n";

foreach ($villages as $village) {
    $name = str_replace("'", "''", $village->name);  // PostgreSQL escape
    $sql .= sprintf(
        "INSERT INTO villages (id, district_id, name, created_at, updated_at) VALUES ('%s', '%s', '%s', NOW(), NOW()) ON CONFLICT (id) DO UPDATE SET district_id = '%s', name = '%s', updated_at = NOW();\n",
        $village->id,
        $village->district_id,
        $name,
        $village->district_id,
        $name
    );
}
file_put_contents('villages_upsert_v3.sql', $sql);
echo 'Exported ' . count($villages) . " villages\n";

echo "\nGenerated files:\n";
echo "- provinces_upsert_v3.sql\n";
echo "- regencies_upsert_v3.sql\n";
echo "- districts_upsert_v3.sql\n";
echo "- villages_upsert_v3.sql\n";
