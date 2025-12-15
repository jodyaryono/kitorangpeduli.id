<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('district_id', 7);
            $table->char('code', 10)->nullable()->unique();
            $table->string('name', 255);
            $table->timestamps();

            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->index('district_id');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
