<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = App\Models\Questionnaire::orderBy('id')
    ->get(['id', 'title', 'visibility', 'opd_id', 'is_active', 'start_date', 'end_date']);

echo $rows->toJson(JSON_PRETTY_PRINT);
