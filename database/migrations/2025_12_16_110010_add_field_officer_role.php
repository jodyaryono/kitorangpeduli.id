<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ensure role column exists; repository already added role earlier.
            // If role is enum in application logic, we keep it as string and use validation.
            // No schema change needed if 'role' already exists; we optionally set default.
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 50)->default('viewer')->after('email');
            }
        });
    }

    public function down(): void
    {
        // Do not drop role column; other roles depend on it.
    }
};
