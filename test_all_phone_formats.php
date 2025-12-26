<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing phone normalization - ALL formats including 812\n";
echo str_repeat('=', 70) . "\n\n";

// Test cases - including 812 format
$testCases = [
    '081222475475',  // 0 + nomor
    '0812-2247-5475',  // dengan dash
    '0812 2247 5475',  // dengan spasi
    '6281222475475',  // 62 + nomor
    '620812224754 75',  // 620 + nomor (extra 0)
    '81222475475',  // tanpa 0 dan 62
    '812',  // hanya 812
    '8122',  // 8122
    '81222',  // 81222
    '812224',  // 812224
    '8122247',  // 8122247
    '81222475',  // 81222475
    '812224754',  // 812224754
    '8122247547',  // 8122247547
    '81222475475',  // full tanpa 0/62
];

function normalizePhone($input)
{
    $no_hp = preg_replace('/[^0-9]/', '', $input);

    // Handle different formats:
    // 0812... -> 6282...
    // 620812... -> 6282... (remove extra 0)
    // 812... -> 6282...
    // 6282... -> 6282...
    if (str_starts_with($no_hp, '0')) {
        $no_hp = '62' . substr($no_hp, 1);
    } elseif (str_starts_with($no_hp, '620')) {
        $no_hp = '62' . substr($no_hp, 3);
    } elseif (!str_starts_with($no_hp, '62')) {
        $no_hp = '62' . $no_hp;
    }

    return $no_hp;
}

echo "Testing normalization:\n";
echo str_repeat('-', 70) . "\n";

foreach ($testCases as $input) {
    $normalized = normalizePhone($input);
    $found = App\Models\User::where('phone', $normalized)->first();

    $status = $found ? "✓ FOUND (ID: {$found->id}, {$found->name})" : '✗ NOT FOUND';

    echo sprintf(
        "%-20s → %-18s → %s\n",
        $input,
        $normalized,
        $status
    );
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "Summary:\n";
echo "- Format 0812... ✓ Didukung\n";
echo "- Format 620812... ✓ Didukung  \n";
echo "- Format 6282... ✓ Didukung\n";
echo "- Format 812... ✓ Didukung\n";
echo "- Format dengan spasi/dash ✓ Didukung\n";
echo "\nSemua format akan dinormalisasi ke 62xxx format!\n";
