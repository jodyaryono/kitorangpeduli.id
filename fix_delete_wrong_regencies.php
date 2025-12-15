<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DANGER: This will delete regencies without code ===\n";
echo "Checking regencies without code...\n\n";

$no_code = DB::table('regencies')->whereNull('code')->orWhere('code', '')->get();
echo "Found " . $no_code->count() . " regencies without code:\n";
foreach ($no_code->take(20) as $r) {
    echo sprintf("ID:%s Name:%s Province_ID:%s\n", $r->id, $r->name, $r->province_id);
}

if ($no_code->count() > 0) {
    echo "\nDo you want to delete these? Type 'YES' to confirm: ";
    // For automated script, we'll check if they're the problematic ones
    
    $problematic_ids = [3401, 3402, 3403, 3404, 3471]; // Yogyakarta IDs in wrong province
    $to_delete = DB::table('regencies')->whereIn('id', $problematic_ids)->get();
    
    echo "\n\nDeleting " . $to_delete->count() . " problematic regencies:\n";
    foreach ($to_delete as $r) {
        echo sprintf("Deleting: ID:%s Name:%s\n", $r->id, $r->name);
    }
    
    $deleted = DB::table('regencies')->whereIn('id', $problematic_ids)->delete();
    echo "\nDeleted: $deleted regencies\n";
}

echo "\n=== Verification ===\n";
echo "Papua Barat regencies now:\n";
$pb_now = DB::table('regencies')->where('province_id', 34)->get();
echo "Count: " . $pb_now->count() . "\n";
