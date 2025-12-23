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
        Schema::table('responses', function (Blueprint $table) {
            $table->foreignId('updated_by_user_id')->nullable()->after('completed_at')->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by_user_id')->nullable()->after('updated_by_user_id')->constrained('users')->nullOnDelete();
            $table->softDeletes()->after('deleted_by_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['updated_by_user_id']);
            $table->dropForeign(['deleted_by_user_id']);
            $table->dropColumn(['updated_by_user_id', 'deleted_by_user_id']);
        });
    }
};
