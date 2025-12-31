<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\User;

echo "\n=== CREATING NEW TEST RESPONSE ===\n\n";

// Get questionnaire 8
$questionnaire = Questionnaire::find(8);

if (!$questionnaire) {
    echo "❌ Questionnaire #8 not found\n";
    exit;
}

// Get user 1 (officer)
$user = User::find(1);

if (!$user) {
    echo "❌ User #1 not found\n";
    exit;
}

// Create new response
$response = Response::create([
    'questionnaire_id' => 8,
    'resident_id' => 24,  // BILLY WASOM from Family 1 (updated ID)
    'status' => 'in_progress',
    'entered_by_user_id' => 1,
    'entry_method' => 'officer',
]);

echo "✅ Response created successfully!\n\n";
echo "Response Details:\n";
echo "  - ID: {$response->id}\n";
echo "  - Questionnaire ID: {$response->questionnaire_id}\n";
echo "  - Resident ID: {$response->resident_id}\n";
echo "  - Status: {$response->status}\n";
echo "  - Entry Method: {$response->entry_method}\n";
echo "  - Created: {$response->created_at}\n\n";

echo "Sekarang buka halaman officer entry dan coba isi questionnaire ID #{$response->id}\n";
