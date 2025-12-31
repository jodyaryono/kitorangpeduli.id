<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$result = \Illuminate\Support\Facades\DB::select("SELECT pg_get_constraintdef(oid) as def FROM pg_constraint WHERE conname = 'respondents_jenis_kelamin_check'");

echo "Constraint definition:\n";
print_r($result);
