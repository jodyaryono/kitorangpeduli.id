<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Family;
use App\Models\Resident;
use App\Models\Answer;

echo "=== Latest Family KK Image Path ===\n";
$family = Family::latest()->first();
if ($family) {
    echo "Family ID: {$family->id}\n";
    echo "No KK: {$family->no_kk}\n";
    echo "KK Image Path: " . ($family->kk_image_path ?? 'NULL') . "\n";
}

echo "\n=== Latest Residents with KTP ===\n";
$residents = Resident::whereNotNull('ktp_image_path')->latest()->take(5)->get();
foreach ($residents as $r) {
    echo "ID: {$r->id}, Name: {$r->nama_lengkap}, KTP Path: {$r->ktp_image_path}\n";
}

echo "\n=== Check File Answers with media_path ===\n";
$answers = Answer::whereNotNull('media_path')->latest()->take(5)->get();
foreach ($answers as $a) {
    echo "Answer ID: {$a->id}, Question: {$a->question_id}, Path: {$a->media_path}\n";
}

echo "\n=== Check storage symlink ===\n";
$storagePath = storage_path('app/public');
$publicStorage = public_path('storage');
echo "Storage path exists: " . (file_exists($storagePath) ? 'YES' : 'NO') . "\n";
echo "Public storage symlink: " . (is_link($publicStorage) ? 'YES (symlink)' : (file_exists($publicStorage) ? 'YES (folder)' : 'NO')) . "\n";

if (is_link($publicStorage)) {
    echo "Symlink target: " . readlink($publicStorage) . "\n";
}

echo "\n=== Done ===\n";
