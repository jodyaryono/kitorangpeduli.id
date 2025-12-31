<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Creating Billy Wasom for Testing ===\n\n";

// Check if Billy Wasom already exists
$existing = DB::table('residents')->where('nik', '9471081234567890')->first();

if ($existing) {
    echo "Billy Wasom already exists (ID: {$existing->id})\n";
    $billyId = $existing->id;
} else {
    // Create family first
    $familyId = DB::table('families')->insertGetId([
        'no_kk' => '9471081234567890',
        'kepala_keluarga' => 'Billy Wasom',
        'alamat' => 'Jl. Test No. 123',
        'rt' => '001',
        'rw' => '002',
        'province_id' => 94,  // Papua
        'regency_id' => 9471,  // Jayapura
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "Created family (ID: {$familyId})\n";

    // Create Billy Wasom
    $billyId = DB::table('residents')->insertGetId([
        'nik' => '9471081234567890',
        'nama_lengkap' => 'Billy Wasom',
        'family_id' => $familyId,
        'family_relation_id' => 1,  // Kepala Keluarga
        'citizen_type_id' => 1,
        'jenis_kelamin' => 'L',
        'tanggal_lahir' => '1990-01-15',
        'tempat_lahir' => 'Jayapura',
        'agama' => 'Kristen',
        'phone' => '6281234567890',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "Created Billy Wasom (ID: {$billyId})\n";
}

// Create response #11 for Billy Wasom (in_progress)
$response11 = DB::table('responses')->where('id', 11)->first();

if ($response11) {
    echo "\nResponse #11 already exists\n";
} else {
    DB::table('responses')->insert([
        'id' => 11,
        'questionnaire_id' => 8,  // Health questionnaire
        'resident_id' => $billyId,
        'entered_by_user_id' => 1,
        'status' => 'in_progress',
        'started_at' => now()->subMinutes(30),
        'created_at' => now()->subMinutes(30),
        'updated_at' => now()->subMinutes(10),
    ]);

    echo "\nCreated Response #11 for Billy Wasom (in_progress)\n";
}

echo "\n=== DONE ===\n";
echo "Billy Wasom should now appear in the list!\n";
