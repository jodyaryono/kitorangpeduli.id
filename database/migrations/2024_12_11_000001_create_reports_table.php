<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('opd_id')->constrained()->onDelete('cascade');
            $table->foreignId('questionnaire_id')->constrained()->onDelete('cascade');

            $table->string('title')->nullable();
            $table->text('user_prompt');
            $table->text('ai_response')->nullable();

            $table->json('raw_data')->nullable();
            $table->json('chart_data')->nullable();
            $table->json('map_data')->nullable();

            $table->string('input_type')->default('text');
            $table->text('voice_transcript')->nullable();

            $table->integer('api_tokens_used')->default(0);
            $table->decimal('api_cost', 10, 4)->default(0);

            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();

            $table->timestamps();
            $table->index('opd_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
