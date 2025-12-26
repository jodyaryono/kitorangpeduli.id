<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking phone number: 081222475475\n";
echo str_repeat('=', 60) . "\n\n";

// Check in users table
echo "Searching in USERS table:\n";
$users = App\Models\User::where('phone', 'like', '%081222475475%')
    ->orWhere('phone', 'like', '%81222475475%')
    ->orWhere('phone', 'like', '%6281222475475%')
    ->get();

if ($users->count() > 0) {
    foreach ($users as $user) {
        echo "✓ FOUND!\n";
        echo "  ID: {$user->id}\n";
        echo "  Name: {$user->name}\n";
        echo "  Email: {$user->email}\n";
        echo "  Phone: {$user->phone}\n";
        echo "  Role: {$user->role}\n";
        echo '  Active: ' . ($user->is_active ? 'Yes' : 'No') . "\n";
        echo "\n";
    }
} else {
    echo "✗ NOT FOUND in users table\n\n";
}

// Check in residents table
echo "Searching in RESIDENTS table:\n";
$residents = App\Models\Resident::where('phone', 'like', '%081222475475%')
    ->orWhere('phone', 'like', '%81222475475%')
    ->orWhere('phone', 'like', '%6281222475475%')
    ->get();

if ($residents->count() > 0) {
    foreach ($residents as $resident) {
        echo "✓ FOUND!\n";
        echo "  ID: {$resident->id}\n";
        echo "  Name: {$resident->name}\n";
        echo "  NIK: {$resident->nik}\n";
        echo "  Phone: {$resident->phone}\n";
        echo '  Verified: ' . ($resident->verified_at ? 'Yes' : 'No') . "\n";
        echo "\n";
    }
} else {
    echo "✗ NOT FOUND in residents table\n\n";
}

// Check all possible formats
echo "Checking all possible phone formats:\n";
$formats = [
    '081222475475',
    '81222475475',
    '6281222475475',
    '62081222475475',
];

foreach ($formats as $format) {
    echo "  Format: {$format}\n";
    $userCount = App\Models\User::where('phone', $format)->count();
    $residentCount = App\Models\Resident::where('phone', $format)->count();
    echo "    Users: {$userCount}\n";
    echo "    Residents: {$residentCount}\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Done!\n";
