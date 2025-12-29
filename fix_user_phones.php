<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing phone formats for all users\n";
echo str_repeat('=', 60) . "\n";

$users = App\Models\User::whereNotNull('phone')->where('phone', '!=', '')->get();

foreach ($users as $user) {
    $oldPhone = $user->phone;
    $newPhone = $oldPhone;

    // Normalize: remove non-numeric
    $newPhone = preg_replace('/[^0-9]/', '', $newPhone);

    // Convert 0xxx to 62xxx
    if (str_starts_with($newPhone, '0')) {
        $newPhone = '62' . substr($newPhone, 1);
    }
    // Already 62xxx - keep as is
    elseif (!str_starts_with($newPhone, '62') && strlen($newPhone) >= 9) {
        $newPhone = '62' . $newPhone;
    }

    if ($oldPhone !== $newPhone) {
        echo "User ID {$user->id} ({$user->name}):\n";
        echo "  Before: {$oldPhone}\n";
        echo "  After:  {$newPhone}\n";

        $user->phone = $newPhone;
        $user->save();

        echo "  ✓ Updated!\n\n";
    } else {
        echo "User ID {$user->id} ({$user->name}): {$oldPhone} - OK (no change needed)\n";
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Done!\n";
