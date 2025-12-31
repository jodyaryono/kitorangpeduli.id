<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "📤 Uploading Billy Wasom family to production...\n\n";

// Family data - Using Tangerang Selatan
$familyData = [
    'no_kk' => '9471060205170001',
    'province_id' => 36,  // Banten
    'regency_id' => 3674,  // Tangerang Selatan
    'district_id' => 3674010,  // Ciputat
    'village_id' => 3674010001,  // Cipayung
    'rt' => '001',
    'rw' => '001',
    'alamat' => 'Jl. Test Billy Wasom',
    'no_bangunan' => '001',
    'updated_by_user_id' => 1
];

// Residents data
$residentsData = [
    [
        'citizen_type_id' => 1,
        'nik' => '1234567890123456',
        'nama_lengkap' => 'BILLY WASOM',
        'tempat_lahir' => 'JAYAPURA',
        'tanggal_lahir' => '1985-05-15',
        'jenis_kelamin' => '1',
        'kewarganegaraan' => 'WNI',
        'phone' => '6281398718772',
        'occupation_id' => 5,
        'education_id' => 5,
        'family_relation_id' => 1,
        'marital_status_id' => 1,
        'religion_id' => 2,
        'umur' => 40,
        'verification_status' => 'pending',
        'updated_by_user_id' => 1,
        'hubungan_keluarga' => '1',
        'agama' => '2',
        'status_kawin' => '1',
        'pekerjaan' => '5',
        'pendidikan' => '5'
    ],
    [
        'citizen_type_id' => 1,
        'nama_lengkap' => 'MATELDA S.LIEM',
        'tanggal_lahir' => '1990-03-15',
        'jenis_kelamin' => '2',
        'kewarganegaraan' => 'WNI',
        'occupation_id' => 5,
        'education_id' => 5,
        'family_relation_id' => 2,
        'marital_status_id' => 1,
        'religion_id' => 2,
        'umur' => 35,
        'verification_status' => 'pending',
        'updated_by_user_id' => 1,
        'hubungan_keluarga' => '2',
        'agama' => '2',
        'status_kawin' => '1',
        'pekerjaan' => '5',
        'pendidikan' => '5'
    ],
    [
        'citizen_type_id' => 1,
        'nama_lengkap' => 'BILL SR.',
        'tanggal_lahir' => '1930-02-10',
        'jenis_kelamin' => '1',
        'kewarganegaraan' => 'WNI',
        'occupation_id' => 1,
        'education_id' => 7,
        'family_relation_id' => 6,
        'marital_status_id' => 4,
        'religion_id' => 2,
        'umur' => 95,
        'verification_status' => 'pending',
        'updated_by_user_id' => 1,
        'hubungan_keluarga' => '6',
        'agama' => '2',
        'status_kawin' => '4',
        'pekerjaan' => '1',
        'pendidikan' => '7'
    ]
];

// Check if family exists
$existingFamily = DB::table('families')->where('no_kk', $familyData['no_kk'])->first();

if ($existingFamily) {
    echo "✓ Family already exists with ID: {$existingFamily->id}\n";
    $familyId = $existingFamily->id;
} else {
    $familyId = DB::table('families')->insertGetId($familyData);
    echo "✓ Created family with ID: {$familyId}\n";
}

echo "\nInserting residents...\n";

foreach ($residentsData as $residentData) {
    $residentData['family_id'] = $familyId;

    // Check if resident exists
    $existing = DB::table('residents')
        ->where('family_id', $familyId)
        ->where('nama_lengkap', $residentData['nama_lengkap'])
        ->first();

    if ($existing) {
        echo "  ⏭️  {$residentData['nama_lengkap']} already exists\n";
    } else {
        $residentId = DB::table('residents')->insertGetId($residentData);
        echo "  ✓ Created {$residentData['nama_lengkap']} with ID: {$residentId}\n";
    }
}

echo "\n✅ Billy Wasom family uploaded successfully!\n";
