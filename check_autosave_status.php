<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Response;
use App\Models\Answer;

echo "\n=== CHECKING RESPONSE #10 ANSWERS ===\n\n";

$response = Response::find(10);

if (!$response) {
    echo "❌ Response #10 not found\n";
    exit;
}

echo "Response #10:\n";
echo "  - Status: {$response->status}\n";
echo "  - Officer Notes: " . ($response->officer_notes ?? 'NULL') . "\n\n";

// Get all answers
$answers = Answer::where('response_id', 10)
    ->with('question')
    ->orderBy('question_id')
    ->get();

echo "Total answers saved: " . $answers->count() . "\n\n";

if ($answers->count() > 0) {
    echo "Recent answers (last 10):\n";
    foreach ($answers->take(10) as $answer) {
        $questionId = $answer->question_id;
        $questionText = $answer->question ? substr($answer->question->question_text, 0, 50) : 'N/A';
        $value = $answer->answer_text ?? $answer->answer_numeric ?? $answer->selected_options ?? 'NULL';

        echo "  Q{$questionId}: {$questionText}... = {$value}\n";
    }
} else {
    echo "❌ NO ANSWERS SAVED YET!\n";
    echo "\nPossible causes:\n";
    echo "  1. Autosave not working (check JavaScript console)\n";
    echo "  2. CSRF token mismatch\n";
    echo "  3. Route not accessible\n";
    echo "  4. JavaScript error preventing save\n";
}

echo "\n=== CHECKING LAST AUTOSAVE LOG ===\n\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = shell_exec("tail -20 " . escapeshellarg($logFile) . " 2>&1");
    if (stripos($logs, 'autosave') !== false || stripos($logs, 'response_id.*10') !== false) {
        echo "Recent autosave logs found:\n";
        $lines = explode("\n", $logs);
        foreach ($lines as $line) {
            if (stripos($line, 'autosave') !== false || stripos($line, '10') !== false) {
                echo $line . "\n";
            }
        }
    } else {
        echo "❌ No recent autosave logs found for Response #10\n";
    }
} else {
    echo "Log file not found\n";
}
