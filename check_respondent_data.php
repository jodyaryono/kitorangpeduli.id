<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Sample data from respondents:\n";
$data = DB::table('respondents')
    ->select('pekerjaan_old', 'pendidikan_old', 'occupation_id', 'education_id')
    ->limit(5)
    ->get();

foreach ($data as $row) {
    echo "Pekerjaan: {$row->pekerjaan_old} | Pendidikan: {$row->pendidikan_old} | Occ ID: {$row->occupation_id} | Edu ID: {$row->education_id}\n";
}
