<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->index(['questionnaire_id', 'status'], 'idx_responses_questionnaire_status');
            $table->index('respondent_id', 'idx_responses_respondent');
            $table->index('status', 'idx_responses_status');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->index('response_id', 'idx_answers_response');
            $table->index('question_id', 'idx_answers_question');
            $table->index('selected_option_id', 'idx_answers_option');
        });

        Schema::table('questionnaires', function (Blueprint $table) {
            $table->index('opd_id', 'idx_questionnaires_opd');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index(['questionnaire_id', 'order'], 'idx_questions_questionnaire_order');
        });

        Schema::table('respondents', function (Blueprint $table) {
            $table->index('jenis_kelamin', 'idx_respondents_gender');
            $table->index('village_id', 'idx_respondents_village');
            $table->index('district_id', 'idx_respondents_district');
            $table->index('citizen_type_id', 'idx_respondents_citizen_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex('idx_responses_questionnaire_status');
            $table->dropIndex('idx_responses_respondent');
            $table->dropIndex('idx_responses_status');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex('idx_answers_response');
            $table->dropIndex('idx_answers_question');
            $table->dropIndex('idx_answers_option');
        });

        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropIndex('idx_questionnaires_opd');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_questionnaire_order');
        });

        Schema::table('respondents', function (Blueprint $table) {
            $table->dropIndex('idx_respondents_gender');
            $table->dropIndex('idx_respondents_village');
            $table->dropIndex('idx_respondents_district');
            $table->dropIndex('idx_respondents_citizen_type');
        });
    }
};
