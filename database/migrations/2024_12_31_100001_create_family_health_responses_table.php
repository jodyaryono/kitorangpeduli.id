<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Tabel untuk menyimpan jawaban kuesioner kesehatan per-KELUARGA (Section VI)
     */
    public function up(): void
    {
        Schema::create('family_health_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->constrained('families')->onDelete('cascade');
            $table->foreignId('response_id')->constrained('responses')->onDelete('cascade');
            $table->string('question_code', 50);  // e.g., 'ptm_1', 'srq_1', 'bayi_1', etc.
            $table->text('answer')->nullable();  // Store answer (can be JSON for complex answers)
            $table->timestamps();

            // Ensure unique combination per family per questionnaire response
            $table->unique(['family_id', 'response_id', 'question_code'], 'family_health_response_unique');

            // Index for faster lookups
            $table->index(['response_id', 'question_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_health_responses');
    }
};
