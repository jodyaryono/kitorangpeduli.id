<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('respondents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kartu_keluarga_id')->nullable()->constrained('kartu_keluarga')->nullOnDelete();
            $table->foreignId('citizen_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nik', 16)->unique();

            // Data Pribadi KTP
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('golongan_darah', 3)->nullable();
            $table->string('agama', 20)->nullable();
            $table->string('status_perkawinan', 20)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('kewarganegaraan', 3)->default('WNI');

            // Alamat KTP
            $table->text('alamat')->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();

            // Wilayah Alamat KTP
            $table->char('province_id', 2)->nullable();
            $table->char('regency_id', 4)->nullable();
            $table->char('district_id', 7)->nullable();
            $table->unsignedBigInteger('village_id')->nullable();

            $table->foreign('province_id')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('regency_id')->references('id')->on('regencies')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('village_id')->references('id')->on('villages')->nullOnDelete();

            // GPS Lokasi
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Kontak & Auth
            $table->string('phone', 15)->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            // Dokumen
            $table->string('ktp_image_path')->nullable();

            // Verifikasi
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index('nik');
            $table->index('phone');
            $table->index('verification_status');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respondents');
    }
};
