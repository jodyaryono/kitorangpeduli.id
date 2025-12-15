<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Unique pekerjaan values:\n";
$pekerjaan = DB::table('respondents')
    ->select('pekerjaan_old')
    ->distinct()
    ->orderBy('pekerjaan_old')
    ->get();

foreach ($pekerjaan as $p) {
    echo "- {$p->pekerjaan_old}\n";
}

echo "\n\nUnique pendidikan values:\n";
$pendidikan = DB::table('respondents')
    ->select('pendidikan_old')
    ->distinct()
    ->orderBy('pendidikan_old')
    ->get();

foreach ($pendidikan as $p) {
    echo "- {$p->pendidikan_old}\n";
}
