<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Drop existing constraint
DB::statement('ALTER TABLE questions DROP CONSTRAINT IF EXISTS questions_question_type_check');

// Add new constraint with field_officer
DB::statement("
    ALTER TABLE questions
    ADD CONSTRAINT questions_question_type_check
    CHECK (question_type IN (
        'text', 'textarea', 'single_choice', 'multiple_choice',
        'dropdown', 'scale', 'date', 'file', 'image', 'video', 'location',
        'province', 'regency', 'district', 'village', 'puskesmas', 'field_officer', 'lookup'
    ))
");

echo "✅ Constraint updated successfully with field_officer type!\n";
