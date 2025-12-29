<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Residents Status After 'Simpan Anggota'\n";
echo str_repeat('=', 80) . "\n\n";

// Check residents count
$count = App\Models\Resident::count();
echo "Total residents: {$count}\n\n";

if ($count > 0) {
    echo "Latest residents:\n";
    echo str_repeat('-', 80) . "\n";
    
    $residents = App\Models\Resident::orderBy('created_at', 'desc')->limit(5)->get();
    foreach ($residents as $resident) {
        echo "ID: {$resident->id} | {$resident->nama_lengkap} ({$resident->hubungan_keluarga})\n";
        echo "  Family: {$resident->family_id} | Created: {$resident->created_at->format('Y-m-d H:i:s')}\n";
        echo str_repeat('-', 80) . "\n";
    }
} else {
    echo "❌ NO RESIDENTS YET!\n\n";
    echo "Checking response family_members JSON:\n";
    echo str_repeat('-', 80) . "\n";
    
    $response = App\Models\Response::find(2);
    if ($response) {
        echo "Response ID: {$response->id}\n";
        echo "Status: {$response->status}\n";
        
        $members = json_decode($response->family_members, true);
        if ($members && is_array($members) && count($members) > 0) {
            echo "\n✅ family_members JSON exists with " . count($members) . " members:\n";
            foreach ($members as $idx => $member) {
                echo "  #{$idx}: {$member['nama_lengkap']} - {$member['hubungan']}\n";
            }
            
            echo "\n⚠️ Data ada di JSON tapi TIDAK di residents table!\n";
            echo "Ini berarti saveFamilyMembers() tidak dipanggil atau gagal.\n";
        } else {
            echo "❌ family_members JSON kosong atau null\n";
            echo "Klik 'Simpan Anggota' belum berhasil menyimpan data.\n";
        }
    }
}

// Check Laravel log for errors
echo "\n\nChecking recent Laravel errors:\n";
echo str_repeat('-', 80) . "\n";

$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $recentErrors = array_slice($lines, -50); // Last 50 lines
    
    $found = false;
    foreach ($recentErrors as $line) {
        if (stripos($line, 'family') !== false || stripos($line, 'resident') !== false || stripos($line, 'error') !== false) {
            echo $line;
            $found = true;
        }
    }
    
    if (!$found) {
        echo "No recent errors related to family/resident\n";
    }
} else {
    echo "Log file not found\n";
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "Done!\n";
