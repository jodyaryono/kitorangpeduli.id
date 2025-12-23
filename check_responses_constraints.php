<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$constraints = DB::select("SELECT conname FROM pg_constraint WHERE conrelid = 'responses'::regclass;");

echo "Constraints on responses table:\n";
foreach ($constraints as $constraint) {
    echo '- ' . $constraint->conname . "\n";
}
