<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Update officer@opd.local with phone number
$user = User::where('email', 'officer@opd.local')->first();
if ($user) {
    $user->phone = '6285719195627';
    $user->save();
    echo "✓ Phone updated for officer@opd.local: {$user->phone}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: {$user->role}\n";
} else {
    echo "✗ User officer@opd.local not found\n";
}
