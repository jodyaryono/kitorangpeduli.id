<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->json('selected_options')->nullable();  // For multiple choice
            $table->string('media_path')->nullable();  // For file/image/video answers
            $table->decimal('latitude', 10, 8)->nullable();  // For location answers
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->index('response_id');
            $table->index('question_id');
            $table->unique(['response_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
