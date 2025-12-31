<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== ALL FAMILIES ===\n\n";

$families = DB::table('families')->get();
echo 'Total families: ' . $families->count() . "\n\n";

foreach ($families as $family) {
    echo "Family ID: {$family->id}\n";

    $members = DB::table('residents')
        ->where('family_id', $family->id)
        ->orderBy('family_relation_id')
        ->get(['id', 'nama_lengkap', 'nik', 'family_relation_id']);

    echo '  Members: ' . $members->count() . "\n";
    foreach ($members as $member) {
        $relation = $member->family_relation_id == 1 ? ' ← KEPALA' : '';
        echo "    - {$member->nama_lengkap} (NIK: {$member->nik}){$relation}\n";
    }
    echo "\n";
}
