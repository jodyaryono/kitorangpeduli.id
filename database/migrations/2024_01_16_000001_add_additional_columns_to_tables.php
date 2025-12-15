<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            if (!Schema::hasColumn('respondents', 'status_hubungan')) {
                $table->string('status_hubungan', 30)->nullable()->after('kartu_keluarga_id');
            }
            if (!Schema::hasColumn('respondents', 'pendidikan')) {
                $table->string('pendidikan', 50)->nullable()->after('pekerjaan');
            }
            if (!Schema::hasColumn('respondents', 'email')) {
                $table->string('email', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('respondents', 'selfie_ktp_path')) {
                $table->string('selfie_ktp_path')->nullable()->after('ktp_image_path');
            }
            if (!Schema::hasColumn('respondents', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('rejection_reason');
            }
        });

        Schema::table('kartu_keluarga', function (Blueprint $table) {
            if (!Schema::hasColumn('kartu_keluarga', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('rejection_reason');
            }
        });

        Schema::table('responses', function (Blueprint $table) {
            if (!Schema::hasColumn('responses', 'is_valid')) {
                $table->boolean('is_valid')->nullable()->after('status');
            }
            if (!Schema::hasColumn('responses', 'validation_notes')) {
                $table->text('validation_notes')->nullable()->after('is_valid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            $table->dropColumn(['status_hubungan', 'pendidikan', 'email', 'selfie_ktp_path', 'verification_notes']);
        });

        Schema::table('kartu_keluarga', function (Blueprint $table) {
            $table->dropColumn(['verification_notes']);
        });

        Schema::table('responses', function (Blueprint $table) {
            $table->dropColumn(['is_valid', 'validation_notes']);
        });
    }
};
