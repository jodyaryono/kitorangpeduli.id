<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking residents after saveFamilyMember\n";
echo str_repeat('=', 80) . "\n\n";

// Check residents count
$count = App\Models\Resident::count();
echo "Total residents: {$count}\n\n";

if ($count > 0) {
    echo "Latest residents:\n";
    echo str_repeat('-', 80) . "\n";

    $residents = App\Models\Resident::latest()->limit(5)->get();
    foreach ($residents as $resident) {
        echo "\nResident ID: {$resident->id}\n";
        echo "Family ID: {$resident->family_id}\n";
        echo "Nama: {$resident->nama_lengkap}\n";
        echo "NIK: " . ($resident->nik ?? 'NULL') . "\n";
        echo "Hubungan: {$resident->hubungan_keluarga}\n";
        echo "Tempat Lahir: {$resident->tempat_lahir}\n";
        echo "Tanggal Lahir: " . ($resident->tanggal_lahir ? $resident->tanggal_lahir->format('d/m/Y') : 'NULL') . "\n";
        echo "Umur: {$resident->umur}\n";
        echo "Jenis Kelamin: {$resident->jenis_kelamin}\n";
        echo "Status Kawin: {$resident->status_kawin}\n";
        echo "Agama: {$resident->agama}\n";
        echo "Pendidikan: {$resident->pendidikan}\n";
        echo "Pekerjaan: {$resident->pekerjaan}\n";
        echo "Created: {$resident->created_at->format('d/m/Y H:i:s')}\n";
        echo str_repeat('-', 80) . "\n";
    }
}

// Check family_members in responses
echo "\n\nResponses with family_members:\n";
echo str_repeat('-', 80) . "\n";

$responses = App\Models\Response::whereNotNull('family_members')->get();

foreach ($responses as $response) {
    $members = json_decode($response->family_members, true);
    if ($members && is_array($members) && count($members) > 0) {
        echo "\nResponse ID: {$response->id}\n";
        echo "Status: {$response->status}\n";
        echo "Family members count: " . count($members) . "\n";

        // Check if family exists
        $family = App\Models\Family::where('response_id', $response->id)->first();
        echo "Family ID: " . ($family ? $family->id : 'NULL') . "\n";

        if ($family) {
            $residentsCount = App\Models\Resident::where('family_id', $family->id)->count();
            echo "Residents in family: {$residentsCount}\n";
        }

        echo str_repeat('-', 40) . "\n";
    }
}

echo "\n✅ Test complete!\n";
