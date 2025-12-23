<?php

namespace Database\Seeders;

use App\Models\Opd;
use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Database\Seeder;

class HealthFamilyQuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find or create OPD Kesehatan
        $opdKesehatan = Opd::firstOrCreate(
            ['name' => 'Dinas Kesehatan'],
            ['description' => 'Dinas Kesehatan Papua']
        );

        // Check if questionnaire already exists
        $existingQuestionnaire = Questionnaire::where('title', 'Data Keluarga dan Anggota Keluarga Sehat')
            ->where('opd_id', $opdKesehatan->id)
            ->first();

        if ($existingQuestionnaire) {
            $this->command->info('⚠️  Questionnaire already exists. Skipping...');
            return;
        }

        // Create the questionnaire
        $questionnaire = Questionnaire::create([
            'opd_id' => $opdKesehatan->id,
            'title' => 'Data Keluarga dan Anggota Keluarga Sehat',
            'description' => 'Formulir pendataan kesehatan keluarga dan anggota keluarga berdasarkan standar Kementerian Kesehatan RI',
            'target_type' => 'family',
            'visibility' => 'officer_assisted',
            'is_active' => true,
        ]);

        // SECTION I: PENGENALAN TEMPAT (Location)
        $sectionI = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'I. PENGENALAN TEMPAT',
            'question_type' => 'text',
            'is_section' => true,
            'order' => 1,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        $provinceQuestion = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionI->id,
            'question_text' => 'Provinsi',
            'question_type' => 'province',
            'order' => 2,
            'is_required' => true,
            'applies_to' => 'family',
        ]);

        $regencyQuestion = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionI->id,
            'question_text' => 'Kabupaten/Kota',
            'question_type' => 'regency',
            'order' => 3,
            'is_required' => true,
            'applies_to' => 'family',
            'settings' => ['cascades_from_question_id' => $provinceQuestion->id],
        ]);

        $districtQuestion = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionI->id,
            'question_text' => 'Kecamatan',
            'question_type' => 'district',
            'order' => 4,
            'is_required' => true,
            'applies_to' => 'family',
            'settings' => ['cascades_from_question_id' => $regencyQuestion->id],
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionI->id,
            'question_text' => 'Desa/Kelurahan',
            'question_type' => 'village',
            'order' => 5,
            'is_required' => true,
            'applies_to' => 'family',
            'settings' => ['cascades_from_question_id' => $districtQuestion->id],
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionI->id,
            'question_text' => 'Nama Puskesmas',
            'question_type' => 'puskesmas',
            'order' => 6,
            'is_required' => true,
            'applies_to' => 'family',
            'settings' => ['lookup_model' => 'Puskesmas'],
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionI->id,
            'question_text' => 'RW',
            'question_type' => 'text',
            'order' => 7,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionI->id,
            'question_text' => 'RT',
            'question_type' => 'text',
            'order' => 8,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionI->id,
            'question_text' => 'No. Bangunan',
            'question_type' => 'text',
            'order' => 9,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        // SECTION II: KETERANGAN KELUARGA (Family Information)
        $sectionII = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'II. KETERANGAN KELUARGA',
            'question_type' => 'text',
            'is_section' => true,
            'order' => 10,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionII->id,
            'question_text' => 'Nomor Kartu Keluarga (KK)',
            'question_type' => 'text',
            'order' => 11,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionII->id,
            'question_text' => 'Upload Kartu Keluarga (KK)',
            'question_type' => 'file',
            'order' => 12,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'parent_section_id' => $sectionII->id,
            'question_text' => 'Alamat',
            'question_type' => 'textarea',
            'order' => 13,
            'is_required' => true,
            'applies_to' => 'family',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Kode Pos',
            'question_type' => 'text',
            'order' => 14,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        $questionR205 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah sumber air minum utama yang digunakan keluarga adalah air perpipaan?',
            'question_type' => 'single_choice',
            'order' => 15,
            'is_required' => true,
            'applies_to' => 'family',
        ]);

        $questionR205->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        $questionR206 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah keluarga memiliki dan menggunakan jamban keluarga?',
            'question_type' => 'single_choice',
            'order' => 16,
            'is_required' => true,
            'applies_to' => 'family',
        ]);

        $questionR206->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        $questionR207 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah keluarga menjadi peserta Jaminan Kesehatan Nasional (JKN)?',
            'question_type' => 'single_choice',
            'order' => 17,
            'is_required' => true,
            'applies_to' => 'family',
        ]);

        $questionR207->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        $questionR208 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah keluarga sudah melakukan Pengelolaan Sampah dengan baik?',
            'question_type' => 'single_choice',
            'order' => 18,
            'is_required' => true,
            'applies_to' => 'family',
        ]);

        $questionR208->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        $questionR209 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah keluarga sudah menerapkan Gerakan Masyarakat Hidup Sehat (GERMAS)?',
            'question_type' => 'single_choice',
            'order' => 19,
            'is_required' => true,
            'applies_to' => 'family',
        ]);

        $questionR209->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        // SECTION III: KETERANGAN PENCACAH (Data Collector Information)
        $sectionIII = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'III. KETERANGAN PENCACAH',
            'question_type' => 'text',
            'order' => 20,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Nama Pencacah',
            'question_type' => 'field_officer',
            'order' => 21,
            'is_required' => true,
            'applies_to' => 'family',
            'settings' => ['lookup_model' => 'User'],
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Tanggal Pendataan',
            'question_type' => 'date',
            'order' => 22,
            'is_required' => true,
            'applies_to' => 'family',
        ]);

        // SECTION IV: DAFTAR ANGGOTA KELUARGA (Family Members List)
        $sectionIV = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'IV. DAFTAR ANGGOTA KELUARGA',
            'question_type' => 'text',
            'order' => 23,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Daftar nama anggota keluarga akan diisi per individu pada bagian berikut',
            'question_type' => 'text',
            'order' => 24,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        // SECTION V: KETERANGAN ANGGOTA KELUARGA (Individual Member Details - REPEATABLE)
        $sectionV = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'V. KETERANGAN ANGGOTA KELUARGA',
            'question_type' => 'text',
            'order' => 25,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        // V.A. Identitas
        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'V.A. IDENTITAS',
            'question_type' => 'text',
            'order' => 26,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Nama Lengkap',
            'question_type' => 'text',
            'order' => 27,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'NIK (Nomor Induk Kependudukan)',
            'question_type' => 'text',
            'order' => 28,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR503 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Jenis Kelamin',
            'question_type' => 'single_choice',
            'order' => 29,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR503->options()->createMany([
            ['option_text' => 'Laki-laki', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Perempuan', 'option_value' => '2', 'order' => 2],
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Tanggal Lahir',
            'question_type' => 'date',
            'order' => 30,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Umur (tahun)',
            'question_type' => 'text',
            'order' => 31,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR506 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Status Perkawinan',
            'question_type' => 'single_choice',
            'order' => 32,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR506->options()->createMany([
            ['option_text' => 'Belum Kawin', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Kawin', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Cerai Hidup', 'option_value' => '3', 'order' => 3],
            ['option_text' => 'Cerai Mati', 'option_value' => '4', 'order' => 4],
        ]);

        $questionR507 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Hubungan dengan Kepala Keluarga',
            'question_type' => 'single_choice',
            'order' => 33,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR507->options()->createMany([
            ['option_text' => 'Kepala Keluarga', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Istri/Suami', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Anak', 'option_value' => '3', 'order' => 3],
            ['option_text' => 'Menantu', 'option_value' => '4', 'order' => 4],
            ['option_text' => 'Cucu', 'option_value' => '5', 'order' => 5],
            ['option_text' => 'Orang Tua', 'option_value' => '6', 'order' => 6],
            ['option_text' => 'Mertua', 'option_value' => '7', 'order' => 7],
            ['option_text' => 'Famili Lain', 'option_value' => '8', 'order' => 8],
            ['option_text' => 'Pembantu', 'option_value' => '9', 'order' => 9],
            ['option_text' => 'Lainnya', 'option_value' => '10', 'order' => 10],
        ]);

        $questionR508 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Pendidikan Terakhir',
            'question_type' => 'single_choice',
            'order' => 34,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR508->options()->createMany([
            ['option_text' => 'Tidak/Belum Sekolah', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak Tamat SD/MI', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Tamat SD/MI', 'option_value' => '3', 'order' => 3],
            ['option_text' => 'SLTP/MTs', 'option_value' => '4', 'order' => 4],
            ['option_text' => 'SLTA/MA', 'option_value' => '5', 'order' => 5],
            ['option_text' => 'Diploma I/II', 'option_value' => '6', 'order' => 6],
            ['option_text' => 'Akademi/Diploma III/Sarjana Muda', 'option_value' => '7', 'order' => 7],
            ['option_text' => 'Diploma IV/Strata I', 'option_value' => '8', 'order' => 8],
            ['option_text' => 'Strata II', 'option_value' => '9', 'order' => 9],
            ['option_text' => 'Strata III', 'option_value' => '10', 'order' => 10],
        ]);

        $questionR509 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Pekerjaan',
            'question_type' => 'single_choice',
            'order' => 35,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR509->options()->createMany([
            ['option_text' => 'Tidak Bekerja', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Mengurus Rumah Tangga', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Pelajar/Mahasiswa', 'option_value' => '3', 'order' => 3],
            ['option_text' => 'Pensiunan', 'option_value' => '4', 'order' => 4],
            ['option_text' => 'PNS', 'option_value' => '5', 'order' => 5],
            ['option_text' => 'TNI/POLRI', 'option_value' => '6', 'order' => 6],
            ['option_text' => 'Pegawai Swasta', 'option_value' => '7', 'order' => 7],
            ['option_text' => 'Wiraswasta', 'option_value' => '8', 'order' => 8],
            ['option_text' => 'Petani', 'option_value' => '9', 'order' => 9],
            ['option_text' => 'Nelayan', 'option_value' => '10', 'order' => 10],
            ['option_text' => 'Buruh', 'option_value' => '11', 'order' => 11],
            ['option_text' => 'Lainnya', 'option_value' => '99', 'order' => 12],
        ]);

        // V.B. Status Kesehatan
        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'V.B. STATUS KESEHATAN',
            'question_type' => 'text',
            'order' => 36,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR510 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah memiliki Kartu BPJS/JKN?',
            'question_type' => 'single_choice',
            'order' => 37,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR510->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Nomor Kartu BPJS/JKN',
            'question_type' => 'text',
            'order' => 38,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR512 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah menderita penyakit kronis/menahun?',
            'question_type' => 'single_choice',
            'order' => 39,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR512->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Jika Ya, sebutkan jenis penyakitnya',
            'question_type' => 'textarea',
            'order' => 40,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR514 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah memiliki disabilitas?',
            'question_type' => 'single_choice',
            'order' => 41,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR514->options()->createMany([
            ['option_text' => 'Tidak', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Ya, Fisik', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Ya, Mental', 'option_value' => '3', 'order' => 3],
            ['option_text' => 'Ya, Fisik dan Mental', 'option_value' => '4', 'order' => 4],
        ]);

        $questionR515 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah merokok?',
            'question_type' => 'single_choice',
            'order' => 42,
            'is_required' => true,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR515->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Mantan Perokok', 'option_value' => '3', 'order' => 3],
        ]);

        // V.C. Khusus untuk Ibu Hamil
        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'V.C. KHUSUS UNTUK IBU HAMIL',
            'question_type' => 'text',
            'order' => 43,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR516 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah sedang hamil?',
            'question_type' => 'single_choice',
            'order' => 44,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR516->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Usia Kehamilan (bulan)',
            'question_type' => 'text',
            'order' => 45,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR518 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah rutin memeriksakan kehamilan?',
            'question_type' => 'single_choice',
            'order' => 46,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR518->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        // V.D. Khusus untuk Balita (Anak Usia <5 tahun)
        Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'V.D. KHUSUS UNTUK BALITA (ANAK USIA <5 TAHUN)',
            'question_type' => 'text',
            'order' => 47,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR519 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah memiliki Buku KIA (Kesehatan Ibu dan Anak)?',
            'question_type' => 'single_choice',
            'order' => 48,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR519->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        $questionR520 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah mendapat ASI eksklusif (0-6 bulan)?',
            'question_type' => 'single_choice',
            'order' => 49,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR520->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Tidak Berlaku', 'option_value' => '3', 'order' => 3],
        ]);

        $questionR521 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah memiliki imunisasi lengkap?',
            'question_type' => 'single_choice',
            'order' => 50,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR521->options()->createMany([
            ['option_text' => 'Ya, Lengkap', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak Lengkap', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Tidak Tahu', 'option_value' => '3', 'order' => 3],
        ]);

        $questionR522 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Apakah rutin ditimbang di Posyandu?',
            'question_type' => 'single_choice',
            'order' => 51,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR522->options()->createMany([
            ['option_text' => 'Ya', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Tidak', 'option_value' => '2', 'order' => 2],
        ]);

        $questionR523 = Question::create([
            'questionnaire_id' => $questionnaire->id,
            'question_text' => 'Status Gizi',
            'question_type' => 'single_choice',
            'order' => 52,
            'is_required' => false,
            'is_repeatable' => true,
            'applies_to' => 'individual',
        ]);

        $questionR523->options()->createMany([
            ['option_text' => 'Baik', 'option_value' => '1', 'order' => 1],
            ['option_text' => 'Kurang', 'option_value' => '2', 'order' => 2],
            ['option_text' => 'Buruk', 'option_value' => '3', 'order' => 3],
            ['option_text' => 'Tidak Tahu', 'option_value' => '4', 'order' => 4],
        ]);

        $this->command->info('✅ Questionnaire "Data Keluarga dan Anggota Keluarga Sehat" created successfully!');
        $this->command->info('📋 Total questions created: ' . $questionnaire->questions()->count());
        $this->command->info("🏥 OPD: {$opdKesehatan->name}");
    }
}
