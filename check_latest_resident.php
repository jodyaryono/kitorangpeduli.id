<?php
/** Check latest resident record */
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$r = \App\Models\Resident::latest()->first();

if (!$r) {
    echo "No residents found\n";
    exit;
}

echo "=== Latest Resident (ID: {$r->id}) ===\n";
echo "nama_lengkap: {$r->nama_lengkap}\n\n";

echo "=== ID-based foreign keys ===\n";
echo 'family_relation_id: ' . ($r->family_relation_id ?? 'NULL') . "\n";
echo 'religion_id: ' . ($r->religion_id ?? 'NULL') . "\n";
echo 'marital_status_id: ' . ($r->marital_status_id ?? 'NULL') . "\n";
echo 'education_id: ' . ($r->education_id ?? 'NULL') . "\n";
echo 'occupation_id: ' . ($r->occupation_id ?? 'NULL') . "\n";
echo 'citizen_type_id: ' . ($r->citizen_type_id ?? 'NULL') . "\n";

echo "\n=== Text columns ===\n";
echo 'hubungan_keluarga: ' . ($r->hubungan_keluarga ?? 'NULL') . "\n";
echo 'agama: ' . ($r->agama ?? 'NULL') . "\n";
echo 'status_kawin: ' . ($r->status_kawin ?? 'NULL') . "\n";
echo 'pendidikan: ' . ($r->pendidikan ?? 'NULL') . "\n";
echo 'pekerjaan: ' . ($r->pekerjaan ?? 'NULL') . "\n";

echo "\n=== Other fields ===\n";
echo 'nik: ' . ($r->nik ?? 'NULL') . "\n";
echo 'tempat_lahir: ' . ($r->tempat_lahir ?? 'NULL') . "\n";
echo 'tanggal_lahir: ' . ($r->tanggal_lahir ?? 'NULL') . "\n";
echo 'umur: ' . ($r->umur ?? 'NULL') . "\n";
echo 'jenis_kelamin: ' . ($r->jenis_kelamin ?? 'NULL') . "\n";
echo 'golongan_darah: ' . ($r->golongan_darah ?? 'NULL') . "\n";
echo 'phone: ' . ($r->phone ?? 'NULL') . "\n";
echo 'ktp_image_path: ' . ($r->ktp_image_path ?? 'NULL') . "\n";
echo 'ktp_kia_path: ' . ($r->ktp_kia_path ?? 'NULL') . "\n";

echo "\n=== Wilayah ===\n";
echo 'province_id: ' . ($r->province_id ?? 'NULL') . "\n";
echo 'regency_id: ' . ($r->regency_id ?? 'NULL') . "\n";
echo 'district_id: ' . ($r->district_id ?? 'NULL') . "\n";
echo 'village_id: ' . ($r->village_id ?? 'NULL') . "\n";

echo "\n=== Done ===\n";
