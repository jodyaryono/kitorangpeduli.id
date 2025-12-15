<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$q = App\Models\Questionnaire::forOfficers(1)
    ->available()
    ->get(['id', 'title', 'opd_id', 'visibility', 'is_active', 'start_date', 'end_date']);

echo $q->toJson(JSON_PRETTY_PRINT);
