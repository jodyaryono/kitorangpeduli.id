<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the specific response from the screenshot (response_id from URL or session)
$response = App\Models\Response::with('resident.family')->whereNotNull('resident_id')->latest()->first();

echo 'Total responses: ' . App\Models\Response::count() . "\n";
echo 'With residents: ' . App\Models\Response::whereNotNull('resident_id')->count() . "\n\n";

if ($response) {
    echo "Found response: {$response->id}\n";
    if ($response->resident) {
        echo "Has resident: {$response->resident->id}\n";
        if ($response->resident->family) {
            echo "Has family: {$response->resident->family->id}\n";
        } else {
            echo "No family linked\n";
        }
    } else {
        echo "No resident linked\n";
    }
}

if ($response && $response->resident && $response->resident->family) {
    $family = $response->resident->family;

    echo "Response ID: {$response->id}\n";
    echo "\nFamily data:\n";
    echo "Province ID: {$family->province_id}\n";
    echo "Regency ID: {$family->regency_id}\n";
    echo "District ID: {$family->district_id}\n";
    echo "Village ID: {$family->village_id}\n";

    echo "\nAnswers for wilayah questions:\n";
    $answers = App\Models\Answer::where('response_id', $response->id)
        ->whereIn('question_id', [214, 215, 216, 217])
        ->get();

    foreach ($answers as $ans) {
        echo "Q{$ans->question_id}: {$ans->answer_text}\n";
    }

    if ($answers->isEmpty()) {
        echo "No wilayah answers found!\n";
    }
} else {
    echo "No response found\n";
}
