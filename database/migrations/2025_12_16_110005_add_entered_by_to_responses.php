<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table
                ->foreignId('entered_by_user_id')
                ->nullable()
                ->after('respondent_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->index('entered_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropIndex(['entered_by_user_id']);
            $table->dropConstrainedForeignId('entered_by_user_id');
        });
    }
};
