<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\CitizenType;
use App\Models\District;
use App\Models\Opd;
use App\Models\Province;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\Regency;
use App\Models\Respondent;
use App\Models\Response;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    // Nama-nama Papua yang umum
    private array $firstNamesMale = [
        'Yohanes', 'Petrus', 'Yohanis', 'Markus', 'Lukas', 'Paulus', 'Musa',
        'Yusuf', 'Yakob', 'Samuel', 'Daniel', 'David', 'Kristian', 'Alfons',
        'Benyamin', 'Frans', 'Herman', 'Isak', 'Karel', 'Leonard', 'Melkias',
        'Natan', 'Oktovianus', 'Pilemon', 'Rafael', 'Simon', 'Titus', 'Viktor',
        'Welem', 'Yance', 'Zakharias', 'Abisai', 'Barnabas', 'Eliezer', 'Gerson',
        'Hendrik', 'Johanes', 'Kornelius', 'Lazarus', 'Matias', 'Nikolas', 'Obaja',
        'Ruben', 'Salmon', 'Timotius', 'Urbanus', 'Wens', 'Xaverius', 'Yulianus', 'Zakeus'
    ];

    private array $firstNamesFemale = [
        'Maria', 'Yuliana', 'Agustina', 'Bernadeta', 'Christina', 'Diana',
        'Elisabeth', 'Fransiska', 'Gloria', 'Helena', 'Irene', 'Joice',
        'Katerina', 'Lusia', 'Margareta', 'Naomi', 'Oktavina', 'Patricia',
        'Regina', 'Sarlota', 'Theresia', 'Ursula', 'Veronica', 'Wilhelmina',
        'Yohana', 'Zuliana', 'Anastasia', 'Beatrix', 'Cecilia', 'Dorothea',
        'Emilia', 'Felisia', 'Gratia', 'Herlina', 'Ivana', 'Julita',
        'Kristina', 'Lidya', 'Monika', 'Natalia', 'Olivia', 'Priska',
        'Rosalina', 'Stefania', 'Trifena', 'Ursulina', 'Veronika', 'Winda', 'Yosefina', 'Zefania'
    ];

    private array $lastNames = [
        'Wenda', 'Kogoya', 'Tabuni', 'Yoman', 'Numberi', 'Rumkabu', 'Daby',
        'Mandobar', 'Waromi', 'Karubaba', 'Wakum', 'Kareni', 'Iyai', 'Korobur',
        'Mansoben', 'Youwe', 'Boseren', 'Hanubun', 'Tebai', 'Ayomi', 'Beanal',
        'Cabey', 'Dimara', 'Enembe', 'Felle', 'Gebze', 'Heselo', 'Imbiri',
        'Jitmau', 'Krey', 'Lani', 'Mandowen', 'Numbay', 'Ohee', 'Pahabol',
        'Raweyai', 'Suebu', 'Theys', 'Udam', 'Wamang', 'Yoku', 'Zonggonau',
        'Manufandu', 'Patay', 'Mabel', 'Kaber', 'Reyaan', 'Wakerkwa', 'Yigibalom', 'Zebua'
    ];

    private array $pekerjaan = [
        'Petani', 'Nelayan', 'PNS', 'Guru', 'Pedagang', 'Buruh', 'Wiraswasta',
        'Ibu Rumah Tangga', 'Mahasiswa', 'Pelajar', 'Honorer', 'Sopir',
        'Tukang', 'Karyawan Swasta', 'TNI/Polri', 'Tenaga Kesehatan', 'Pengusaha',
        'Tidak Bekerja', 'Pensiunan', 'Dokter', 'Perawat', 'Bidan', 'Dosen'
    ];

    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->command->info('🚀 Starting Dummy Data Seeder...');

            // Get Kota Jayapura data
            $province = Province::where('code', '91')->first();
            $regency = Regency::where('code', '9171')->first();
            $districts = District::where('regency_id', $regency->id)->get();
            $villages = Village::whereIn('district_id', $districts->pluck('id'))->get();
            $citizenTypes = CitizenType::all();
            $opds = Opd::all();

            if ($districts->isEmpty() || $villages->isEmpty()) {
                $this->command->error('❌ Please run WilayahSeeder first!');
                return;
            }

            if ($citizenTypes->isEmpty()) {
                $this->command->error('❌ Please run CitizenTypeSeeder first!');
                return;
            }

            if ($opds->isEmpty()) {
                $this->command->error('❌ Please run OpdSeeder first!');
                return;
            }

            // 1. Create 200 Respondents
            $this->command->info('👥 Creating 200 Respondents...');
            $respondents = $this->createRespondents(200, $province, $regency, $districts, $villages, $citizenTypes);
            $this->command->info('✅ Created ' . count($respondents) . ' Respondents');

            // 2. Create 22 Questionnaires (2 per OPD)
            $this->command->info('📋 Creating 22 Questionnaires (2 per OPD)...');
            $questionnaires = $this->createQuestionnaires($opds);
            $this->command->info('✅ Created ' . count($questionnaires) . ' Questionnaires');

            // 3. Create Responses (some completed, some in progress)
            $this->command->info('📝 Creating Responses...');
            $this->createResponses($respondents, $questionnaires);
            $this->command->info('✅ Responses created');

            DB::commit();
            $this->command->info('🎉 Dummy Data Seeder completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createRespondents(int $count, $province, $regency, $districts, $villages, $citizenTypes): array
    {
        $respondents = [];
        $usedNiks = [];

        for ($i = 0; $i < $count; $i++) {
            $gender = fake()->randomElement(['L', 'P']);
            $firstName = $gender === 'L'
                ? fake()->randomElement($this->firstNamesMale)
                : fake()->randomElement($this->firstNamesFemale);
            $lastName = fake()->randomElement($this->lastNames);
            $namaLengkap = $firstName . ' ' . $lastName;

            // Random birthdate between 1960-2005
            $birthDate = fake()->dateTimeBetween('-65 years', '-20 years');
            $birthYear = (int) $birthDate->format('Y');
            $birthMonth = (int) $birthDate->format('m');
            $birthDay = (int) $birthDate->format('d');

            // Random village
            $village = $villages->random();
            $district = $districts->firstWhere('id', $village->district_id);
            $districtCode = substr($district->code, 0, 6);  // Take first 6 digits only

            // Generate NIK: districtCode (6) + DDMMYY (6) + random (4) = 16 digits
            $dayPart = $gender === 'P' ? $birthDay + 40 : $birthDay;
            $datePart = str_pad($dayPart, 2, '0', STR_PAD_LEFT)
                . str_pad($birthMonth, 2, '0', STR_PAD_LEFT)
                . substr($birthYear, -2);

            // Generate unique NIK
            do {
                $randomPart = str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);
                $nik = $districtCode . $datePart . $randomPart;
            } while (in_array($nik, $usedNiks) || strlen($nik) !== 16);
            $usedNiks[] = $nik;

            $respondent = Respondent::create([
                'citizen_type_id' => $citizenTypes->random()->id,
                'province_id' => $province->id,
                'regency_id' => $regency->id,
                'district_id' => $district->id,
                'village_id' => $village->id,
                'nik' => $nik,
                'nama_lengkap' => $namaLengkap,
                'tempat_lahir' => 'Jayapura',
                'tanggal_lahir' => $birthDate->format('Y-m-d'),
                'jenis_kelamin' => $gender,
                'golongan_darah' => fake()->randomElement(array_keys(Respondent::BLOOD_TYPES)),
                'agama' => fake()->randomElement(['Kristen', 'Katolik', 'Islam']),
                'status_perkawinan' => fake()->randomElement(array_keys(Respondent::MARITAL_STATUSES)),
                'status_hubungan' => fake()->randomElement(array_keys(Respondent::FAMILY_RELATIONS)),
                'pendidikan' => fake()->randomElement(array_keys(Respondent::EDUCATIONS)),
                'pekerjaan' => fake()->randomElement($this->pekerjaan),
                'kewarganegaraan' => 'WNI',
                'alamat' => 'Jl. ' . fake()->streetName() . ' No. ' . fake()->buildingNumber(),
                'rt' => str_pad(fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT),
                'rw' => str_pad(fake()->numberBetween(1, 10), 3, '0', STR_PAD_LEFT),
                'phone' => '08' . fake()->numberBetween(11, 99) . fake()->numberBetween(1000000, 9999999),
                'email' => strtolower($firstName) . '.' . strtolower($lastName) . fake()->numberBetween(1, 99) . '@gmail.com',
                // GPS dalam radius 5-10km dari pusat Jayapura (-2.5333, 140.7167)
                // 1 derajat ≈ 111km, jadi 5-10km ≈ 0.045-0.09 derajat
                'latitude' => $this->generateRandomCoordinate(-2.5333, 0.045, 0.09),
                'longitude' => $this->generateRandomCoordinate(140.7167, 0.045, 0.09),
                'verification_status' => fake()->randomElement(['pending', 'verified', 'verified', 'verified']),  // 75% verified
                'verification_notes' => null,
            ]);

            $respondents[] = $respondent;

            if (($i + 1) % 20 === 0) {
                $this->command->info("   Created {$i}/{$count} respondents...");
            }
        }

        return $respondents;
    }

    /**
     * Generate random coordinate within a radius from center point
     * @param float $center Center coordinate
     * @param float $minRadius Minimum radius in degrees
     * @param float $maxRadius Maximum radius in degrees
     * @return float
     */
    private function generateRandomCoordinate(float $center, float $minRadius, float $maxRadius): float
    {
        // Generate random angle
        $angle = fake()->randomFloat(2, 0, 2 * pi());

        // Generate random radius between min and max
        $radius = fake()->randomFloat(6, $minRadius, $maxRadius);

        // Calculate offset based on angle and radius
        $offset = $radius * cos($angle);

        return round($center + $offset, 6);
    }

    private function createQuestionnaires($opds): array
    {
        $questionnaires = [];

        // Load realistic questionnaire templates
        $questionnaireTemplates = require database_path('seeders/RealisticQuestionnaireTemplates.php');

        // Create 2 questionnaires per OPD (total 22)
        foreach ($opds as $opdIndex => $opd) {
            // Find templates for this OPD (should be 2 templates per OPD)
            $opdTemplates = array_filter($questionnaireTemplates, function ($template) use ($opdIndex) {
                return isset($template['opd_index']) && $template['opd_index'] === $opdIndex;
            });

            foreach ($opdTemplates as $template) {
                // Check if questionnaire already exists (prevent duplicates)
                $existing = Questionnaire::where('opd_id', $opd->id)
                    ->where('title', $template['title'])
                    ->first();

                if ($existing) {
                    $this->command->info("Skipping duplicate: {$template['title']}");
                    continue;
                }

                $questionnaire = Questionnaire::create([
                    'opd_id' => $opd->id,
                    'title' => $template['title'],
                    'description' => $template['description'],
                    'start_date' => Carbon::now()->subDays(30),
                    'end_date' => Carbon::now()->addDays(60),
                    'is_active' => true,
                    'requires_location' => fake()->boolean(70),
                    'requires_verified_respondent' => fake()->boolean(50),
                    'max_responses' => fake()->randomElement([null, 100, 200, 500]),
                ]);

                // Create questions for this questionnaire
                foreach ($template['questions'] as $order => $q) {
                    $question = Question::create([
                        'questionnaire_id' => $questionnaire->id,
                        'question_text' => $q['text'],
                        'question_type' => $q['type'],
                        'is_required' => true,
                        'order' => $order + 1,
                        'settings' => $q['type'] === 'scale' ? ['min' => 1, 'max' => 5, 'min_label' => 'Sangat Buruk', 'max_label' => 'Sangat Baik'] : null,
                    ]);

                    // Create options for choice questions
                    if (isset($q['options'])) {
                        foreach ($q['options'] as $optOrder => $optText) {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => $optText,
                                'order' => $optOrder + 1,
                            ]);
                        }
                    }
                }

                $questionnaires[] = $questionnaire;
            }
        }

        return $questionnaires;
    }

    private function createResponses(array $respondents, array $questionnaires): void
    {
        $totalResponses = 0;
        $completedResponses = 0;
        $totalRespondents = count($respondents);

        // Minimum 40% dari total responden harus mengisi setiap questionnaire
        $minimumRespondentsPerQuestionnaire = (int) ceil($totalRespondents * 0.4);

        $this->command->info("   Ensuring each questionnaire gets minimum {$minimumRespondentsPerQuestionnaire} completed responses...");

        // Step 1: Ensure each questionnaire gets minimum 40% completed responses
        foreach ($questionnaires as $questionnaire) {
            // Randomly select respondents for this questionnaire
            $selectedRespondents = collect($respondents)->random($minimumRespondentsPerQuestionnaire);

            foreach ($selectedRespondents as $respondent) {
                // Create completed response
                $response = Response::create([
                    'questionnaire_id' => $questionnaire->id,
                    'respondent_id' => $respondent->id,
                    'status' => 'completed',
                    'is_valid' => true,
                    'progress_percentage' => 100,
                    'latitude' => $respondent->latitude,
                    'longitude' => $respondent->longitude,
                    'device_info' => fake()->randomElement(['Android', 'iOS', 'Web']),
                    'ip_address' => fake()->ipv4(),
                    'started_at' => Carbon::now()->subDays(fake()->numberBetween(1, 20)),
                    'completed_at' => Carbon::now()->subDays(fake()->numberBetween(0, 19)),
                ]);

                $totalResponses++;
                $completedResponses++;

                // Create answers for all questions
                $questions = $questionnaire->questions;
                foreach ($questions as $question) {
                    $answerData = [
                        'response_id' => $response->id,
                        'question_id' => $question->id,
                        'answered_at' => Carbon::now()->subDays(fake()->numberBetween(0, 20)),
                    ];

                    switch ($question->question_type) {
                        case 'text':
                            $answerData['answer_text'] = $this->generateRealisticTextAnswer($question->question_text);
                            break;
                        case 'textarea':
                            $answerData['answer_text'] = $this->generateRealisticTextareaAnswer($question->question_text);
                            break;
                        case 'scale':
                            $answerData['answer_text'] = (string) fake()->numberBetween(1, 5);
                            break;
                        case 'single_choice':
                        case 'dropdown':
                            $option = $question->options->random();
                            $answerData['selected_option_id'] = $option->id;
                            break;
                        case 'multiple_choice':
                            $options = $question->options->random(fake()->numberBetween(1, min(3, $question->options->count())));
                            $answerData['selected_options'] = $options->pluck('id')->toArray();
                            break;
                    }

                    Answer::create($answerData);
                }
            }
        }

        // Step 2: Add additional random responses (both completed and in-progress)
        $this->command->info('   Adding additional random responses...');
        foreach ($respondents as $respondent) {
            // Each respondent might answer 0-2 additional random questionnaires (30% chance)
            if (fake()->boolean(30)) {
                $numToAnswer = fake()->numberBetween(1, 2);
                $selectedQuestionnaires = fake()->randomElements($questionnaires, min($numToAnswer, count($questionnaires)));

                foreach ($selectedQuestionnaires as $questionnaire) {
                    // Check if this respondent already responded to this questionnaire
                    $existingResponse = Response::where('questionnaire_id', $questionnaire->id)
                        ->where('respondent_id', $respondent->id)
                        ->exists();

                    if ($existingResponse) {
                        continue;  // Skip if already responded
                    }

                    // 60% chance completed, 40% in progress
                    $isCompleted = fake()->boolean(60);

                    $response = Response::create([
                        'questionnaire_id' => $questionnaire->id,
                        'respondent_id' => $respondent->id,
                        'status' => $isCompleted ? 'completed' : 'in_progress',
                        'is_valid' => $isCompleted,
                        'progress_percentage' => $isCompleted ? 100 : fake()->numberBetween(25, 75),
                        'latitude' => $respondent->latitude,
                        'longitude' => $respondent->longitude,
                        'device_info' => fake()->randomElement(['Android', 'iOS', 'Web']),
                        'ip_address' => fake()->ipv4(),
                        'started_at' => Carbon::now()->subDays(fake()->numberBetween(1, 20)),
                        'completed_at' => $isCompleted ? Carbon::now()->subDays(fake()->numberBetween(0, 19)) : null,
                    ]);

                    $totalResponses++;
                    if ($isCompleted)
                        $completedResponses++;

                    // Create answers
                    $questions = $questionnaire->questions;
                    $questionsToAnswer = $isCompleted ? $questions : $questions->take(fake()->numberBetween(1, $questions->count()));

                    foreach ($questionsToAnswer as $question) {
                        $answerData = [
                            'response_id' => $response->id,
                            'question_id' => $question->id,
                            'answered_at' => Carbon::now()->subDays(fake()->numberBetween(0, 20)),
                        ];

                        switch ($question->question_type) {
                            case 'text':
                                $answerData['answer_text'] = $this->generateRealisticTextAnswer($question->question_text);
                                break;
                            case 'textarea':
                                $answerData['answer_text'] = $this->generateRealisticTextareaAnswer($question->question_text);
                                break;
                            case 'scale':
                                $answerData['answer_text'] = (string) fake()->numberBetween(1, 5);
                                break;
                            case 'single_choice':
                            case 'dropdown':
                                $option = $question->options->random();
                                $answerData['selected_option_id'] = $option->id;
                                break;
                            case 'multiple_choice':
                                $options = $question->options->random(fake()->numberBetween(1, min(3, $question->options->count())));
                                $answerData['selected_options'] = $options->pluck('id')->toArray();
                                break;
                        }

                        Answer::create($answerData);
                    }

                    // Update response progress
                    if (!$isCompleted) {
                        $answeredCount = $response->answers()->count();
                        $totalQuestions = $questionnaire->questions()->count();
                        $response->update([
                            'progress_percentage' => round(($answeredCount / $totalQuestions) * 100, 2),
                            'last_question_id' => $questionsToAnswer->last()?->id,
                        ]);
                    }
                }
            }
        }

        $this->command->info("   Total responses: {$totalResponses} (Completed: {$completedResponses}, In Progress: " . ($totalResponses - $completedResponses) . ')');
    }

    private function generateRealisticTextAnswer(string $questionText): string
    {
        $questionLower = strtolower($questionText);

        // Lokasi/Alamat
        if (stripos($questionLower, 'jalan') !== false || stripos($questionLower, 'lokasi') !== false || stripos($questionLower, 'alamat') !== false) {
            return fake()->randomElement([
                'Jalan Raya Abepura', 'Jalan Ahmad Yani', 'Jalan Percetakan', 'Jalan Sentani',
                'Jalan Argapura', 'Jalan Jayapura-Sentani', 'Jalan Koti', 'Jalan Yos Sudarso',
                'Jalan Kelapa Dua', 'Jalan Sam Ratulangi', 'Jalan Polimak'
            ]);
        }

        // Nama tempat/pasar
        if (stripos($questionLower, 'pasar') !== false || stripos($questionLower, 'tempat') !== false) {
            return fake()->randomElement([
                'Pasar Sentani', 'Pasar Yotefa', 'Pasar Hamadi', 'Pasar Argapura',
                'Pasar Jayapura', 'Pasar Expo', 'Pasar Trikora'
            ]);
        }

        // Produk/kerajinan
        if (stripos($questionLower, 'produk') !== false || stripos($questionLower, 'kerajinan') !== false) {
            return fake()->randomElement([
                'Noken Papua', 'Ukiran kayu', 'Keripik sagu', 'Manisan buah matoa',
                'Tas rajutan', 'Gelang manik-manik', 'Patung asmat', 'Kerajinan kulit kayu'
            ]);
        }

        // Layanan kesehatan/puskesmas
        if (stripos($questionLower, 'layanan') !== false) {
            return fake()->randomElement([
                'Pemeriksaan umum', 'Imunisasi', 'Posyandu', 'KB', 'Konsultasi gizi',
                'Pemeriksaan ibu hamil', 'Rawat jalan', 'Apotek', 'Laboratorium'
            ]);
        }

        // Bantuan sosial
        if (stripos($questionLower, 'bantuan') !== false) {
            return fake()->randomElement([
                'PKH (Program Keluarga Harapan)', 'Bantuan Pangan Non Tunai (BPNT)',
                'Kartu Indonesia Pintar (KIP)', 'Bantuan Langsung Tunai (BLT)',
                'Kartu Sembako', 'Bantuan sosial Covid-19', 'Rastra', 'BPJS Kesehatan gratis'
            ]);
        }

        // Fasilitas pendidikan
        if (stripos($questionLower, 'fasilitas') !== false && stripos($questionLower, 'pendidikan') !== false) {
            return fake()->randomElement([
                'Ruang kelas', 'Perpustakaan', 'Laboratorium komputer', 'Lapangan olahraga',
                'Toilet', 'Kantin', 'Mushola', 'Ruang guru'
            ]);
        }

        // Infrastruktur
        if (stripos($questionLower, 'infrastruktur') !== false || stripos($questionLower, 'jalan') !== false) {
            return fake()->randomElement([
                'Jalan beraspal', 'Jalan paving', 'Jalan tanah', 'Drainase',
                'Lampu jalan', 'Jembatan', 'Gorong-gorong'
            ]);
        }

        // Waktu/jam
        if (stripos($questionLower, 'waktu') !== false || stripos($questionLower, 'jam') !== false) {
            return fake()->randomElement([
                '08.00 - 12.00', '13.00 - 16.00', '07.30 - 14.00',
                'Senin-Jumat pagi', 'Setiap hari', 'Weekday saja'
            ]);
        }

        // Frekuensi
        if (stripos($questionLower, 'sering') !== false || stripos($questionLower, 'berapa kali') !== false) {
            return fake()->randomElement([
                'Setiap hari', 'Seminggu sekali', 'Sebulan sekali', '2-3 kali seminggu',
                'Jarang', 'Kadang-kadang', 'Sering', 'Hampir setiap hari'
            ]);
        }

        // Default responses
        return fake()->randomElement([
            'Sudah cukup baik', 'Masih perlu ditingkatkan', 'Cukup memuaskan',
            'Belum optimal', 'Sangat baik', 'Kurang baik', 'Lumayan', 'Baik sekali'
        ]);
    }

    private function generateRealisticTextareaAnswer(string $questionText): string
    {
        // Generate realistic longer text answers in Indonesian
        $suggestions = [
            'Perlu ditingkatkan kebersihan dan kerapian fasilitas agar lebih nyaman untuk masyarakat. Semoga kedepannya bisa lebih baik lagi pelayanannya.',
            'Sebaiknya menambah petugas di jam-jam ramai supaya tidak terlalu lama mengantri. Secara keseluruhan sudah cukup baik.',
            'Fasilitas sudah cukup lengkap tetapi masih perlu perbaikan di beberapa bagian yang rusak. Harapannya bisa segera diperbaiki.',
            'Pelayanannya sudah memuaskan, petugas ramah dan membantu. Mungkin bisa ditambah layanan online agar lebih praktis.',
            'Masih ada kekurangan di sana-sini tapi secara umum sudah lumayan baik. Perlu perbaikan infrastruktur dan penambahan fasilitas pendukung.',
            'Sudah cukup bagus namun perlu ditingkatkan lagi kualitasnya. Terutama dari segi kebersihan dan kenyamanan fasilitas.',
            'Menurut saya perlu ada sosialisasi lebih banyak ke masyarakat tentang program-program yang ada. Informasinya masih kurang.',
            'Harapan saya pemerintah lebih memperhatikan kebutuhan masyarakat kecil. Banyak yang membutuhkan bantuan tapi belum terdata.',
            'Alangkah baiknya jika ada perbaikan jalan dan penerangan di malam hari untuk keamanan dan kenyamanan warga.',
            'Perlu penambahan tenaga profesional dan pelatihan untuk meningkatkan kualitas pelayanan kepada masyarakat.',
            'Sistem yang ada sudah bagus tapi masih sering terkendala jaringan internet. Perlu upgrade infrastruktur teknologi.',
            'Semoga kedepannya bisa lebih transparan dalam pengelolaan dan masyarakat lebih dilibatkan dalam pengambilan keputusan.',
        ];

        $problems = [
            'Kendala utama adalah akses yang masih sulit dijangkau, terutama saat musim hujan jalanan rusak dan becek.',
            'Masalah terbesar adalah kurangnya modal untuk mengembangkan usaha. Butuh bantuan dana atau akses pinjaman lunak.',
            'Sering terjadi putus listrik yang mengganggu aktivitas produksi. Perlu perbaikan infrastruktur kelistrikan.',
            'Kesulitan dalam pemasaran produk karena terbatasnya akses pasar dan promosi yang masih minim.',
            'Kekurangan tenaga kerja terampil dan ahli di bidang tertentu. Perlu ada program pelatihan rutin.',
            'Masih banyak masyarakat yang belum paham tentang prosedur dan persyaratan yang diperlukan.',
        ];

        if (stripos($questionText, 'saran') !== false || stripos($questionText, 'masukan') !== false || stripos($questionText, 'program') !== false) {
            return fake()->randomElement($suggestions);
        } elseif (stripos($questionText, 'kendala') !== false || stripos($questionText, 'masalah') !== false || stripos($questionText, 'kesulitan') !== false) {
            return fake()->randomElement($problems);
        }

        return fake()->randomElement(array_merge($suggestions, $problems));
    }
}
