<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\Response;

echo "🔍 Finding Latest Active Response\n";
echo str_repeat('=', 60) . "\n\n";

$latestResponses = Response::where('status', 'in_progress')
    ->orderBy('updated_at', 'desc')
    ->take(5)
    ->get();

echo "📋 Latest In-Progress Responses:\n\n";

foreach ($latestResponses as $response) {
    echo "Response #{$response->id}:\n";
    echo "   Status: {$response->status}\n";
    echo '   Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";

    if ($response->resident) {
        echo "   Resident Name: {$response->resident->nama_lengkap}\n";
        echo "   Resident NIK: {$response->resident->nik}\n";
        echo "   Family ID: {$response->resident->family_id}\n";
    }

    $answerCount = Answer::where('response_id', $response->id)->count();
    echo "   Saved Answers: {$answerCount}\n";
    echo "\n";
}

echo str_repeat('=', 60) . "\n";
