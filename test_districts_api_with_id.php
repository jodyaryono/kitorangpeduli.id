<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Simulate API request to /api/wilayah/districts/3674
$request = Illuminate\Http\Request::create('/api/wilayah/districts/3674', 'GET');
$request->headers->set('Accept', 'application/json');

try {
    $response = $kernel->handle($request);

    echo "=== Testing /api/wilayah/districts/3674 ===\n\n";
    echo 'HTTP Status: ' . $response->getStatusCode() . "\n";
    echo "Response Headers:\n";
    foreach ($response->headers->all() as $key => $values) {
        foreach ($values as $value) {
            echo "  {$key}: {$value}\n";
        }
    }
    echo "\nResponse Content:\n";
    $content = $response->getContent();
    if ($json = json_decode($content)) {
        echo json_encode($json, JSON_PRETTY_PRINT) . "\n";
    } else {
        echo $content . "\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);
