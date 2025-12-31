<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== UPDATING JENIS KELAMIN CONSTRAINT ===\n\n";

// Drop old constraint
echo "Dropping old constraint...\n";
DB::statement('ALTER TABLE residents DROP CONSTRAINT IF EXISTS respondents_jenis_kelamin_check');
echo "✅ Old constraint dropped\n\n";

// Convert the data FIRST before adding new constraint
echo "Converting data from L/P to 1/2...\n";

// Update L -> 1 (Pria/Laki-laki)
$updated1 = DB::table('residents')
    ->where('jenis_kelamin', 'L')
    ->update(['jenis_kelamin' => '1']);

echo "Updated {$updated1} records from 'L' to '1'\n";

// Update P -> 2 (Wanita/Perempuan)
$updated2 = DB::table('residents')
    ->where('jenis_kelamin', 'P')
    ->update(['jenis_kelamin' => '2']);

echo "Updated {$updated2} records from 'P' to '2'\n";

// NOW add new constraint for '1' and '2' after data is converted
echo "\nAdding new constraint for '1' and '2'...\n";
DB::statement("ALTER TABLE residents ADD CONSTRAINT residents_jenis_kelamin_check CHECK (jenis_kelamin IN ('1', '2'))");
echo "✅ New constraint added\n\n";

// Check results
echo "\nVerifying results:\n";
$counts = DB::table('residents')
    ->select('jenis_kelamin', DB::raw('count(*) as total'))
    ->groupBy('jenis_kelamin')
    ->get();

foreach ($counts as $count) {
    $label = $count->jenis_kelamin === '1' ? 'Pria' :
             ($count->jenis_kelamin === '2' ? 'Wanita' : 'Unknown');
    echo "  {$count->jenis_kelamin} ({$label}): {$count->total} records\n";
}

echo "\n✅ All done!\n";
