<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\Family;
use App\Models\Response;

echo "Syncing No. Bangunan (221) and No. Keluarga (268) to Family\n";
echo str_repeat('=', 80) . "\n\n";

$response = Response::find(2);
$family = Family::find(1);

if (!$response || !$family) {
    echo "Response or Family not found!\n";
    exit;
}

// Get answers for Q221 (no_bangunan) and Q268 (no_kk)
$answers = Answer::where('response_id', $response->id)
    ->whereIn('question_id', [221, 268])
    ->get()
    ->keyBy('question_id');

$updateData = [];

if ($bangAnswer = $answers->get(221)) {
    $updateData['no_bangunan'] = $bangAnswer->answer_text;
    echo "Found No. Bangunan: {$bangAnswer->answer_text}\n";
}

if ($kkAnswer = $answers->get(268)) {
    $updateData['no_kk'] = $kkAnswer->answer_text;
    echo "Found No. Keluarga (KK): {$kkAnswer->answer_text}\n";
}

if (!empty($updateData)) {
    echo "\nUpdating Family ID {$family->id}...\n";
    $family->update($updateData);
    echo "✅ Updated successfully!\n\n";

    $family->refresh();
    echo "Current values:\n";
    echo '  no_bangunan: ' . ($family->no_bangunan ?? 'NULL') . "\n";
    echo '  no_kk: ' . ($family->no_kk ?? 'NULL') . "\n";
} else {
    echo "No data to update.\n";
}

echo "\nDone!\n";
