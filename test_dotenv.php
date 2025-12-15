<?php

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    echo "SUCCESS: .env loaded\n";
    echo 'WA_TOKEN from $_ENV: ' . ($_ENV['WA_TOKEN'] ?? 'NOT SET') . "\n";
    echo 'WA_TOKEN from getenv: ' . (getenv('WA_TOKEN') ?: 'NOT SET') . "\n";
    echo 'WA_URL from $_ENV: ' . ($_ENV['WA_URL'] ?? 'NOT SET') . "\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
