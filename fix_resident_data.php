<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Resident;

$resident = Resident::find(21);

echo "Updating BILLY WASOM with complete data...\n\n";

$resident->family_relation_id = 1;  // 1 = Kepala Keluarga
$resident->jenis_kelamin = 'L';  // 'L' = Laki-laki
$resident->tanggal_lahir = '1985-05-15';
$resident->tempat_lahir = 'Jayapura';
$resident->save();

echo "✅ Updated!\n\n";
echo "Resident ID: {$resident->id}\n";
echo "Nama: {$resident->nama_lengkap}\n";
echo "Status Keluarga (family_relation_id): {$resident->family_relation_id}\n";
echo "Jenis Kelamin: {$resident->jenis_kelamin}\n";
echo "Tanggal Lahir: {$resident->tanggal_lahir}\n";
echo "Tempat Lahir: {$resident->tempat_lahir}\n";
echo 'Umur: ' . \Carbon\Carbon::parse($resident->tanggal_lahir)->age . " tahun\n";
