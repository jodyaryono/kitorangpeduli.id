<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing constraint
        DB::statement('ALTER TABLE questions DROP CONSTRAINT IF EXISTS questions_question_type_check');

        // Add new constraint with additional types
        DB::statement("
            ALTER TABLE questions
            ADD CONSTRAINT questions_question_type_check
            CHECK (question_type IN (
                'text', 'textarea', 'single_choice', 'multiple_choice',
                'dropdown', 'scale', 'date', 'file', 'image', 'video', 'location',
                'province', 'regency', 'district', 'village', 'puskesmas', 'field_officer', 'lookup'
            ))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new constraint
        DB::statement('ALTER TABLE questions DROP CONSTRAINT IF EXISTS questions_question_type_check');

        // Restore original constraint
        DB::statement("
            ALTER TABLE questions
            ADD CONSTRAINT questions_question_type_check
            CHECK (question_type IN (
                'text', 'textarea', 'single_choice', 'multiple_choice',
                'dropdown', 'scale', 'date', 'file', 'image', 'video', 'location'
            ))
        ");
    }
};
