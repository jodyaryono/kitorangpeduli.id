<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking pekerjaan data:\n\n";

$respondents = App\Models\Respondent::select('pekerjaan')
    ->whereNotNull('pekerjaan')
    ->limit(20)
    ->get();

echo 'Found ' . $respondents->count() . " respondents with pekerjaan\n\n";

foreach ($respondents as $respondent) {
    echo '- ' . $respondent->pekerjaan . "\n";
}

echo "\nAll pekerjaan values (unique):\n";
$allPekerjaan = App\Models\Respondent::select('pekerjaan')
    ->whereNotNull('pekerjaan')
    ->where('pekerjaan', '!=', '')
    ->groupBy('pekerjaan')
    ->pluck('pekerjaan');

foreach ($allPekerjaan as $pekerjaan) {
    echo '- ' . $pekerjaan . "\n";
}
