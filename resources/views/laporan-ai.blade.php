@extends('filament-panels::components.layout.index')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    #map { height: 500px; }
    .recording { animation: pulse 1.5s ease-in-out infinite; }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>

<div class="fi-page">
    <div class="fi-page-content">
        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold tracking-tight">Laporan AI</h1>
                <button onclick="location.reload()" class="fi-btn fi-btn-color-primary">
                    <svg class="fi-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>New Report</span>
                </button>
            </div>

            <!-- Card 1: Context -->
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6">
                        <svg class="inline-block w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Konteks
                    </h3>
                </div>
                <div class="fi-section-content p-6">
                    <form method="POST" action="/admin/laporan-ai/submit" id="reportForm">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">OPD</span>
                                </label>
                                <select name="opd_id" id="opdSelect" required class="fi-input block w-full rounded-lg border-none bg-white py-1.5 pe-3 ps-3 text-base text-gray-950 shadow-sm ring-1 ring-inset ring-gray-950/10 dark:bg-white/5 dark:text-white dark:ring-white/20">
                                    <option value="">Pilih OPD</option>
                                    @foreach($opds as $opd)
                                        <option value="{{ $opd->id }}">{{ $opd->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3">
                                    <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Questionnaire</span>
                                </label>
                                <select name="questionnaire_id" id="questionnaireSelect" required class="fi-input block w-full rounded-lg border-none bg-white py-1.5 pe-3 ps-3 text-base text-gray-950 shadow-sm ring-1 ring-inset ring-gray-950/10 dark:bg-white/5 dark:text-white dark:ring-white/20">
                                    <option value="">Pilih Questionnaire</option>
                                    @foreach($questionnaires as $qst)
                                        <option value="{{ $qst->id }}" data-opd="{{ $qst->opd_id }}">{{ $qst->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card 2: 10 Quick Ideas -->
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6">
                        <svg class="inline-block w-5 h-5 mr-2 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
                        </svg>
                        10 Ide Cepat
                    </h3>
                </div>
                <div class="fi-section-content p-6">
                    <div id="quickIdeasList" class="text-gray-500 dark:text-gray-400 text-center py-4">
                        Pilih OPD dan Questionnaire untuk melihat ide...
                    </div>
                </div>
            </div>

            <!-- Card 3: Prompt -->
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6">
                        <svg class="inline-block w-5 h-5 mr-2 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        Prompt
                    </h3>
                </div>
                <div class="fi-section-content p-6">
                    <div class="flex gap-2">
                        <textarea
                            name="prompt"
                            id="promptInput"
                            rows="3"
                            form="reportForm"
                            placeholder="Tulis pertanyaan Anda atau pilih dari ide cepat..."
                            class="flex-1 fi-input block w-full rounded-lg border-none bg-white py-1.5 pe-3 ps-3 text-base text-gray-950 shadow-sm ring-1 ring-inset ring-gray-950/10 dark:bg-white/5 dark:text-white dark:ring-white/20"
                        ></textarea>

                        <button type="button" id="voiceBtn" class="fi-btn fi-btn-color-danger fi-btn-size-xl">
                            <svg class="fi-btn-icon h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                            </svg>
                        </button>
                    </div>

                    <button type="submit" form="reportForm" class="mt-4 w-full fi-btn fi-btn-color-primary">
                        <svg class="fi-btn-icon h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        <span>Submit</span>
                    </button>
                </div>
            </div>

            <!-- Card 4: AI Response -->
            @if(session('report'))
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header">
                    <h3 class="fi-section-header-heading text-base font-semibold leading-6">
                        <svg class="inline-block w-5 h-5 mr-2 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                        </svg>
                        Respon AI
                    </h3>
                </div>
                <div class="fi-section-content p-6">
                    <!-- Tabs -->
                    <div class="fi-tabs flex border-b border-gray-200 dark:border-gray-700 mb-4">
                        <button onclick="switchTab('teks')" id="tab-teks" class="tab-button px-6 py-3 border-b-2 border-primary-500 font-medium text-sm text-primary-600 dark:text-primary-400">Teks</button>
                        <button onclick="switchTab('data')" id="tab-data" class="tab-button px-6 py-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Data</button>
                        <button onclick="switchTab('chart')" id="tab-chart" class="tab-button px-6 py-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Chart</button>
                        <button onclick="switchTab('peta')" id="tab-peta" class="tab-button px-6 py-3 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Peta</button>
                    </div>

                    <!-- Tab: Teks -->
                    <div id="teks" class="tab-content active prose dark:prose-invert max-w-none">
                        {!! nl2br(e(session('report')['text'] ?? 'Tidak ada teks')) !!}
                    </div>

                    <!-- Tab: Data -->
                    <div id="data" class="tab-content">
                        <h3 class="text-lg font-semibold mb-3">Data Responden</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gender</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Umur</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lat/Lng</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jawaban</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $rawData = session('report')['raw_data'] ?? [];
                                        $respondents = $rawData['respondent_details'] ?? [];
                                    @endphp

                                    @forelse($respondents as $resp)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">{{ $resp['nama'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $resp['jenis_kelamin'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $resp['umur'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $resp['lokasi'] ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @if(isset($resp['latitude']) && isset($resp['longitude']))
                                                    {{ $resp['latitude'] }}, {{ $resp['longitude'] }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if(isset($resp['jawaban']) && is_array($resp['jawaban']))
                                                    @foreach($resp['jawaban'] as $qa)
                                                        <strong>{{ $qa['pertanyaan'] }}:</strong> {{ $qa['jawaban'] }}<br>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab: Chart -->
                    <div id="chart" class="tab-content">
                        <canvas id="chartCanvas"></canvas>
                    </div>

                    <!-- Tab: Peta -->
                    <div id="peta" class="tab-content">
                        <div id="map" class="rounded-lg"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Voice Recognition
const voiceBtn = document.getElementById('voiceBtn');
const promptInput = document.getElementById('promptInput');
let recognition = null;

if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.lang = 'id-ID';
    recognition.continuous = false;

    recognition.onstart = () => voiceBtn.classList.add('recording');
    recognition.onresult = (event) => {
        promptInput.value = event.results[0][0].transcript;
    };
    recognition.onend = () => voiceBtn.classList.remove('recording');

    voiceBtn.addEventListener('click', () => {
        if (voiceBtn.classList.contains('recording')) {
            recognition.stop();
        } else {
            recognition.start();
        }
    });
} else {
    voiceBtn.disabled = true;
}

// OPD/Questionnaire filtering
const opdSelect = document.getElementById('opdSelect');
const questionnaireSelect = document.getElementById('questionnaireSelect');
const allQuestionnaireOptions = Array.from(questionnaireSelect.options);

opdSelect.addEventListener('change', function() {
    const selectedOpdId = this.value;
    questionnaireSelect.innerHTML = '<option value="">Pilih Questionnaire</option>';

    if (selectedOpdId) {
        allQuestionnaireOptions.forEach(option => {
            if (option.dataset.opd === selectedOpdId) {
                questionnaireSelect.appendChild(option.cloneNode(true));
            }
        });
    }
    questionnaireSelect.dispatchEvent(new Event('change'));
});

// Load Quick Ideas
questionnaireSelect.addEventListener('change', async function() {
    const questionnaireId = this.value;
    const opdId = opdSelect.value;

    if (!questionnaireId || !opdId) {
        document.getElementById('quickIdeasList').innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Pilih OPD dan Questionnaire untuk melihat ide...</p>';
        return;
    }

    document.getElementById('quickIdeasList').innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Loading...</p>';

    try {
        const response = await fetch(`/admin/laporan-ai/quick-ideas?questionnaire_id=${questionnaireId}&opd_id=${opdId}`);
        const data = await response.json();

        if (data.ideas && data.ideas.length > 0) {
            let html = '<div class="grid grid-cols-1 gap-3">';
            data.ideas.forEach((idea, index) => {
                html += `
                    <button
                        type="button"
                        onclick="selectIdea(this.dataset.idea)"
                        data-idea="${idea.replace(/"/g, '&quot;')}"
                        class="text-left p-4 bg-gray-50 dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 transition-all"
                    >
                        <span class="font-semibold text-primary-600 dark:text-primary-400">${index + 1}.</span>
                        <span class="text-gray-700 dark:text-gray-300">${idea}</span>
                    </button>
                `;
            });
            html += '</div>';
            document.getElementById('quickIdeasList').innerHTML = html;
        } else {
            document.getElementById('quickIdeasList').innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center py-4">Tidak ada ide tersedia</p>';
        }
    } catch (error) {
        console.error('Error loading ideas:', error);
        document.getElementById('quickIdeasList').innerHTML = '<p class="text-red-500 text-center py-4">Error loading ideas</p>';
    }
});

function selectIdea(idea) {
    promptInput.value = idea;
    promptInput.focus();
}

// Tab switching
function switchTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-primary-500', 'text-primary-600', 'dark:text-primary-400');
        btn.classList.add('border-transparent', 'text-gray-500');
    });

    document.getElementById(tabName).classList.add('active');
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tabName).classList.add('border-primary-500', 'text-primary-600', 'dark:text-primary-400');

    if (tabName === 'chart') renderChart();
    if (tabName === 'peta') renderMap();
}

// Chart rendering
let chartInstance = null;
function renderChart() {
    const reportData = @json(session('report', []));
    if (!reportData || !reportData.chart_data) return;

    const ctx = document.getElementById('chartCanvas').getContext('2d');
    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(ctx, {
        type: 'bar',
        data: reportData.chart_data,
        options: {
            responsive: true,
            plugins: {
                legend: { display: true },
                title: { display: true, text: 'Visualisasi Data' }
            }
        }
    });
}

// Map rendering
let mapInstance = null;
function renderMap() {
    const reportData = @json(session('report', []));
    if (!reportData || !reportData.map_data || !reportData.map_data.markers) return;

    if (!mapInstance) {
        mapInstance = L.map('map').setView([-2.5, 118.0], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(mapInstance);
    }

    reportData.map_data.markers.forEach(marker => {
        L.marker([marker.lat, marker.lng])
            .bindPopup(marker.popup)
            .addTo(mapInstance);
    });

    if (reportData.map_data.markers.length > 0) {
        const bounds = reportData.map_data.markers.map(m => [m.lat, m.lng]);
        mapInstance.fitBounds(bounds);
    }
}
</script>
@endsection
