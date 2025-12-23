<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE questions DROP CONSTRAINT IF EXISTS questions_question_type_check');

        DB::statement("
            ALTER TABLE questions
            ADD CONSTRAINT questions_question_type_check
            CHECK (question_type IN (
                'text', 'textarea', 'single_choice', 'multiple_choice', 'dropdown',
                'scale', 'date', 'file', 'image', 'video', 'location',
                'province', 'regency', 'district', 'village',
                'puskesmas', 'field_officer', 'lookup', 'family_members', 'health_per_member'
            ))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE questions DROP CONSTRAINT IF EXISTS questions_question_type_check');

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
};
