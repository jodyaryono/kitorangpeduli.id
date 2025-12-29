<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking latest file uploads\n";
echo str_repeat('=', 60) . "\n\n";

// Get latest answers with media_path
$answers = App\Models\Answer::whereNotNull('media_path')
    ->orderBy('updated_at', 'desc')
    ->limit(10)
    ->get();

if ($answers->count() > 0) {
    echo "Latest uploads:\n";
    foreach ($answers as $answer) {
        echo "- Answer ID: {$answer->id}\n";
        echo "  Question ID: {$answer->question_id}\n";
        echo "  Response ID: {$answer->response_id}\n";
        echo "  File Name: {$answer->answer_text}\n";
        echo "  Media Path: {$answer->media_path}\n";
        echo "  Uploaded: {$answer->updated_at}\n";
        echo "\n";
    }
} else {
    echo "No file uploads found in database.\n";
}

echo str_repeat('=', 60) . "\n";
echo "Done!\n";
