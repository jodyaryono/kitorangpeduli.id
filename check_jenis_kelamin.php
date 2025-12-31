<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking jenis_kelamin column and existing data\n";
echo str_repeat('=', 80) . "\n\n";

// Check column definition
$columns = DB::select("
    SELECT column_name, data_type, character_maximum_length
    FROM information_schema.columns
    WHERE table_name = 'residents'
    AND column_name = 'jenis_kelamin'
");

foreach ($columns as $col) {
    echo "Column: {$col->column_name}\n";
    echo "Type: {$col->data_type}\n";
    echo "Max Length: {$col->character_maximum_length}\n\n";
}

// Check existing data values
$existing = DB::select('
    SELECT DISTINCT jenis_kelamin, COUNT(*) as count
    FROM residents
    WHERE jenis_kelamin IS NOT NULL
    GROUP BY jenis_kelamin
    ORDER BY jenis_kelamin
');

echo "Existing jenis_kelamin values:\n";
foreach ($existing as $row) {
    echo "  '{$row->jenis_kelamin}' => {$row->count} records\n";
}

echo "\nDone!\n";
