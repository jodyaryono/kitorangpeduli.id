<?php

/**
 * Script to verify residents table columns
 * Run: php verify_residents_columns.php
 */
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Schema;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Verifying Residents Table Structure ===\n\n";

// Get all columns from residents table
$columns = Schema::getColumnListing('residents');
sort($columns);

echo 'Total columns: ' . count($columns) . "\n\n";

// Expected columns for family members
$requiredColumns = [
    'family_id',
    'nik',
    'nama_lengkap',
    'tempat_lahir',
    'tanggal_lahir',
    'umur',
    'jenis_kelamin',
    'golongan_darah',
    'phone',
    // ID-based foreign keys
    'citizen_type_id',
    'family_relation_id',
    'religion_id',
    'marital_status_id',
    'education_id',
    'occupation_id',
    // Text columns
    'hubungan_keluarga',
    'agama',
    'status_kawin',
    'pendidikan',
    'pekerjaan',
    // File paths
    'ktp_image_path',
    'ktp_kia_path',
    // Wilayah
    'province_id',
    'regency_id',
    'district_id',
    'village_id',
];

echo "Checking required columns:\n";
$missing = [];
foreach ($requiredColumns as $col) {
    $exists = in_array($col, $columns);
    echo "  - {$col}: " . ($exists ? '✓' : '✗ MISSING') . "\n";
    if (!$exists) {
        $missing[] = $col;
    }
}

if (!empty($missing)) {
    echo "\n⚠️  Missing columns: " . implode(', ', $missing) . "\n";
} else {
    echo "\n✅ All required columns exist!\n";
}

// Show sample resident if any
$sampleResident = DB::table('residents')->first();
if ($sampleResident) {
    echo "\n=== Sample Resident Data ===\n";
    foreach ($requiredColumns as $col) {
        if (property_exists($sampleResident, $col)) {
            $value = $sampleResident->{$col};
            echo "  {$col}: " . (is_null($value) ? 'NULL' : $value) . "\n";
        }
    }
}

echo "\n=== Done ===\n";
