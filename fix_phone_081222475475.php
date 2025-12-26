<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing phone format for user 081222475475\n";
echo str_repeat('=', 60) . "\n\n";

// Find user
$user = App\Models\User::where('phone', '081222475475')->first();

if (!$user) {
    echo "✗ User with phone 081222475475 not found!\n";
    exit(1);
}

echo "BEFORE:\n";
echo "  ID: {$user->id}\n";
echo "  Name: {$user->name}\n";
echo "  Phone: {$user->phone}\n";
echo "\n";

// Convert to international format
$newPhone = '6281222475475';
$user->phone = $newPhone;
$user->save();

echo "AFTER:\n";
echo "  ID: {$user->id}\n";
echo "  Name: {$user->name}\n";
echo "  Phone: {$user->phone}\n";
echo "\n";

echo "✓ Phone number updated successfully!\n";
echo "\n" . str_repeat('=', 60) . "\n";

// Test login normalization
echo "\nTesting login normalization:\n";
$testInputs = ['081222475475', '6281222475475', '81222475475'];

foreach ($testInputs as $input) {
    echo "  Input: {$input}\n";

    // Normalize (same logic as AuthController)
    $normalized = preg_replace('/[^0-9]/', '', $input);
    $normalized = ltrim($normalized, '0');
    if (!str_starts_with($normalized, '62')) {
        $normalized = '62' . $normalized;
    }

    echo "  Normalized: {$normalized}\n";

    $found = App\Models\User::where('phone', $normalized)->first();
    echo '  Result: ' . ($found ? "✓ FOUND (ID: {$found->id})" : '✗ NOT FOUND') . "\n";
    echo "\n";
}

echo "Done!\n";
