<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Question;

$q = Question::find(265);

echo "=== Question 265 Details ===\n";
echo "ID: {$q->id}\n";
echo "Type: {$q->question_type}\n";
echo "Text: {$q->question_text}\n";
echo "Parent Section ID: {$q->parent_section_id}\n";
echo "Order: {$q->order}\n";
echo 'Is Section: ' . ($q->is_section ? 'Yes' : 'No') . "\n";

echo "\n";

// Check parent section
$parent = Question::find($q->parent_section_id);
if ($parent) {
    echo "Parent Section: {$parent->question_text}\n";
}
