<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Citizen Types Table:\n";
echo str_repeat('=', 80) . "\n\n";

$citizenTypes = DB::table('citizen_types')->get();
foreach ($citizenTypes as $ct) {
    echo "ID: {$ct->id}, Name: {$ct->name}\n";
}
