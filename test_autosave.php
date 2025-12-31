<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Simulate HTTP request
$request = \Illuminate\Http\Request::create(
    '/questionnaire/8/autosave',
    'POST',
    [
        '_token' => 'test-token',
        'question_id' => 'officer_notes',
        'answer' => 'Test catatan pencacah',
        'response_id' => 7
    ]
);

// Set session data (simulate authenticated user)
$request->setLaravelSession(app('session.store'));
$request->session()->put('_token', 'test-token');

echo "\n=== TESTING AUTOSAVE ENDPOINT ===\n\n";
echo "Request URL: /questionnaire/8/autosave\n";
echo "Method: POST\n";
echo "Data:\n";
echo "  question_id: officer_notes\n";
echo "  answer: Test catatan pencacah\n";
echo "  response_id: 7\n\n";

try {
    $response = $kernel->handle($request);

    echo 'HTTP Status: ' . $response->getStatusCode() . "\n";
    echo 'Response Body: ' . $response->getContent() . "\n\n";

    if ($response->getStatusCode() === 200) {
        echo "✅ SUCCESS - Autosave working!\n";

        // Check database
        $dbResponse = \App\Models\Response::find(7);
        echo "\nDatabase check:\n";
        echo '  officer_notes: ' . ($dbResponse->officer_notes ?? 'NULL') . "\n";
    } else {
        echo '❌ FAILED - Status code: ' . $response->getStatusCode() . "\n";
    }
} catch (\Exception $e) {
    echo '❌ EXCEPTION: ' . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);
