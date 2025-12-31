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
        Schema::create('resident_health_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('residents')->onDelete('cascade');
            $table->foreignId('response_id')->constrained('responses')->onDelete('cascade');
            $table->string('question_code', 50);  // jkn, merokok, jamban, air_bersih, tb_paru, etc.
            $table->string('answer', 255)->nullable();  // 1, 2, or text value for sistolik/diastolik
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['resident_id', 'question_code']);
            $table->index(['response_id', 'question_code']);
            $table->index('question_code');

            // Unique constraint - one answer per resident per question per response
            $table->unique(['resident_id', 'response_id', 'question_code'], 'unique_resident_response_question');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resident_health_responses');
    }
};
