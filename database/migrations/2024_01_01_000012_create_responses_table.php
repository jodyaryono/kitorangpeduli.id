<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained()->onDelete('cascade');
            $table->foreignId('respondent_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['draft', 'in_progress', 'completed'])->default('draft');
            $table->foreignId('last_question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->decimal('progress_percentage', 5, 2)->default(0);

            // GPS saat mengisi kuesioner
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Device info
            $table->string('device_info')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('questionnaire_id');
            $table->index('respondent_id');
            $table->index('status');
            $table->unique(['questionnaire_id', 'respondent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
