<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Opd;

echo "OPD List (by ID order):\n\n";

$opds = Opd::orderBy('id')->get();

foreach ($opds as $opd) {
    echo "ID {$opd->id}: {$opd->name}\n";
}

echo "\n\nExpected mapping (index 0-9 in templates):\n";
echo "Index 0 = ID 1: Dinas Kesehatan\n";
echo "Index 1 = ID 2: Dinas Pendidikan\n";
echo "Index 2 = ID 3: Dinas PUPR\n";
echo "Index 3 = ID 4: Dinas Perindustrian/Perdagangan\n";
echo "Index 4 = ID 5: Dinas Kependudukan\n";
echo "Index 5 = ID 6: Dinas Tenaga Kerja\n";
echo "Index 6 = ID 7: Dinas Pemberdayaan Masyarakat\n";
echo "Index 7 = ID 8: Dinas Sosial\n";
echo "Index 8 = ID 9: Dinas Lingkungan Hidup\n";
echo "Index 9 = ID 10: Dinas DP3AKB\n";
