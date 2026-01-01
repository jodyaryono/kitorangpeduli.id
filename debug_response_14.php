<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Response;
use App\Models\Family;
use App\Models\Resident;
use App\Models\Answer;

echo "=== Debug Response 14 ===\n\n";

$response = Response::find(14);
if (!$response) {
    echo "Response 14 not found!\n";
    exit;
}

echo "Response ID: {$response->id}\n";
echo "Resident ID: " . ($response->resident_id ?? 'NULL') . "\n";
echo "User ID: " . ($response->user_id ?? 'NULL') . "\n";

// Check answers
$noKkAnswer = Answer::where('response_id', 14)
    ->whereIn('question_id', [268, 223])
    ->whereNotNull('answer_text')
    ->first();
    
echo "No KK from answer: " . ($noKkAnswer ? $noKkAnswer->answer_text : 'NOT FOUND') . "\n";

// Check if family exists with this no_kk
if ($noKkAnswer) {
    $family = Family::where('no_kk', $noKkAnswer->answer_text)->first();
    echo "Family with this no_kk: " . ($family ? "ID: {$family->id}" : 'NOT FOUND') . "\n";
}

// Check all families
echo "\n=== All Families ===\n";
$families = Family::all();
foreach ($families as $f) {
    echo "ID: {$f->id}, No KK: {$f->no_kk}, KK Image: " . ($f->kk_image_path ?? 'NULL') . "\n";
}

// Check residents linked to families
echo "\n=== Residents with family_id ===\n";
$residents = Resident::whereNotNull('family_id')->get();
foreach ($residents as $r) {
    echo "ID: {$r->id}, Name: {$r->nama_lengkap}, Family ID: {$r->family_id}\n";
}
