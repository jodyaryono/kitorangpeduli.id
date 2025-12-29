<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking families table data\n";
echo str_repeat('=', 80) . "\n\n";

$families = App\Models\Family::latest()->limit(5)->get();

if ($families->count() > 0) {
    echo "Found {$families->count()} families:\n\n";

    foreach ($families as $family) {
        echo "Family ID: {$family->id}\n";
        echo 'No KK: ' . ($family->no_kk ?? 'NULL') . "\n";
        echo 'Kepala Keluarga: ' . ($family->kepala_keluarga ?? 'NULL') . "\n";
        echo 'Alamat: ' . ($family->alamat ?? 'NULL') . "\n";
        echo 'RT/RW: ' . ($family->rt ?? '-') . '/' . ($family->rw ?? '-') . "\n";
        echo 'KK Image Path: ' . ($family->kk_image_path ?? 'NULL') . "\n";
        echo 'Province ID: ' . ($family->province_id ?? 'NULL') . "\n";
        echo 'Regency ID: ' . ($family->regency_id ?? 'NULL') . "\n";
        echo 'District ID: ' . ($family->district_id ?? 'NULL') . "\n";
        echo 'Village ID: ' . ($family->village_id ?? 'NULL') . "\n";

        // Check linked residents
        $residents = App\Models\Resident::where('family_id', $family->id)->get();
        echo "Linked Residents: {$residents->count()}\n";
        foreach ($residents as $resident) {
            echo "  - {$resident->nama_lengkap} (ID: {$resident->id})\n";
        }

        echo "Created: {$family->created_at}\n";
        echo "Updated: {$family->updated_at}\n";
        echo str_repeat('-', 80) . "\n\n";
    }
} else {
    echo "No families found in database.\n";
}

// Check recent answers with media_path
echo "\nRecent file uploads in answers table:\n";
echo str_repeat('-', 80) . "\n";

$answers = App\Models\Answer::whereNotNull('media_path')
    ->latest('updated_at')
    ->limit(5)
    ->get();

if ($answers->count() > 0) {
    foreach ($answers as $answer) {
        echo "Answer ID: {$answer->id} | Question ID: {$answer->question_id}\n";
        echo "Response ID: {$answer->response_id}\n";
        echo "File: {$answer->answer_text}\n";
        echo "Path: {$answer->media_path}\n";
        echo "Updated: {$answer->updated_at}\n";
        echo str_repeat('-', 40) . "\n";
    }
} else {
    echo "No file uploads found.\n";
}

echo "\nDone!\n";
