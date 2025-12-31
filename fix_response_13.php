<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$response = \App\Models\Response::find(13);
if ($response) {
    $response->resident_id = 30;
    $response->save();
    echo "✅ Response #13 updated to resident_id=30 (Billy Wasom)\n";
} else {
    echo "❌ Response #13 not found\n";
}
