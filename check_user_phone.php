<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$phone = '8814366287';
echo "Searching for phone: $phone\n";
echo str_repeat('=', 60) . "\n";

// Check users
echo "\nUSERS TABLE:\n";
$users = App\Models\User::where('phone', 'like', '%' . $phone . '%')->get();
if ($users->count() > 0) {
    foreach ($users as $u) {
        echo "  ID: {$u->id}, Name: {$u->name}, Phone: {$u->phone}, Role: {$u->role}\n";
    }
} else {
    echo "  No users found\n";
}

// Check residents
echo "\nRESIDENTS TABLE:\n";
$residents = App\Models\Resident::where('phone', 'like', '%' . $phone . '%')->get();
if ($residents->count() > 0) {
    foreach ($residents as $r) {
        echo "  ID: {$r->id}, Name: {$r->nama_lengkap}, Phone: {$r->phone}\n";
    }
} else {
    echo "  No residents found\n";
}

// List all users
echo "\nALL USERS IN SYSTEM:\n";
$allUsers = App\Models\User::select('id', 'name', 'phone', 'role')->get();
foreach ($allUsers as $u) {
    echo "  ID: {$u->id}, Name: {$u->name}, Phone: {$u->phone}, Role: {$u->role}\n";
}
