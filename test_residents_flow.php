<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing family members submission flow\n";
echo str_repeat('=', 80) . "\n\n";

// Step 1: Check if residents table has new columns
echo "Step 1: Checking residents table schema\n";
echo str_repeat('-', 80) . "\n";

$columns = DB::select("
    SELECT column_name, data_type
    FROM information_schema.columns
    WHERE table_name = 'residents'
    AND column_name IN ('hubungan_keluarga', 'agama', 'status_kawin', 'pekerjaan', 'pendidikan', 'umur', 'ktp_kia_path')
    ORDER BY column_name
");

foreach ($columns as $col) {
    echo "✓ {$col->column_name} ({$col->data_type})\n";
}

if (count($columns) === 7) {
    echo "\n✅ All 7 new columns exist!\n";
} else {
    echo "\n❌ Missing columns! Expected 7, found " . count($columns) . "\n";
}

// Step 2: Check current residents count
echo "\n\nStep 2: Current residents count\n";
echo str_repeat('-', 80) . "\n";

$count = App\Models\Resident::count();
echo "Total residents: {$count}\n";

if ($count > 0) {
    echo "\n✅ Residents exist! Let's check the latest one:\n\n";
    $latest = App\Models\Resident::latest()->first();
    echo "Resident ID: {$latest->id}\n";
    echo "Nama: {$latest->nama_lengkap}\n";
    echo "NIK: " . ($latest->nik ?? 'NULL') . "\n";
    echo "Family ID: {$latest->family_id}\n";
    echo "Hubungan: {$latest->hubungan_keluarga}\n";
    echo "Tempat Lahir: {$latest->tempat_lahir}\n";
    echo "Tanggal Lahir: {$latest->tanggal_lahir}\n";
    echo "Umur: {$latest->umur}\n";
    echo "Jenis Kelamin: {$latest->jenis_kelamin}\n";
    echo "Status Kawin: {$latest->status_kawin}\n";
    echo "Agama: {$latest->agama}\n";
    echo "Pendidikan: {$latest->pendidikan}\n";
    echo "Pekerjaan: {$latest->pekerjaan}\n";
    echo "Golongan Darah: " . ($latest->golongan_darah ?? 'NULL') . "\n";
    echo "Phone: " . ($latest->phone ?? 'NULL') . "\n";
    echo "KTP/KIA Path: " . ($latest->ktp_kia_path ?? 'NULL') . "\n";
}

// Step 3: Check responses with family_members
echo "\n\nStep 3: Checking responses with family_members data\n";
echo str_repeat('-', 80) . "\n";

$responses = App\Models\Response::whereNotNull('family_members')->get();
echo "Found {$responses->count()} responses with family_members\n\n";

foreach ($responses as $response) {
    echo "Response ID: {$response->id}\n";
    echo "Status: {$response->status}\n";

    $members = json_decode($response->family_members, true);
    if ($members && is_array($members) && count($members) > 0) {
        echo "Family members count: " . count($members) . "\n";

        foreach ($members as $idx => $member) {
            echo "\nMember #{$idx}:\n";
            echo "  Nama: " . ($member['nama_lengkap'] ?? 'NULL') . "\n";
            echo "  Hubungan: " . ($member['hubungan'] ?? 'NULL') . "\n";
            echo "  Tanggal Lahir: " . ($member['tanggal_lahir'] ?? 'NULL') . "\n";
            echo "  Jenis Kelamin: " . ($member['jenis_kelamin'] ?? 'NULL') . "\n";
        }
    } else {
        echo "Family members: empty or invalid\n";
    }
    echo str_repeat('-', 40) . "\n";
}

// Step 4: Instructions
echo "\n\nStep 4: Testing Instructions\n";
echo str_repeat('=', 80) . "\n";
echo "To test the complete flow:\n\n";
echo "1. Open questionnaire in browser (response ID 2 or create new)\n";
echo "2. Fill Section IV (KK data) - upload KK image\n";
echo "3. Fill Section V (Family Members):\n";
echo "   - Fill all required fields\n";
echo "   - Click 'Simpan Anggota' (fields should stay visible but readonly)\n";
echo "4. Fill remaining sections\n";
echo "5. Click 'Kirim' to submit\n";
echo "6. Run this script again to verify residents were created\n\n";
echo "Expected result: residents table should have 1+ records with family_id\n";
echo str_repeat('=', 80) . "\n";

echo "\nDone!\n";
