<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop old constraint
        DB::statement('ALTER TABLE questions DROP CONSTRAINT IF EXISTS questions_question_type_check');

        // Add new constraint including family_members
        DB::statement("
            ALTER TABLE questions
            ADD CONSTRAINT questions_question_type_check
            CHECK (question_type IN (
                'text', 'textarea', 'single_choice', 'multiple_choice', 'dropdown',
                'scale', 'date', 'file', 'image', 'video', 'location',
                'province', 'regency', 'district', 'village',
                'puskesmas', 'field_officer', 'lookup', 'family_members'
            ))
        ");
    }

    public function down(): void
    {
        // Drop constraint
        DB::statement('ALTER TABLE questions DROP CONSTRAINT IF EXISTS questions_question_type_check');

        // Restore old constraint without family_members
        DB::statement("
            ALTER TABLE questions
            ADD CONSTRAINT questions_question_type_check
            CHECK (question_type IN (
                'text', 'textarea', 'single_choice', 'multiple_choice', 'dropdown',
                'scale', 'date', 'file', 'image', 'video', 'location',
                'province', 'regency', 'district', 'village',
                'puskesmas', 'field_officer', 'lookup'
            ))
        ");
    }
};
