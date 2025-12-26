<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing phone normalization logic\n";
echo str_repeat('=', 60) . "\n\n";

// Test cases
$testCases = [
    '081222475475',
    '0812224754 75',
    '6281222475475',
    '620812224754 75',
    '81222475475',
];

echo "Using WhatsAppService formatPhone:\n";
echo str_repeat('-', 60) . "\n";

$whatsappService = new App\Services\WhatsAppService();
$reflector = new ReflectionClass($whatsappService);
$formatPhone = $reflector->getMethod('formatPhone');
$formatPhone->setAccessible(true);

foreach ($testCases as $input) {
    $normalized = $formatPhone->invoke($whatsappService, $input);
    echo 'Input: ' . str_pad($input, 20) . ' → Normalized: ' . $normalized . "\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Checking if user with phone 6281222475475 exists:\n";

$user = App\Models\User::where('phone', '6281222475475')->first();
if ($user) {
    echo "✓ FOUND - ID: {$user->id}, Name: {$user->name}, Phone: {$user->phone}\n";
} else {
    echo "✗ NOT FOUND\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Testing all input formats against database:\n";
echo str_repeat('-', 60) . "\n";

foreach ($testCases as $input) {
    echo 'Input: ' . str_pad($input, 20);

    // Simulate normalization (same logic as AuthController)
    $no_hp = preg_replace('/[^0-9]/', '', $input);

    if (str_starts_with($no_hp, '0')) {
        $no_hp = '62' . substr($no_hp, 1);
    } elseif (str_starts_with($no_hp, '620')) {
        $no_hp = '62' . substr($no_hp, 3);
    } elseif (!str_starts_with($no_hp, '62')) {
        $no_hp = '62' . $no_hp;
    }

    echo ' → Normalized: ' . str_pad($no_hp, 20);

    $found = App\Models\User::where('phone', $no_hp)->first();
    echo ' → ' . ($found ? "✓ FOUND (ID: {$found->id})" : '✗ NOT FOUND') . "\n";
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Done!\n";
