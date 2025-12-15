<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', [
                'text',
                'textarea',
                'single_choice',
                'multiple_choice',
                'dropdown',
                'scale',
                'date',
                'file',
                'image',
                'video',
                'location'
            ])->default('text');
            $table->enum('media_type', ['none', 'image', 'video'])->default('none');
            $table->string('media_path')->nullable();
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->json('settings')->nullable();  // For scale min/max, validation rules, etc.
            $table->timestamps();

            $table->index('questionnaire_id');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
