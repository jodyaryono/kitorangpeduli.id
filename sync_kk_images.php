<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Family;
use App\Models\Answer;
use App\Models\Response;

echo "=== Syncing KK Image Paths to Families ===\n\n";

// Find all answers with media_path for question 266 (Upload Kartu Keluarga)
$kkAnswers = Answer::where('question_id', 266)
    ->whereNotNull('media_path')
    ->get();

echo "Found {$kkAnswers->count()} KK image answers\n\n";

foreach ($kkAnswers as $answer) {
    echo "Processing Answer ID: {$answer->id}, Response ID: {$answer->response_id}\n";
    echo "  Media Path: {$answer->media_path}\n";
    
    $response = Response::find($answer->response_id);
    if (!$response) {
        echo "  ❌ Response not found\n\n";
        continue;
    }
    
    // Try to find family via resident
    $family = null;
    
    if ($response->resident && $response->resident->family_id) {
        $family = Family::find($response->resident->family_id);
        echo "  Found family via resident: {$family->id}\n";
    }
    
    // Try to find family by no_kk from answers
    if (!$family) {
        $noKkAnswer = Answer::where('response_id', $response->id)
            ->whereIn('question_id', [268, 223])
            ->whereNotNull('answer_text')
            ->first();
            
        if ($noKkAnswer) {
            $family = Family::where('no_kk', $noKkAnswer->answer_text)->first();
            if ($family) {
                echo "  Found family by no_kk: {$family->id} (no_kk: {$noKkAnswer->answer_text})\n";
            }
        }
    }
    
    if ($family) {
        $oldPath = $family->kk_image_path;
        $family->update(['kk_image_path' => $answer->media_path]);
        echo "  ✅ Updated family {$family->id}: kk_image_path = {$answer->media_path}\n";
        echo "     (was: " . ($oldPath ?? 'NULL') . ")\n";
    } else {
        echo "  ⚠️ No family found for this response\n";
    }
    
    echo "\n";
}

echo "=== Done ===\n";
