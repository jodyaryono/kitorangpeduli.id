<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking residents table\n";
echo str_repeat('=', 80) . "\n\n";

$count = App\Models\Resident::count();
echo "Total residents: {$count}\n\n";

if ($count > 0) {
    $residents = App\Models\Resident::latest()->limit(5)->get();
    foreach ($residents as $resident) {
        echo "Resident ID: {$resident->id}\n";
        echo "Nama: {$resident->nama_lengkap}\n";
        echo "NIK: " . ($resident->nik ?? 'NULL') . "\n";
        echo "Family ID: " . ($resident->family_id ?? 'NULL') . "\n";
        echo "Created: {$resident->created_at}\n";
        echo str_repeat('-', 80) . "\n";
    }
}

// Check responses with family_members data
echo "\nChecking responses with family_members data:\n";
echo str_repeat('-', 80) . "\n";

$responses = App\Models\Response::whereNotNull('family_members')->get();

echo "Found {$responses->count()} responses with family_members\n\n";

foreach ($responses as $response) {
    echo "Response ID: {$response->id}\n";
    echo "Status: {$response->status}\n";
    $members = json_decode($response->family_members, true);
    if ($members && is_array($members) && count($members) > 0) {
        echo "Family members count: " . count($members) . "\n";
        echo "Data: " . json_encode($members, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "Family members: empty or invalid\n";
    }
    echo str_repeat('-', 40) . "\n";
}

echo "\nDone!\n";
