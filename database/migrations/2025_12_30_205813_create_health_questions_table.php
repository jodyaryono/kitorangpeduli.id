<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Struktur pertanyaan kesehatan yang FLEKSIBEL:
     * - Pertanyaan bisa ditambah/diubah/dihapus dari database
     * - Mendukung kondisional (berdasarkan umur, jenis kelamin, jawaban sebelumnya)
     * - Mendukung berbagai tipe input (radio, checkbox, number, text, table)
     */
    public function up(): void
    {
        // Tabel utama untuk kategori/section pertanyaan
        Schema::create('health_question_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();  // ptm, hamil, melahirkan, bayi, balita, remaja, lansia
            $table->string('name');  // "Penyakit Menular & Tidak Menular"
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('target_criteria')->nullable();  // {"min_age": 0, "max_age": 999, "gender": "all"}
            $table->timestamps();
        });

        // Tabel pertanyaan kesehatan
        Schema::create('health_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('health_question_categories')->onDelete('cascade');
            $table->string('code', 50)->unique();  // ptm_1, hamil_1, bayi_1, etc
            $table->text('question_text');  // Teks pertanyaan
            $table->text('question_note')->nullable();  // Catatan/keterangan (italic)
            $table->string('input_type', 30);  // radio, checkbox, number, text, textarea, date, table
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);

            // Kondisi tampil
            $table->json('show_conditions')->nullable();
            // {"min_age": 15, "max_age": 60, "gender": "P", "depends_on": "ptm_1", "depends_value": "1"}

            // Validasi & setting
            $table->json('validation_rules')->nullable();  // {"min": 0, "max": 500, "unit": "mg/dl"}
            $table->json('settings')->nullable();  // {"reference_table": true, "multiple": true}

            $table->timestamps();

            $table->index(['category_id', 'order']);
        });

        // Tabel opsi jawaban untuk radio/checkbox
        Schema::create('health_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('health_questions')->onDelete('cascade');
            $table->string('value', 50);  // 1, 2, a, b, etc
            $table->string('label');  // "Ya", "Tidak", "Ada", dll
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();  // {"triggers_followup": "ptm_2", "is_other": true}
            $table->timestamps();

            $table->index(['question_id', 'order']);
        });

        // Tabel untuk sub-pertanyaan dalam format tabel (seperti hasil pemeriksaan darah)
        Schema::create('health_question_table_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('health_questions')->onDelete('cascade');
            $table->string('row_code', 50);  // gula_darah, asam_urat, kolestrol
            $table->string('row_label');  // "Gula Darah", "Asam Urat"
            $table->string('input_type', 30)->default('number');  // number, text, select
            $table->string('unit')->nullable();  // mg/dl, g/dl
            $table->string('reference_value')->nullable();  // "70-100 mg/dl"
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['question_id', 'order']);
        });

        // Update tabel resident_health_responses untuk mendukung struktur baru
        // (jika belum ada kolom)
        if (Schema::hasTable('resident_health_responses')) {
            Schema::table('resident_health_responses', function (Blueprint $table) {
                if (!Schema::hasColumn('resident_health_responses', 'question_id')) {
                    $table
                        ->foreignId('question_id')
                        ->nullable()
                        ->after('question_code')
                        ->constrained('health_questions')
                        ->nullOnDelete();
                }
                if (!Schema::hasColumn('resident_health_responses', 'table_row_code')) {
                    $table->string('table_row_code', 50)->nullable()->after('answer');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('resident_health_responses')) {
            Schema::table('resident_health_responses', function (Blueprint $table) {
                if (Schema::hasColumn('resident_health_responses', 'question_id')) {
                    $table->dropForeign(['question_id']);
                    $table->dropColumn('question_id');
                }
                if (Schema::hasColumn('resident_health_responses', 'table_row_code')) {
                    $table->dropColumn('table_row_code');
                }
            });
        }

        Schema::dropIfExists('health_question_table_rows');
        Schema::dropIfExists('health_question_options');
        Schema::dropIfExists('health_questions');
        Schema::dropIfExists('health_question_categories');
    }
};
