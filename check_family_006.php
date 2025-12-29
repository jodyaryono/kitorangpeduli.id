<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking if family exists with no_kk = 006\n";
echo str_repeat('=', 80) . "\n\n";

$family = App\Models\Family::where('no_kk', '006')->first();

if ($family) {
    echo "✅ Family found!\n";
    echo "Family ID: {$family->id}\n";
    echo "No KK: {$family->no_kk}\n";
    echo "Alamat: {$family->alamat}\n";
    echo "Province: {$family->province_id}\n";
    echo "Regency: {$family->regency_id}\n";
    echo "District: {$family->district_id}\n";
    echo "Village: {$family->village_id}\n";

    $residents = App\Models\Resident::where('family_id', $family->id)->get();
    echo "\n✅ Residents in this family: " . $residents->count() . "\n";

    foreach ($residents as $resident) {
        echo "  - {$resident->nama_lengkap} ({$resident->hubungan_keluarga})\n";
    }

    echo "\n✅ Ready to accept family members!\n";
} else {
    echo "❌ Family with no_kk = 006 NOT FOUND\n";
    echo "\nThis means syncFamilyData hasn't run yet.\n";
    echo "Please upload KK image first in Section IV to trigger family creation.\n";
}

// Check answer for no_kk
echo "\n\nChecking answers for no_kk (Q268):\n";
echo str_repeat('-', 80) . "\n";

$answer = App\Models\Answer::where('response_id', 2)
    ->where('question_id', 268)
    ->first();

if ($answer) {
    echo "✅ Answer found for Q268 (No. Keluarga)\n";
    echo "Answer text: {$answer->answer_text}\n";
} else {
    echo "⚠️ No answer for Q268 yet\n";
}

echo "\nDone!\n";
