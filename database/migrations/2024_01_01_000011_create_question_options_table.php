<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('option_text');
            $table->enum('media_type', ['none', 'image', 'video'])->default('none');
            $table->string('media_path')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('question_id');
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
