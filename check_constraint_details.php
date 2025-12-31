<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== CHECKING DATABASE CONSTRAINTS ===\n\n";

// Check constraint definition
$constraint = DB::select("
    SELECT
        con.conname AS constraint_name,
        pg_get_constraintdef(con.oid) AS constraint_definition
    FROM pg_constraint con
    INNER JOIN pg_class rel ON rel.oid = con.conrelid
    WHERE rel.relname = 'residents'
    AND con.conname LIKE '%jenis_kelamin%'
");

if (!empty($constraint)) {
    echo "JENIS KELAMIN CONSTRAINT:\n";
    echo 'Name: ' . $constraint[0]->constraint_name . "\n";
    echo 'Definition: ' . $constraint[0]->constraint_definition . "\n\n";

    if (strpos($constraint[0]->constraint_definition, "'L'") !== false) {
        echo "⚠️ DATABASE REQUIRES 'L' or 'P' - CANNOT use 1 or 2!\n";
        echo "   Attempting to save 1 or 2 will cause constraint violation error.\n\n";
    }
} else {
    echo "No constraint found - numbers may be allowed\n\n";
}

// Check actual data types in residents table
echo "CHECKING EXISTING DATA:\n";
$residents = DB::table('residents')
    ->select('id', 'nama_lengkap', 'jenis_kelamin')
    ->limit(10)
    ->get();

foreach ($residents as $r) {
    echo "ID: {$r->id}, Nama: {$r->nama_lengkap}, Jenis Kelamin: '{$r->jenis_kelamin}' (type: " . gettype($r->jenis_kelamin) . ")\n";
}

echo "\n=== RECOMMENDATION ===\n";
echo "If constraint requires 'L'/'P', we must:\n";
echo "1. Keep modal options as value='L' and value='P'\n";
echo "2. Keep display logic checking for 'L' and 'P'\n";
echo "3. OR remove the constraint if you want to use 1/2\n\n";
