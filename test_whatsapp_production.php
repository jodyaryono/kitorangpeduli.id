<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$service = new App\Services\WhatsAppService();
$result = $service->sendOTP('+6285719195627', '123456');

echo "WhatsApp Test Result:\n";
echo json_encode($result, JSON_PRETTY_PRINT);
echo "\n";
