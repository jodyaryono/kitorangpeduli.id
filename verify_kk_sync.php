<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\Family;
use App\Models\Response;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║               VERIFICATION REPORT - KK TO FAMILIES SYNC                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check families table
$families = Family::latest()->first();

if (!$families) {
    echo "❌ NO FAMILIES FOUND\n";
    echo "   Please fill the KK questionnaire to create family records.\n\n";
    exit;
}

echo "📊 LATEST FAMILY RECORD\n";
echo str_repeat('─', 80) . "\n";
echo "Family ID: {$families->id}\n";
echo "Created: {$families->created_at}\n";
echo "Updated: {$families->updated_at}\n";
echo "\n";

// Check each field
$checks = [
    'no_kk' => '📋 Nomor KK',
    'no_bangunan' => '🏢 No. Bangunan',
    'alamat' => '🏠 Alamat',
    'rt' => '📍 RT',
    'rw' => '📍 RW',
    'province_id' => '🗺️  Province ID',
    'regency_id' => '🗺️  Regency ID',
    'district_id' => '🗺️  District ID',
    'village_id' => '🗺️  Village ID',
    'kk_image_path' => '📷 KK Image Path',
    // Note: kepala_keluarga not checked - will be in family members table
];

$passed = 0;
$failed = 0;

foreach ($checks as $field => $label) {
    $value = $families->$field;

    if ($value) {
        echo "✅ {$label}: ";
        if ($field == 'kk_image_path') {
            echo "✓ File uploaded\n";
        } elseif (in_array($field, ['province_id', 'regency_id', 'district_id', 'village_id'])) {
            echo "{$value} (ID saved correctly)\n";
        } else {
            $display = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
            echo "{$display}\n";
        }
        $passed++;
    } else {
        echo "❌ {$label}: NULL (not filled)\n";
        $failed++;
    }
}

echo "\n";
echo str_repeat('─', 80) . "\n";
echo "Summary: {$passed} passed, {$failed} failed\n";
echo "\n";

// Check recent file uploads
echo "📁 RECENT FILE UPLOADS\n";
echo str_repeat('─', 80) . "\n";

$uploads = Answer::whereNotNull('media_path')
    ->latest('updated_at')
    ->limit(3)
    ->get();

if ($uploads->count() > 0) {
    foreach ($uploads as $upload) {
        echo "Question {$upload->question_id}: {$upload->answer_text}\n";
        echo "  Path: {$upload->media_path}\n";
        echo "  Uploaded: {$upload->updated_at}\n\n";
    }
} else {
    echo "No file uploads found.\n\n";
}

// Check responses
echo "📝 ACTIVE RESPONSES\n";
echo str_repeat('─', 80) . "\n";

$responses = Response::latest()->limit(1)->get();

foreach ($responses as $response) {
    echo "Response ID: {$response->id}\n";
    echo "Status: {$response->status}\n";
    echo "Started: {$response->started_at}\n";

    $kkAnswers = Answer::where('response_id', $response->id)
        ->whereIn('question_id', [214, 215, 216, 217, 219, 220, 223, 225, 266, 269])
        ->count();

    echo "KK Answers: {$kkAnswers}/10\n";
}

echo "\n";
echo str_repeat('═', 80) . "\n";

if ($failed == 0) {
    echo "🎉 ALL CHECKS PASSED! KK data is fully synced to families table.\n";
} elseif ($failed <= 2) {
    echo "⚠️  MOSTLY COMPLETE - {$failed} field(s) need to be filled.\n";
    echo "   Please complete the questionnaire and check again.\n";
} else {
    echo "❌ INCOMPLETE - {$failed} fields are missing.\n";
    echo "   Please refresh page (Ctrl+F5) and fill the KK form completely.\n";
}

echo str_repeat('═', 80) . "\n";
echo "\n";
