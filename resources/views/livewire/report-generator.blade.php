<div class="p-6 bg-white rounded-lg shadow-md max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold text-blue-600">🤖 Laporan AI - Analisis Survey Interaktif</h2>
        <div class="text-sm text-gray-600">
            👤 <span class="font-semibold">{{ auth()->user()->name ?? 'User' }}</span>
        </div>
    </div>

    <!-- Authorization Check -->
    @if(!auth()->check())
        <div class="mb-6 p-6 bg-red-100 text-red-700 rounded-lg border border-red-300 text-center">
            <p class="text-lg font-semibold mb-3">🔐 Akses Terbatas</p>
            <p class="mb-4">Anda harus login terlebih dahulu untuk menggunakan fitur Laporan AI.</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                🔑 Login Sekarang
            </a>
        </div>
    @elseif(!auth()->user()->opd_id)
        <div class="mb-6 p-6 bg-yellow-100 text-yellow-700 rounded-lg border border-yellow-300">
            <p class="text-lg font-semibold mb-2">⚠️ OPD Belum Terdaftar</p>
            <p>Akun Anda belum terdaftar ke OPD manapun. Hubungi administrator untuk melengkapi data OPD Anda.</p>
        </div>
    @else
        <!-- Form Input -->
        <div class="space-y-6 mb-8 bg-gray-50 p-6 rounded-lg">
            <!-- OPD Display -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">📍 OPD Anda</label>
                <div class="px-4 py-3 bg-blue-100 text-blue-800 rounded-md font-medium border border-blue-300">
                    {{ $opdName }}
                </div>
                <p class="text-xs text-gray-600 mt-2">💡 Anda hanya dapat mengakses questionnaire dari OPD ini</p>
            </div>

            <!-- Questionnaire Selector -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">📋 Pilih Questionnaire</label>
                @if($questionnaires->count() > 0)
                    <select
                        wire:model="questionnaire_id"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition"
                    >
                        <option value="">-- Pilih Questionnaire --</option>
                        @foreach($questionnaires as $q)
                            <option value="{{ $q->id }}">{{ $q->title }} ({{ $q->responses()->where('status', 'completed')->count() }} responses)</option>
                        @endforeach
                    </select>
                    @error('questionnaire_id')
                        <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                    @enderror
                @else
                    <p class="text-gray-600 italic">Tidak ada questionnaire aktif untuk OPD Anda</p>
                @endif
            </div>

            <!-- User Prompt Input -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">💬 Pertanyaan Anda (Bahasa Indonesia)</label>
                <textarea
                    wire:model="user_prompt"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 transition resize-none"
                    placeholder="Contoh: Berapa total responden per kecamatan? Atau: Tampilkan distribusi jawaban untuk pertanyaan nomor 3"
                    rows="4"
                ></textarea>
                @error('user_prompt')
                    <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                @enderror
                <p class="text-gray-500 text-xs mt-2">💡 Tips: Tanyakan dalam Bahasa Indonesia dengan jelas dan spesifik</p>
            </div>

            <!-- Generate Button -->
            <button
                wire:click="generateReport"
                wire:loading.attr="disabled"
                class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
            >
                <span wire:loading.remove>🚀 Generate Laporan</span>
                <span wire:loading>⏳ Processing... Mohon tunggu...</span>
            </button>
        </div>

        <!-- Success Message -->
        @if($success)
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg flex items-start">
                <span class="text-2xl mr-3">✅</span>
                <div>
                    <p class="font-semibold">Laporan berhasil dibuat!</p>
                    <p class="text-sm">Lihat hasil analisis di bawah dalam 3 format berbeda</p>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if($error)
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg flex items-start">
                <span class="text-2xl mr-3">⚠️</span>
                <div>
                    <p class="font-semibold">Terjadi Error</p>
                    <p class="text-sm">{{ $error }}</p>
                </div>
            </div>
        @endif
    @endif
</div>

    <!-- Report Output -->
    @if($reportData)
        <div class="mt-8">
            <!-- Tab Navigation -->
            <div class="flex space-x-2 border-b-2 border-gray-300 mb-6">
                <button
                    wire:click="$set('selectedTab', 'data')"
                    class="px-6 py-3 font-semibold transition {{ $selectedTab === 'data' ? 'border-b-4 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}"
                >
                    📊 Data Raw
                </button>
                <button
                    wire:click="$set('selectedTab', 'chart')"
                    class="px-6 py-3 font-semibold transition {{ $selectedTab === 'chart' ? 'border-b-4 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}"
                >
                    📈 Chart
                </button>
                <button
                    wire:click="$set('selectedTab', 'map')"
                    class="px-6 py-3 font-semibold transition {{ $selectedTab === 'map' ? 'border-b-4 border-blue-600 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}"
                >
                    🗺️ Peta
                </button>
            </div>

            <!-- Tab Content -->
            <div class="bg-gray-50 p-8 rounded-lg">
                <!-- Tab: Data -->
                @if($selectedTab === 'data')
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">📊 Analisis Data</h3>

                        <!-- AI Answer -->
                        <div class="mb-8 p-6 bg-white rounded-lg border-l-4 border-blue-600 shadow-sm">
                            <h4 class="font-semibold text-gray-700 mb-3">🤖 Analisis AI:</h4>
                            <p class="text-gray-700 leading-relaxed">{{ $reportData['raw_data']['ai_analysis'] ?? 'Tidak ada analisis' }}</p>
                        </div>

                        <!-- Statistics -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <p class="text-gray-600 text-sm">Total Responses</p>
                                <p class="text-3xl font-bold text-blue-600">{{ $reportData['raw_data']['total_responses'] ?? 0 }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <p class="text-gray-600 text-sm">Total Questions</p>
                                <p class="text-3xl font-bold text-green-600">{{ $reportData['raw_data']['total_questions'] ?? 0 }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <p class="text-gray-600 text-sm">Total Answers</p>
                                <p class="text-3xl font-bold text-purple-600">{{ $reportData['raw_data']['total_answers'] ?? 0 }}</p>
                            </div>
                            <div class="bg-white p-4 rounded-lg border border-gray-200">
                                <p class="text-gray-600 text-sm">Completion Rate</p>
                                <p class="text-3xl font-bold text-orange-600">
                                    @if($reportData['raw_data']['total_questions'] > 0)
                                        {{ round(($reportData['raw_data']['total_answers'] / ($reportData['raw_data']['total_questions'] * $reportData['raw_data']['total_responses'])) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Insights -->
                        @if(!empty($reportData['raw_data']['insights']) && count($reportData['raw_data']['insights']) > 0)
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h4 class="font-semibold text-gray-700 mb-4">💡 Key Insights:</h4>
                                <ul class="space-y-3">
                                    @foreach($reportData['raw_data']['insights'] as $insight)
                                        <li class="flex items-start text-gray-700">
                                            <span class="mr-3 mt-1">✓</span>
                                            <span>{{ $insight }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Tab: Chart -->
                @if($selectedTab === 'chart')
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">{{ $reportData['chart_data']['title'] ?? 'Visualisasi Data' }}</h3>
                        <div class="bg-white p-8 rounded-lg">
                            <canvas id="chart"></canvas>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
                        <script>
                            const ctx = document.getElementById('chart').getContext('2d');
                            const chartType = '{{ $reportData["chart_data"]["type"] ?? "bar" }}';
                            const labels = @json($reportData['chart_data']['labels'] ?? []);
                            const series = @json($reportData['chart_data']['series'] ?? []);

                            const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

                            new Chart(ctx, {
                                type: chartType,
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Jumlah Responden',
                                        data: series,
                                        backgroundColor: colors.slice(0, labels.length),
                                        borderColor: colors.slice(0, labels.length),
                                        borderWidth: 2,
                                        borderRadius: 5,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
                                    plugins: {
                                        legend: {
                                            display: true,
                                            position: 'top',
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1
                                            }
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                @endif

                <!-- Tab: Map -->
                @if($selectedTab === 'map')
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">🗺️ Peta Lokasi Responden</h3>
                        <p class="text-gray-600 mb-4">Total Lokasi Teridentifikasi: <span class="font-bold">{{ $reportData['map_data']['total_markers'] ?? 0 }}</span></p>
                        <div id="map" style="height: 500px; border-radius: 8px; overflow: hidden;"></div>

                        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}"></script>
                        <script>
                            const mapCenter = @json($reportData['map_data']['center'] ?? ['lat' => -6.2088, 'lng' => 106.8456]);
                            const markers = @json($reportData['map_data']['markers'] ?? []);

                            const map = new google.maps.Map(document.getElementById('map'), {
                                center: mapCenter,
                                zoom: {{ $reportData['map_data']['zoom'] ?? 12 }},
                                mapTypeId: 'roadmap',
                                styles: [
                                    {
                                        featureType: 'poi',
                                        stylers: [{ visibility: 'off' }]
                                    }
                                ]
                            });

                            // Add markers
                            markers.forEach(function(markerData, index) {
                                new google.maps.Marker({
                                    position: {
                                        lat: markerData.lat,
                                        lng: markerData.lng
                                    },
                                    map: map,
                                    title: markerData.label,
                                    icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                                });
                            });

                            // Auto-fit bounds jika ada markers
                            if (markers.length > 0) {
                                const bounds = new google.maps.LatLngBounds();
                                markers.forEach(function(markerData) {
                                    bounds.extend({
                                        lat: markerData.lat,
                                        lng: markerData.lng
                                    });
                                });
                                map.fitBounds(bounds);
                            }
                        </script>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
