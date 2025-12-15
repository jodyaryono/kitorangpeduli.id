<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kartu_keluarga', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 16)->unique();
            $table->string('kepala_keluarga', 100);
            $table->text('alamat');
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('kode_pos', 5)->nullable();

            // Wilayah KK
            $table->char('province_id', 2)->nullable();
            $table->char('regency_id', 4)->nullable();
            $table->char('district_id', 7)->nullable();
            $table->unsignedBigInteger('village_id')->nullable();

            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('regency_id')->references('id')->on('regencies')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('village_id')->references('id')->on('villages')->nullOnDelete();

            // Dokumen
            $table->string('kk_image_path')->nullable();

            // Verifikasi
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index('no_kk');
            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_keluarga');
    }
};
