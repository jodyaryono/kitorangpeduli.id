<?php
/** Check saved data in response */
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$response = \App\Models\Response::find(2);

echo "=== Response ID: 2 ===\n\n";

echo "Family Members JSON:\n";
echo $response->family_members . "\n\n";

echo "Health Data JSON:\n";
echo $response->health_data . "\n\n";

// Check residents with KTP
echo "=== Residents with KTP ===\n";
$residents = \App\Models\Resident::whereNotNull('ktp_image_path')->get();
foreach ($residents as $r) {
    echo "ID: {$r->id}, Name: {$r->nama_lengkap}, KTP: {$r->ktp_image_path}\n";
}
