<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table
                ->enum('visibility', ['self_entry', 'officer_assisted', 'both'])
                ->default('self_entry')
                ->after('is_active');
            $table->index('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropIndex(['visibility']);
            $table->dropColumn('visibility');
        });
    }
};
