<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking residents table constraints\n";
echo str_repeat('=', 80) . "\n\n";

$constraints = DB::select("
    SELECT
        con.conname AS constraint_name,
        pg_get_constraintdef(con.oid) AS constraint_definition
    FROM pg_constraint con
    JOIN pg_class rel ON rel.oid = con.conrelid
    WHERE rel.relname = 'residents'
    AND con.contype = 'c'
    ORDER BY con.conname
");

foreach ($constraints as $constraint) {
    echo "Constraint: {$constraint->constraint_name}\n";
    echo "Definition: {$constraint->constraint_definition}\n";
    echo str_repeat('-', 80) . "\n";
}

echo "\nDone!\n";
