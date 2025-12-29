<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Response -> Resident -> Family relationship\n";
echo str_repeat('=', 80) . "\n\n";

// Get response ID 2
$response = App\Models\Response::find(2);

if (!$response) {
    echo "❌ Response ID 2 not found\n";
    exit(1);
}

echo "Response ID: {$response->id}\n";
echo "Status: {$response->status}\n";

// Check resident
$resident = $response->resident;
if ($resident) {
    echo "\n✅ Resident found:\n";
    echo "  Resident ID: {$resident->id}\n";
    echo "  Nama: {$resident->nama_lengkap}\n";
    echo "  Family ID: " . ($resident->family_id ?? 'NULL') . "\n";
    
    // Check family
    if ($resident->family_id) {
        $family = App\Models\Family::find($resident->family_id);
        if ($family) {
            echo "\n✅ Family found:\n";
            echo "  Family ID: {$family->id}\n";
            echo "  No KK: {$family->no_kk}\n";
            echo "  Alamat: {$family->alamat}\n";
            
            // Check residents in this family
            $residentsCount = App\Models\Resident::where('family_id', $family->id)->count();
            echo "\n  Residents in this family: {$residentsCount}\n";
            
            if ($residentsCount > 0) {
                echo "\n  Family members:\n";
                $residents = App\Models\Resident::where('family_id', $family->id)->get();
                foreach ($residents as $r) {
                    echo "    - {$r->nama_lengkap} ({$r->hubungan_keluarga})\n";
                }
            }
        } else {
            echo "❌ Family not found with ID {$resident->family_id}\n";
        }
    } else {
        echo "\n⚠️ Resident has no family_id yet (family will be created when KK is uploaded)\n";
    }
} else {
    echo "❌ No resident linked to this response\n";
}

// Check family_members JSON in response
echo "\n\nFamily members JSON in response:\n";
echo str_repeat('-', 80) . "\n";

$members = json_decode($response->family_members, true);
if ($members && is_array($members) && count($members) > 0) {
    echo "✅ Found " . count($members) . " family members in JSON:\n";
    foreach ($members as $idx => $member) {
        echo "  #{$idx}: {$member['nama_lengkap']} - {$member['hubungan']}\n";
    }
} else {
    echo "⚠️ No family_members JSON data yet\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "✅ Test complete!\n\n";

echo "To fix:\n";
echo "1. Make sure Section IV (KK upload) is filled first to create Family record\n";
echo "2. Then fill Section V (family members) and click 'Simpan Anggota'\n";
echo "3. Family members will be saved to residents table\n";
