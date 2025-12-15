<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "WhatsApp Configuration Check:\n";
echo "============================\n";
echo 'URL: ' . config('services.whatsapp.url') . "\n";
echo 'Token: ' . config('services.whatsapp.token') . "\n";
echo 'Token length: ' . strlen(config('services.whatsapp.token')) . "\n";
echo "\n";

echo "ENV Values:\n";
echo "===========\n";
echo 'WA_URL: ' . env('WA_URL') . "\n";
echo 'WA_TOKEN: ' . env('WA_TOKEN') . "\n";
echo 'WA_TOKEN length: ' . strlen(env('WA_TOKEN')) . "\n";
