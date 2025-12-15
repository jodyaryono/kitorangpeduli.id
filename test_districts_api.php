<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Simulate API request
$request = Illuminate\Http\Request::create('/api/wilayah/districts/3674', 'GET');

try {
    $response = $kernel->handle($request);

    echo 'HTTP Status: ' . $response->getStatusCode() . "\n";
    echo "Response Content:\n";
    echo $response->getContent() . "\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);
