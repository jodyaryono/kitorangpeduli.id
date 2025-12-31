<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "Testing saveFamilyMembers endpoint directly\n";
echo str_repeat('=', 80) . "\n\n";

// Simulate the request data
$responseId = 2;
$familyMembersData = [
    [
        'nik' => '',
        'nama_lengkap' => 'BILLY SARWOM',
        'hubungan' => '1',  // Kepala Keluarga
        'tempat_lahir' => 'JAYAPURA',
        'tanggal_lahir' => '28/09/1990',
        'umur' => '35',
        'jenis_kelamin' => '1',  // 1 = Pria
        'status_perkawinan' => 'Kawin',
        'agama' => 'Kristen',
        'pendidikan' => 'Tamat SD/MI',
        'pekerjaan' => 'Wiraswasta/Pedagang/Jasa',
        'golongan_darah' => '',
        'phone' => '',
    ]
];

echo "Simulating AJAX call with data:\n";
echo "Response ID: {$responseId}\n";
echo 'Members count: ' . count($familyMembersData) . "\n";
echo json_encode($familyMembersData, JSON_PRETTY_PRINT) . "\n\n";

// Get the response
$response = App\Models\Response::find($responseId);
if (!$response) {
    echo "❌ Response ID {$responseId} not found!\n";
    exit(1);
}

echo "Response found: ID {$response->id}, Status: {$response->status}\n\n";

// Check for family via Method 1 (resident)
echo "Method 1: Via resident\n";
echo str_repeat('-', 80) . "\n";
$resident = $response->resident;
if ($resident) {
    echo "✅ Resident found: ID {$resident->id}, Family ID: {$resident->family_id}\n";
    if ($resident->family_id) {
        $family = App\Models\Family::find($resident->family_id);
        if ($family) {
            echo "✅ Family found via resident: ID {$family->id}, No KK: {$family->no_kk}\n";
        }
    }
} else {
    echo "❌ No resident linked to response\n";
}

// Check for family via Method 2 (no_kk)
echo "\nMethod 2: Via no_kk from answers\n";
echo str_repeat('-', 80) . "\n";
$noKkAnswer = App\Models\Answer::where('response_id', $response->id)
    ->whereIn('question_id', [268, 223])
    ->whereNotNull('answer_text')
    ->first();

if ($noKkAnswer) {
    echo "✅ No KK answer found: {$noKkAnswer->answer_text} (Question ID: {$noKkAnswer->question_id})\n";

    $family = App\Models\Family::where('no_kk', $noKkAnswer->answer_text)->first();
    if ($family) {
        echo "✅ Family found by no_kk: ID {$family->id}\n";
        echo "   No KK: {$family->no_kk}\n";
        echo "   Alamat: {$family->alamat}\n";

        // Now test creating residents
        echo "\nCreating residents...\n";
        echo str_repeat('-', 80) . "\n";

        // Delete existing
        $deleted = App\Models\Resident::where('family_id', $family->id)->delete();
        echo "Deleted {$deleted} existing residents\n";

        // Create new
        foreach ($familyMembersData as $idx => $memberData) {
            $tanggalLahir = null;
            if (!empty($memberData['tanggal_lahir'])) {
                try {
                    $tanggalLahir = \Carbon\Carbon::createFromFormat('d/m/Y', $memberData['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                    echo "⚠️ Invalid date format: {$memberData['tanggal_lahir']}\n";
                }
            }

            // Map jenis_kelamin: '1' -> 'L', '2' -> 'P'
            $jenisKelamin = null;
            if (!empty($memberData['jenis_kelamin'])) {
                $jk = trim($memberData['jenis_kelamin']);
                if ($jk === '1') {
                    $jenisKelamin = 'L';
                } elseif ($jk === '2') {
                    $jenisKelamin = 'P';
                }
            }

            $resident = App\Models\Resident::create([
                'family_id' => $family->id,
                'nama_lengkap' => $memberData['nama_lengkap'] ?? null,
                'nik' => $memberData['nik'] ?? null,
                'hubungan_keluarga' => $memberData['hubungan'] ?? null,
                'tempat_lahir' => $memberData['tempat_lahir'] ?? null,
                'tanggal_lahir' => $tanggalLahir,
                'umur' => $memberData['umur'] ?? null,
                'jenis_kelamin' => $jenisKelamin,
                'status_kawin' => $memberData['status_perkawinan'] ?? null,
                'agama' => $memberData['agama'] ?? null,
                'pendidikan' => $memberData['pendidikan'] ?? null,
                'pekerjaan' => $memberData['pekerjaan'] ?? null,
                'golongan_darah' => $memberData['golongan_darah'] ?? null,
                'phone' => $memberData['phone'] ?? null,
            ]);

            echo "✅ Created resident #{$idx}: ID {$resident->id}, {$resident->nama_lengkap}\n";
        }

        $count = App\Models\Resident::where('family_id', $family->id)->count();
        echo "\n✅ Total residents in family: {$count}\n";
    } else {
        echo "❌ Family not found with no_kk: {$noKkAnswer->answer_text}\n";
    }
} else {
    echo "❌ No no_kk answer found\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "Test complete!\n\n";

echo "Now check residents table:\n";
$allResidents = App\Models\Resident::all();
echo 'Total residents: ' . $allResidents->count() . "\n";
foreach ($allResidents as $r) {
    echo "  - {$r->nama_lengkap} (Family: {$r->family_id}, Hubungan: {$r->hubungan_keluarga})\n";
}
