<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/database/seeders/TempOfficerSeeder.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$seeder = new Database\Seeders\TempOfficerSeeder();
$seeder->run();
