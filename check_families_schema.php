<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking families table schema\n";
echo str_repeat('=', 80) . "\n\n";

$columns = DB::select("
    SELECT column_name, data_type
    FROM information_schema.columns
    WHERE table_name = 'families'
    ORDER BY ordinal_position
");

echo "Columns in 'families' table:\n";
echo str_repeat('-', 80) . "\n";

foreach ($columns as $col) {
    echo "{$col->column_name} ({$col->data_type})\n";
}

echo "\n\nChecking if response_id exists: ";
$hasResponseId = false;
foreach ($columns as $col) {
    if ($col->column_name === 'response_id') {
        $hasResponseId = true;
        break;
    }
}

if ($hasResponseId) {
    echo "✅ YES\n";
} else {
    echo "❌ NO - THIS IS THE PROBLEM!\n";
    echo "\nWe need to add response_id column to families table.\n";
}

// Check families count
$count = App\Models\Family::count();
echo "\n\nTotal families: {$count}\n";

if ($count > 0) {
    echo "\nLatest family:\n";
    $family = App\Models\Family::latest()->first();
    echo "Family ID: {$family->id}\n";
    echo "No KK: {$family->no_kk}\n";
    echo "Alamat: {$family->alamat}\n";

    // Try to find how family is linked to response
    echo "\nChecking family relationships...\n";

    // Check if there's a column that could link to response
    $familyArray = $family->toArray();
    foreach ($familyArray as $key => $value) {
        if (stripos($key, 'response') !== false || stripos($key, 'questionnaire') !== false) {
            echo "  {$key}: {$value}\n";
        }
    }
}

echo "\nDone!\n";
