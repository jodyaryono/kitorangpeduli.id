<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laporan AI - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key', env('GOOGLE_MAPS_API_KEY')) }}&libraries=marker&loading=async"></script>
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        #map { height: 500px; }
        .recording {
            animation: pulse 1.5s ease-in-out infinite;
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%) !important;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        .idea-card {
            transition: all 0.2s;
            cursor: pointer;
        }
        .idea-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .marker-cluster-medium div {
            background-color: rgba(139, 92, 246, 0.8);
        }
        .marker-cluster-large {
            background-color: rgba(236, 72, 153, 0.6);
        }
        .marker-cluster-large div {
            background-color: rgba(236, 72, 153, 0.8);
        }
        .marker-cluster div {
            width: 34px;
            height: 34px;
            margin-left: 3px;
            margin-top: 3px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 14px;
        }
        .marker-cluster span {
            line-height: 34px;
            color: white;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 text-white min-h-screen">
<div class="min-h-screen py-8 px-4">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <div class="flex items-center gap-4 mb-2">
                        <a href="/admin" class="text-gray-400 hover:text-blue-400 transition-colors flex items-center gap-2 group">
                            <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span class="text-sm">Kembali ke Dashboard</span>
                        </a>
                    </div>
                    <h1 class="text-4xl font-bold gradient-text mb-2">🤖 Laporan AI</h1>
                    <p class="text-gray-400">Generate laporan cerdas dengan AI dari data survey</p>
                </div>
                <button onclick="location.reload()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl flex items-center gap-2 shadow-lg shadow-blue-500/30 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Report
                </button>
            </div>

            <!-- Context -->
            <div class="bg-gray-900/50 backdrop-blur-xl rounded-2xl p-6 ring-1 ring-white/10 shadow-2xl">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2 bg-blue-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h2 class="text-xl font-semibold">Konteks Laporan</h2>
                </div>
                <form method="POST" action="/admin/laporan-ai/submit" id="reportForm">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-300">OPD</label>
                            <select name="opd_id" id="opdSelect" required class="w-full bg-gray-800/80 border border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">Pilih OPD</option>
                                @foreach($opds ?? [] as $opd)
                                    <option value="{{ $opd->id }}" {{ session('selected_opd') == $opd->id ? 'selected' : '' }}>{{ $opd->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 text-gray-300">Questionnaire</label>
                            <select name="questionnaire_id" id="questionnaireSelect" required class="w-full bg-gray-800/80 border border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                <option value="">Pilih Questionnaire</option>
                                {{-- Options will be populated by JavaScript based on selected OPD --}}
                            </select>
                            <p class="text-xs text-gray-500 mt-1">* Minimal 10 pertanyaan & 40% response rate (80 responden)</p>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Quick Ideas -->
            <div class="bg-gradient-to-br from-gray-900/90 to-gray-800/90 backdrop-blur-xl rounded-2xl p-8 ring-1 ring-white/10 shadow-2xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-gradient-to-br from-yellow-500/30 to-orange-500/30 rounded-xl shadow-lg">
                        <svg class="w-7 h-7 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold gradient-text">10 Ide Cepat</h2>
                        <p class="text-sm text-gray-400 mt-1">Pilih salah satu untuk analisis data survey</p>
                    </div>
                </div>
                <div id="quickIdeasList" class="text-gray-400 text-center py-12">
                    <div class="inline-block p-4 bg-gray-800/50 rounded-2xl mb-4">
                        <svg class="w-20 h-20 mx-auto text-yellow-500/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <p class="text-lg">Pilih OPD dan Questionnaire untuk melihat ide...</p>
                </div>
            </div>

            <!-- Prompt -->
            <div class="bg-gray-900/50 backdrop-blur-xl rounded-2xl p-6 ring-1 ring-white/10 shadow-2xl">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <h2 class="text-xl font-semibold">Tulis Prompt Anda</h2>
                    </div>
                    <button type="button" id="clearPromptBtn" onclick="clearPrompt()" class="px-4 py-2 bg-gray-700/50 hover:bg-red-600/80 rounded-lg flex items-center gap-2 transition-all text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Clear
                    </button>
                </div>
                <div class="flex gap-3 mb-4">
                    <textarea name="prompt" id="promptInput" rows="4" form="reportForm"
                        placeholder="Tulis pertanyaan Anda atau pilih dari ide cepat di atas..."
                        class="flex-1 bg-gray-800/80 border border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"></textarea>
                    <div class="flex flex-col gap-2">
                        <button type="button" id="voiceBtn" class="px-6 py-3 bg-gradient-to-br from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl shadow-lg shadow-red-500/30 transition-all relative" title="Tekan untuk voice input">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                        </button>
                        <p class="text-xs text-gray-500 text-center">Tekan mic
                        untuk bicara</p>
                        <!-- Voice Input Visualizer -->
                        <div id="voiceInputVisualizer" class="hidden flex items-center justify-center gap-1 px-2">
                            <div class="voice-input-bar w-1 bg-red-500 rounded-full"></div>
                            <div class="voice-input-bar w-1 bg-red-500 rounded-full"></div>
                            <div class="voice-input-bar w-1 bg-red-500 rounded-full"></div>
                            <div class="voice-input-bar w-1 bg-red-500 rounded-full"></div>
                            <div class="voice-input-bar w-1 bg-red-500 rounded-full"></div>
                        </div>
                    </div>
                </div>
                <button type="submit" form="reportForm" class="w-full px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 transition-all font-semibold text-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Generate Laporan AI
                </button>
            </div>

            <!-- Error Message -->
            @if(session('report_error'))
            <div class="bg-red-900/50 backdrop-blur-xl rounded-2xl p-6 ring-1 ring-red-500/50 shadow-2xl">
                <div class="flex items-center gap-3 text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-semibold">{{ session('report_error') }}</p>
                </div>
            </div>
            @endif

            <!-- Loading Modal -->
            <div id="loadingModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50" style="display: none;">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 max-w-md mx-4 shadow-2xl ring-1 ring-purple-500/50">
                    <div class="text-center">
                        <!-- Animated Robot Icon -->
                        <div class="mb-6">
                            <svg class="w-20 h-20 mx-auto text-blue-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <!-- Loading Text -->
                        <h3 class="text-2xl font-bold text-white mb-2">Memproses Data...</h3>
                        <p class="text-gray-400 mb-6">AI sedang menganalisis data survey Anda</p>
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-700 rounded-full h-2 mb-4 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-full animate-pulse" style="width: 70%"></div>
                        </div>
                        <p class="text-sm text-gray-500">Mohon tunggu sebentar...</p>
                    </div>
                </div>
            </div>

            <!-- Initial Page Load Modal -->
            <div id="initialLoadModal" class="fixed inset-0 bg-black/90 backdrop-blur-md flex items-center justify-center z-50">
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 max-w-md mx-4 shadow-2xl ring-1 ring-blue-500/50">
                    <div class="text-center">
                        <!-- Animated Robot Icon -->
                        <div class="mb-6">
                            <div class="relative">
                                <svg class="w-24 h-24 mx-auto text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <div class="absolute inset-0 bg-blue-500/20 rounded-full blur-2xl animate-pulse"></div>
                            </div>
                        </div>
                        <!-- Loading Text -->
                        <h3 class="text-3xl font-bold gradient-text mb-3">Memuat Laporan AI</h3>
                        <p class="text-gray-400 mb-6">Menyiapkan data dan konteks untuk analisis...</p>
                        <!-- Spinner -->
                        <div class="flex justify-center mb-4">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-t-2 border-blue-500"></div>
                        </div>
                        <p class="text-sm text-gray-500">Tunggu sebentar, sistem sedang memuat...</p>
                    </div>
                </div>
            </div>

            <!-- AI Response -->
            @if(session('report'))
            <div id="aiResponse" class="bg-gradient-to-br from-gray-900/80 to-gray-800/80 backdrop-blur-xl rounded-2xl p-6 ring-1 ring-purple-500/30 shadow-2xl shadow-purple-500/20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-purple-500/20 rounded-lg">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    </div>
                    <h2 class="text-2xl font-semibold gradient-text">✨ Respon AI</h2>
                </div>

                <!-- Submitted Context -->
                <div class="bg-gray-800/50 rounded-xl p-4 mb-6 grid md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-1">OPD</p>
                        <p class="text-gray-200 font-medium">{{ session('selected_opd_name') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-1">Questionnaire</p>
                        <p class="text-gray-200 font-medium">{{ session('questionnaire_title') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase mb-1">Pertanyaan Anda</p>
                        <p class="text-gray-300 text-sm italic">"{{ Str::limit(session('prompt'), 100) ?? 'N/A' }}"</p>
                    </div>
                </div>

                @php
                    $isDataDriven = session('report')['data_driven'] ?? true;
                @endphp

                <div class="border-b border-gray-700/50 mb-6">
                    <nav class="flex gap-2 -mb-px">
                        <button onclick="switchTab('teks')" id="tab-teks" class="tab-btn px-6 py-3 border-b-2 border-blue-500 text-blue-400 font-medium rounded-t-lg bg-blue-500/10">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Teks
                        </button>
                        @if($isDataDriven)
                        <button onclick="switchTab('data')" id="tab-data" class="tab-btn px-6 py-3 border-b-2 border-transparent hover:border-gray-500 text-gray-400 hover:text-white font-medium rounded-t-lg hover:bg-gray-800/50">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Data {{{ count(session('report')['raw_data']['respondent_details'] ?? []) }}}
                        </button>
                        <button onclick="switchTab('chart')" id="tab-chart" class="tab-btn px-6 py-3 border-b-2 border-transparent hover:border-gray-500 text-gray-400 hover:text-white font-medium rounded-t-lg hover:bg-gray-800/50">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Chart
                        </button>
                        <button onclick="switchTab('peta')" id="tab-peta" class="tab-btn px-6 py-3 border-b-2 border-transparent hover:border-gray-500 text-gray-400 hover:text-white font-medium rounded-t-lg hover:bg-gray-800/50">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            Peta
                        </button>
                        @endif
                    </nav>
                </div>

                <div id="teks" class="tab-content active">
                    <!-- TTS Controls -->
                    <div class="flex items-center justify-between mb-4 p-4 bg-gray-800/80 rounded-xl border border-gray-700">
                        <div class="flex items-center gap-3">
                            <button id="ttsBtn" onclick="toggleTTS()" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 rounded-lg flex items-center gap-2 shadow-lg transition-all">
                                <svg id="ttsIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                </svg>
                                <span id="ttsText">Dengarkan</span>
                            </button>
                            <button id="ttsStop" onclick="stopTTS()" class="hidden px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg flex items-center gap-2 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10h6v4H9z"/>
                                </svg>
                                Stop
                            </button>
                        </div>
                        <!-- Voice Visualizer -->
                        <div id="voiceVisualizer" class="hidden flex items-center gap-1">
                            <div class="voice-bar w-1 bg-green-500 rounded-full"></div>
                            <div class="voice-bar w-1 bg-green-500 rounded-full"></div>
                            <div class="voice-bar w-1 bg-green-500 rounded-full"></div>
                            <div class="voice-bar w-1 bg-green-500 rounded-full"></div>
                            <div class="voice-bar w-1 bg-green-500 rounded-full"></div>
                            <div class="voice-bar w-1 bg-green-500 rounded-full"></div>
                            <div class="voice-bar w-1 bg-green-500 rounded-full"></div>
                            <div class="voice-bar w-1 bg-green-500 rounded-full"></div>
                        </div>
                    </div>

                    <div class="prose prose-invert max-w-none bg-gray-800/50 rounded-xl p-6">
                        @php
                            $text = session('report')['text'] ?? 'Tidak ada respon';
                            // Remove JSON markers if present
                            $text = preg_replace('/^```json\s*/i', '', $text);
                            $text = preg_replace('/```\s*$/i', '', $text);
                            // Try to parse if it's JSON
                            $decoded = json_decode($text, true);
                            if (is_array($decoded) && isset($decoded['answer'])) {
                                $text = $decoded['answer'];
                            }
                            // Replace <br/> and <br> with actual line breaks
                            $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
                            // Strip other HTML tags
                            $text = strip_tags($text);
                        @endphp
                        <div class="text-gray-200 leading-relaxed whitespace-pre-wrap">{{ $text }}</div>
                    </div>
                </div>

                <div id="data" class="tab-content">
                    <!-- Search Box and Export -->
                    <div class="mb-4 flex gap-3">
                        <input type="text" id="searchData" placeholder="Cari nama, gender, lokasi..." class="flex-1 bg-gray-800/80 border border-gray-700 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" onkeyup="filterTableData()">
                        <button onclick="exportToExcel()" class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 rounded-xl flex items-center gap-2 shadow-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export Excel
                        </button>
                    </div>

                    <div class="overflow-x-auto bg-gray-800/50 rounded-xl">
                        <table class="w-full" id="dataTable">
                            <thead>
                                <tr class="border-b border-gray-700 bg-gray-800">
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Nama</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Gender</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Umur</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Alamat</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Jawaban</th>
                            </tr>
                        </thead>
                        <tbody id="dataTableBody">
                            @foreach((session('report')['raw_data']['respondent_details'] ?? []) as $index => $resp)
                                <tr class="border-b border-gray-800 data-row" data-index="{{ $index }}" style="{{ $index >= 10 ? 'display: none;' : '' }}">
                                    <td class="px-4 py-2">{{ $resp['nama'] ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $resp['jenis_kelamin'] ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $resp['umur'] ?? '-' }} tahun</td>
                                    <td class="px-4 py-2 text-sm">
                                        {{ $resp['desa'] ?? '-' }}, {{ $resp['kecamatan'] ?? '-' }}, {{ $resp['kabupaten'] ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm">
                                        @foreach(($resp['jawaban'] ?? []) as $qa)
                                            <div class="mb-2"><strong class="text-blue-400">{{ $qa['question'] ?? '' }}:</strong> <span class="text-gray-300">{{ $qa['answer'] ?? '' }}</span></div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>

                    @if(count(session('report')['raw_data']['respondent_details'] ?? []) > 10)
                    <div class="mt-4 text-center">
                        <button id="loadMoreBtn" onclick="loadMoreData()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-xl font-medium transition-all">
                            Load More Data
                        </button>
                        <p class="text-gray-400 text-sm mt-2">Showing <span id="visibleCount">10</span> of {{ count(session('report')['raw_data']['respondent_details'] ?? []) }} records</p>
                    </div>
                    @endif
                </div>

                <div id="chart" class="tab-content">
                    <div class="mb-4 text-right">
                        <button onclick="printCharts()" class="px-6 py-3 bg-gradient-to-r from-rose-600 to-rose-700 hover:from-rose-700 hover:to-rose-800 rounded-xl flex items-center gap-2 shadow-lg transition-all inline-flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak PDF
                        </button>
                    </div>
                    <div id="chartContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Charts will be dynamically rendered here -->
                    </div>
                </div>

                <div id="peta" class="tab-content">
                    <div class="mb-4 text-right">
                        <button onclick="printMap()" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 rounded-xl flex items-center gap-2 shadow-lg transition-all inline-flex">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak PDF
                        </button>
                    </div>
                    <div id="map" class="rounded-lg" style="height: 600px;"></div>
            </div>
            @endif
        </div>
    </div>

    <script>
    let REPORT = {};
    try {
        REPORT = {!! json_encode(session('report', []), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
        console.log('REPORT data:', REPORT);
    } catch(e) {
        console.error('Error parsing REPORT:', e);
        REPORT = {};
    }
    console.log('Session report exists:', {!! json_encode(session()->has('report')) !!});
    console.log('Session error:', {!! json_encode(session('report_error', null), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!});

    // Restore submitted values and reload ideas
    @if(session('report'))
    window.addEventListener('load', function() {
        const opdSelect = document.getElementById('opdSelect');
        const questionnaireSelect = document.getElementById('questionnaireSelect');
        const promptInput = document.getElementById('promptInput');

        // Set OPD value from session
        const reportOpdId = {!! json_encode(session('selected_opd', null)) !!};
        const reportQuestionnaireId = {!! json_encode(session('selected_questionnaire_id', null)) !!};
        console.log('Restoring values - OPD:', reportOpdId, 'Questionnaire:', reportQuestionnaireId);

        if (reportOpdId && opdSelect) {
            opdSelect.value = reportOpdId;
            opdSelect.dispatchEvent(new Event('change'));

            // Wait for OPD change to populate questionnaires, then set questionnaire value
            const waitForQuestionnaires = setInterval(() => {
                if (questionnaireSelect.options.length > 1) { // More than just "Pilih Questionnaire"
                    clearInterval(waitForQuestionnaires);

                    if (reportQuestionnaireId) {
                        questionnaireSelect.value = reportQuestionnaireId;
                        console.log('Setting questionnaire to:', reportQuestionnaireId);
                        questionnaireSelect.dispatchEvent(new Event('change'));
                    }
                }
            }, 50); // Check every 50ms

            // Timeout after 2 seconds
            setTimeout(() => clearInterval(waitForQuestionnaires), 2000);
        }

        // Populate prompt from session
        if (promptInput) {
            promptInput.value = {!! json_encode(session('prompt', ''), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
        }
    });
    @endif

    // Voice Input with Visualizer
    const voiceBtn = document.getElementById('voiceBtn');
    const promptInput = document.getElementById('promptInput');
    const voiceInputVisualizer = document.getElementById('voiceInputVisualizer');

    if ('webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.lang = 'id-ID';
        recognition.continuous = false;
        recognition.interimResults = false;

        recognition.onstart = () => {
            voiceBtn.classList.add('recording', 'animate-pulse');
            startVoiceInputVisualizer();
        };

        recognition.onresult = (e) => {
            promptInput.value = e.results[0][0].transcript;
        };

        recognition.onend = () => {
            voiceBtn.classList.remove('recording', 'animate-pulse');
            stopVoiceInputVisualizer();
        };

        recognition.onerror = (e) => {
            console.error('Recognition error:', e);
            voiceBtn.classList.remove('recording', 'animate-pulse');
            stopVoiceInputVisualizer();
        };

        voiceBtn.onclick = () => {
            if (voiceBtn.classList.contains('recording')) {
                recognition.stop();
            } else {
                recognition.start();
            }
        };
    }

    function startVoiceInputVisualizer() {
        voiceInputVisualizer.classList.remove('hidden');
        voiceInputVisualizer.style.display = 'flex';

        const bars = voiceInputVisualizer.querySelectorAll('.voice-input-bar');
        bars.forEach((bar, index) => {
            animateVoiceInputBar(bar, index);
        });
    }

    function animateVoiceInputBar(bar, index) {
        const animate = () => {
            if (!voiceBtn.classList.contains('recording')) {
                bar.style.height = '8px';
                return;
            }

            const randomHeight = Math.random() * 28 + 8; // 8-36px
            bar.style.height = randomHeight + 'px';
            bar.style.transition = 'height 0.08s ease';

            setTimeout(animate, 90 + index * 15);
        };
        animate();
    }

    function stopVoiceInputVisualizer() {
        voiceInputVisualizer.classList.add('hidden');

        const bars = voiceInputVisualizer.querySelectorAll('.voice-input-bar');
        bars.forEach(bar => {
            bar.style.height = '8px';
        });
    }

    // Filter - load questionnaires data from backend
    const questionnaireData = {!! json_encode($questionnaires ?? [], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};

    console.log('Total questionnaires loaded:', questionnaireData.length);
    console.log('Questionnaire data:', questionnaireData);

    const opdSelect = document.getElementById('opdSelect');
    const questionnaireSelect = document.getElementById('questionnaireSelect');

    opdSelect.onchange = function() {
        console.log('OPD changed to:', this.value);
        questionnaireSelect.innerHTML = '<option value="">Pilih Questionnaire</option>';

        if (this.value) {
            const filteredQuestionnaires = questionnaireData.filter(q => q.opd_id == this.value);
            console.log('Filtered questionnaires:', filteredQuestionnaires.length, filteredQuestionnaires);

            filteredQuestionnaires.forEach(q => {
                const opt = document.createElement('option');
                opt.value = q.id;
                opt.dataset.opd = q.opd_id;
                opt.textContent = `${q.title} (${q.questions_count} pertanyaan, ${q.responses_count} responden)`;
                questionnaireSelect.add(opt);
            });

            console.log('Total options added:', questionnaireSelect.options.length - 1);
        }
        questionnaireSelect.dispatchEvent(new Event('change'));
    };

    // Trigger OPD change on page load if OPD is already selected
    if (opdSelect.value) {
        console.log('Triggering initial OPD change for:', opdSelect.value);
        opdSelect.dispatchEvent(new Event('change'));
    }

    // Ideas
    questionnaireSelect.onchange = async function() {
        console.log('Questionnaire changed:', this.value, 'OPD:', opdSelect.value);

        if (!this.value || !opdSelect.value) {
            document.getElementById('quickIdeasList').innerHTML = `
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <p class="text-center py-4 text-gray-400">Pilih OPD dan Questionnaire...</p>`;
            return;
        }

        document.getElementById('quickIdeasList').innerHTML = '<div class="text-center py-8"><div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div><p class="mt-4 text-gray-400">Loading ide...</p></div>';

        try {
            const url = `/admin/laporan-ai/quick-ideas?questionnaire_id=${this.value}&opd_id=${opdSelect.value}`;
            console.log('Fetching ideas from:', url);

            const res = await fetch(url);
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}: ${res.statusText}`);
            }

            const data = await res.json();
            console.log('Ideas response:', data);

            if (data.ideas?.length) {
                // Ensure we have exactly 10 ideas
                const ideas = data.ideas.slice(0, 10);
                let html = '<div class="grid md:grid-cols-2 gap-4">';
                ideas.forEach((idea, i) => {
                    const colors = [
                        'from-blue-500/10 to-blue-600/5 border-blue-500/30 hover:border-blue-400',
                        'from-purple-500/10 to-purple-600/5 border-purple-500/30 hover:border-purple-400',
                        'from-pink-500/10 to-pink-600/5 border-pink-500/30 hover:border-pink-400',
                        'from-green-500/10 to-green-600/5 border-green-500/30 hover:border-green-400',
                        'from-orange-500/10 to-orange-600/5 border-orange-500/30 hover:border-orange-400'
                    ];
                    const color = colors[i % colors.length];

                    html += `
                        <button type="button" onclick="appendToPrompt(this.dataset.idea)" data-idea="${idea.replace(/"/g, '&quot;')}"
                            class="idea-card group text-left p-5 bg-gradient-to-br ${color} rounded-xl border-2 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-gradient-to-br from-white/10 to-white/5 font-bold text-lg group-hover:from-white/20 group-hover:to-white/10 transition-all shadow-lg">
                                    ${i+1}
                                </div>
                                <p class="flex-1 text-gray-200 leading-relaxed group-hover:text-white transition-colors font-medium">${idea}</p>
                            </div>
                            <div class="mt-3 flex items-center gap-2 text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                                <span>Klik untuk gunakan</span>
                            </div>
                        </button>`;
                });
                html += '</div>';
                document.getElementById('quickIdeasList').innerHTML = html;
            } else {
                document.getElementById('quickIdeasList').innerHTML = '<p class="text-center py-8 text-gray-400">Tidak ada ide tersedia</p>';
            }
        } catch (e) {
            console.error('Error loading ideas:', e);
            document.getElementById('quickIdeasList').innerHTML = `<p class="text-center py-8 text-red-400">Error loading ideas: ${e.message}</p>`;
        }
    };

    // Tabs
    function switchTab(name) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('border-blue-500', 'text-blue-400', 'bg-blue-500/10');
            el.classList.add('border-transparent', 'text-gray-400');
        });
        document.getElementById(name).classList.add('active');
        const tab = document.getElementById('tab-' + name);
        tab.classList.remove('border-transparent', 'text-gray-400');
        tab.classList.add('border-blue-500', 'text-blue-400', 'bg-blue-500/10');

        if (name === 'chart') renderChart();
        if (name === 'peta') renderMap();
    }

    // Data Table Functions
    let visibleRows = 10;
    function loadMoreData() {
        const rows = document.querySelectorAll('.data-row');
        const totalRows = rows.length;
        visibleRows = Math.min(visibleRows + 10, totalRows);

        rows.forEach((row, index) => {
            if (index < visibleRows) {
                row.style.display = '';
            }
        });

        document.getElementById('visibleCount').textContent = visibleRows;

        if (visibleRows >= totalRows) {
            document.getElementById('loadMoreBtn').style.display = 'none';
        }
    }

    function filterTableData() {
        const searchValue = document.getElementById('searchData').value.toLowerCase();
        const rows = document.querySelectorAll('.data-row');
        let visibleCount = 0;

        rows.forEach((row, index) => {
            const text = row.textContent.toLowerCase();
            const matches = text.includes(searchValue);

            if (matches && (searchValue || index < visibleRows)) {
                row.style.display = '';
                visibleCount++;
            } else if (!matches) {
                row.style.display = 'none';
            } else if (!searchValue && index >= visibleRows) {
                row.style.display = 'none';
            }
        });

        // Update visible count if searching
        if (searchValue && document.getElementById('visibleCount')) {
            document.getElementById('visibleCount').textContent = visibleCount;
        }
    }

    // Export to Excel
    function exportToExcel() {
        if (!REPORT.raw_data?.respondent_details) {
            alert('Tidak ada data untuk diexport');
            return;
        }

        const data = REPORT.raw_data.respondent_details;
        let csv = 'Nama,Gender,Umur,Desa,Kecamatan,Kabupaten,Latitude,Longitude\n';

        data.forEach(row => {
            csv += `"${row.nama}","${row.jenis_kelamin}","${row.umur}","${row.desa}","${row.kecamatan}","${row.kabupaten}","${row.latitude}","${row.longitude}"\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `Laporan_Data_${new Date().getTime()}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Charts - Multiple Dashboard
    let chartInstances = [];
    function renderChart() {
        if (!REPORT.chart_data || !Array.isArray(REPORT.chart_data)) {
            console.log('No chart data available');
            return;
        }
        console.log('Chart data:', REPORT.chart_data);

        // Clear existing charts
        chartInstances.forEach(chart => chart.destroy());
        chartInstances = [];

        const chartContainer = document.getElementById('chartContainer');
        chartContainer.innerHTML = '';

        // Create charts
        REPORT.chart_data.forEach((chartData, index) => {
            const chartWrapper = document.createElement('div');
            chartWrapper.className = 'bg-gray-800/50 rounded-xl p-4 chart-wrapper';
            chartWrapper.innerHTML = `
                <h3 class="text-sm font-semibold text-gray-300 mb-3">${chartData.title}</h3>
                <div class="relative" style="height: 250px;">
                    <canvas id="chart-${index}"></canvas>
                </div>
            `;
            chartContainer.appendChild(chartWrapper);

            const ctx = document.getElementById(`chart-${index}`).getContext('2d');
            const colors = [
                'rgba(59, 130, 246, 0.85)',
                'rgba(139, 92, 246, 0.85)',
                'rgba(236, 72, 153, 0.85)',
                'rgba(34, 197, 94, 0.85)',
                'rgba(251, 146, 60, 0.85)',
                'rgba(14, 165, 233, 0.85)',
                'rgba(168, 85, 247, 0.85)',
                'rgba(249, 115, 22, 0.85)',
            ];

            const borderColors = colors.map(c => c.replace('0.85', '1'));

            // Map chart type from backend to Chart.js valid types
            let chartType = chartData.type || 'bar';
            let chartOptions = {
                indexAxis: 'x', // default vertical
                responsive: true,
                maintainAspectRatio: true,
                animation: { duration: 1000 },
                plugins: {
                    legend: {
                        display: chartType === 'pie' || chartType === 'doughnut',
                        position: 'bottom',
                        labels: {
                            color: '#e5e7eb',
                            font: { size: 11 },
                            padding: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        padding: 10
                    }
                }
            };

            // Handle different chart types
            if (chartData.type === 'vertical_bar') {
                chartType = 'bar';
                chartOptions.indexAxis = 'x';
            } else if (chartData.type === 'horizontal_bar') {
                chartType = 'bar';
                chartOptions.indexAxis = 'y';
            }

            // Add scales for non-pie charts
            if (chartType !== 'pie' && chartType !== 'doughnut') {
                // For horizontal bar, x is the value axis and y is the category axis
                if (chartOptions.indexAxis === 'y') {
                    chartOptions.scales = {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                color: '#9ca3af',
                                font: { size: 10 }
                            },
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)'
                            }
                        },
                        y: {
                            ticks: {
                                color: '#9ca3af',
                                font: { size: 11 },
                                autoSkip: false
                            },
                            grid: {
                                display: false
                            }
                        }
                    };
                } else {
                    // For vertical bar, y is the value axis and x is the category axis
                    chartOptions.scales = {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#9ca3af',
                                font: { size: 10 }
                            },
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                font: { size: 10 },
                                maxRotation: 45,
                                minRotation: 0,
                                autoSkip: false
                            },
                            grid: {
                                color: 'rgba(75, 85, 99, 0.3)'
                            }
                        }
                    };
                }
            }

            const chartInstance = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: chartData.labels || [],
                    datasets: [{
                        label: chartData.title || 'Data',
                        data: chartData.series || [],
                        backgroundColor: colors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: chartType === 'bar' ? 8 : 0
                    }]
                },
                options: chartOptions
            });

            chartInstances.push(chartInstance);
        });
    }


    // Google Maps
    let mapInstance = null;
    let googleMarkers = [];
    let clusterer = null;

    function renderMap() {
        if (!REPORT.map_data?.markers || REPORT.map_data.markers.length === 0) {
            console.log('No map data available');
            return;
        }
        console.log('Map data:', REPORT.map_data);
        console.log('Total markers:', REPORT.map_data.markers.length);

        // Initialize Google Map if not exists
        if (!mapInstance) {
            // Pusat Kota Jayapura
            const jayapuraCenter = { lat: -2.5333, lng: 140.7167 };
            const center = REPORT.map_data.center
                ? { lat: REPORT.map_data.center[0], lng: REPORT.map_data.center[1] }
                : jayapuraCenter;

            mapInstance = new google.maps.Map(document.getElementById('map'), {
                center: center,
                zoom: REPORT.map_data.zoom || 13,
                mapTypeId: google.maps.MapTypeId.HYBRID, // Default HYBRID
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_RIGHT,
                    mapTypeIds: [
                        google.maps.MapTypeId.ROADMAP,
                        google.maps.MapTypeId.SATELLITE,
                        google.maps.MapTypeId.HYBRID,
                        google.maps.MapTypeId.TERRAIN
                    ]
                },
                streetViewControl: true,
                fullscreenControl: true,
                zoomControl: true
            });
        }

        // Clear existing markers and clusterer
        if (clusterer) {
            clusterer.clearMarkers();
        }
        googleMarkers.forEach(marker => marker.setMap(null));
        googleMarkers = [];

        // Create info window (reuse for all markers)
        const infoWindow = new google.maps.InfoWindow();

        // Add markers
        const bounds = new google.maps.LatLngBounds();

        REPORT.map_data.markers.forEach(m => {
            const position = { lat: m.lat, lng: m.lng };

            const marker = new google.maps.Marker({
                position: position,
                title: m.name || 'Responden',
                animation: google.maps.Animation.DROP
            });

            // Create popup content
            const popupContent = m.popup || `
                <div style="min-width: 200px; padding: 10px;">
                    <strong style="color: #1f2937;">${m.name || 'Responden'}</strong><br>
                    <small style="color: #6b7280;">Lat: ${m.lat.toFixed(6)}, Lng: ${m.lng.toFixed(6)}</small>
                </div>
            `;

            marker.addListener('click', () => {
                infoWindow.setContent(popupContent);
                infoWindow.open(mapInstance, marker);
            });

            googleMarkers.push(marker);
            bounds.extend(position);
        });

        // Initialize MarkerClusterer with custom options
        if (clusterer) {
            clusterer.clearMarkers();
        }

        // Create new clusterer using markerClusterer library
        clusterer = new markerClusterer.MarkerClusterer({
            map: mapInstance,
            markers: googleMarkers,
            algorithm: new markerClusterer.SuperClusterAlgorithm({
                radius: 100,
                maxZoom: 15
            })
        });

        // Fit bounds to show all markers
        if (REPORT.map_data.markers.length > 0) {
            mapInstance.fitBounds(bounds);

            // Adjust zoom if too close
            google.maps.event.addListenerOnce(mapInstance, 'bounds_changed', () => {
                if (mapInstance.getZoom() > 15) {
                    mapInstance.setZoom(15);
                }
            });
        }
    }

    // Print Charts to PDF
    function printCharts() {
        // Ensure all charts are fully rendered before printing
        if (chartInstances.length === 0) {
            alert('Tidak ada chart yang tersedia untuk dicetak');
            return;
        }

        // Convert all canvas charts to images before printing
        const chartContainer = document.getElementById('chartContainer');
        const printContainer = chartContainer.cloneNode(true);

        // Replace each canvas with its image representation
        const canvases = chartContainer.querySelectorAll('canvas');
        const images = printContainer.querySelectorAll('canvas');

        if (canvases.length === 0) {
            alert('Chart belum di-render. Silakan generate laporan terlebih dahulu.');
            return;
        }

        canvases.forEach((canvas, index) => {
            if (images[index]) {
                try {
                    const imgData = canvas.toDataURL('image/png', 1.0);
                    const img = document.createElement('img');
                    img.src = imgData;
                    img.style.width = '100%';
                    img.style.height = 'auto';
                    img.style.maxHeight = '300px';
                    img.style.objectFit = 'contain';
                    images[index].parentNode.replaceChild(img, images[index]);
                } catch (e) {
                    console.error('Error converting chart to image:', e);
                }
            }
        });

        const printWindow = window.open('', '', 'height=800,width=1000');

        printWindow.document.write('<html><head><title>Laporan Charts - Ki Torang Peduli</title>');
        printWindow.document.write(`<style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: Arial, sans-serif; padding: 20px; background: #fff; }
            h1 { text-align: center; margin-bottom: 30px; color: #1f2937; font-size: 24px; }
            .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
            .chart-wrapper {
                border: 2px solid #e5e7eb;
                padding: 15px;
                border-radius: 8px;
                page-break-inside: avoid;
                background: #fff;
            }
            h3 {
                margin: 0 0 15px 0;
                font-size: 14px;
                color: #374151;
                font-weight: 600;
                text-align: center;
            }
            img {
                display: block;
                width: 100%;
                height: auto;
                max-height: 300px;
                object-fit: contain;
                margin: 0 auto;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                color: #6b7280;
                font-size: 12px;
                border-top: 1px solid #e5e7eb;
                padding-top: 15px;
            }
            @media print {
                body { padding: 10px; }
                .grid { gap: 15px; }
                .chart-wrapper {
                    page-break-inside: avoid;
                    margin-bottom: 10px;
                    break-inside: avoid;
                }
                @page {
                    margin: 1.5cm;
                }
            }
        </style>`);
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h1>Laporan Charts - Ki Torang Peduli</h1>');
        printWindow.document.write('<div class="grid">' + printContainer.innerHTML + '</div>');
        printWindow.document.write('<div class="footer">');
        printWindow.document.write('Generated on ' + new Date().toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }));
        printWindow.document.write('</div>');
        printWindow.document.write('</body></html>');

        printWindow.document.close();

        // Wait for images to load before printing
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 1500);
    }

    // Print Map to PDF
    function printMap() {
        // Trigger resize event for Google Maps before printing
        if (mapInstance) {
            setTimeout(() => {
                google.maps.event.trigger(mapInstance, 'resize');

                // Wait for map to fully render
                setTimeout(() => {
                    printWindow.focus();
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }, 100);
        } else {
            window.print();
        }
    }

    // Clear prompt function
    function clearPrompt() {
        const promptInput = document.getElementById('promptInput');
        promptInput.value = '';
        promptInput.focus();
    }

    // Append idea to prompt (instead of replacing)
    function appendToPrompt(idea) {
        const promptInput = document.getElementById('promptInput');
        const currentValue = promptInput.value.trim();

        if (currentValue === '') {
            // If empty, just set the idea
            promptInput.value = idea;
        } else {
            // If has content, append with separator
            promptInput.value = currentValue + ' ' + idea;
        }

        // Focus and scroll to end
        promptInput.focus();
        promptInput.scrollTop = promptInput.scrollHeight;
    }

    // Form submission loading modal
    document.getElementById('reportForm').addEventListener('submit', function() {
        document.getElementById('loadingModal').style.display = 'flex';
    });

    // Text-to-Speech functionality with Google Translate TTS
    let speechSynthesis = window.speechSynthesis;
    let currentUtterance = null;
    let isPlaying = false;
    let availableVoices = [];
    let selectedVoice = null;
    let currentAudioQueue = [];
    let currentAudioIndex = 0;

    // Google TTS - No need for browser voice detection

    function toggleTTS() {
        if (isPlaying) {
            pauseTTS();
        } else {
            playTTS();
        }
    }

    function playTTS() {
        const textElement = document.querySelector('#teks .text-gray-200');
        if (!textElement) return;

        // Get text and clean it from HTML tags
        let text = textElement.textContent;
        text = text.replace(/<br\s*\/?>/gi, ' ');
        text = text.replace(/\s+/g, ' ').trim();

        // Use Google Translate TTS - ALWAYS (tidak tergantung browser/OS)
        playGoogleTTS(text);
    }

    // Backend TTS - No CORS issues
    async function playGoogleTTS(text) {
        // Stop any currently playing audio
        stopGoogleTTS();

        // Split text into chunks (TTS has character limit ~200)
        const chunks = splitTextIntoChunks(text, 200);

        isPlaying = true;
        updateTTSButton(true);
        startVoiceVisualizer();

        // Create audio elements for each chunk using backend proxy
        currentAudioQueue = await Promise.all(chunks.map(async chunk => {
            const audio = new Audio();

            // Use backend TTS endpoint (no CORS issues)
            const formData = new FormData();
            formData.append('text', chunk);

            try {
                const response = await fetch('/api/tts/generate', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (response.ok) {
                    const blob = await response.blob();
                    audio.src = URL.createObjectURL(blob);
                } else {
                    console.error('TTS API error:', response.status);
                }
            } catch (err) {
                console.error('Failed to fetch TTS:', err);
            }

            return audio;
        }));

        // Play chunks sequentially
        currentAudioIndex = 0;
        playNextAudioChunk();
    }

    function playNextAudioChunk() {
        if (currentAudioIndex >= currentAudioQueue.length) {
            // All chunks played
            isPlaying = false;
            updateTTSButton(false);
            stopVoiceVisualizer();
            currentAudioQueue = [];
            return;
        }

        const audio = currentAudioQueue[currentAudioIndex];

        audio.onended = () => {
            currentAudioIndex++;
            playNextAudioChunk();
        };

        audio.onerror = (e) => {
            console.error('Audio playback error:', e);
            currentAudioIndex++;
            playNextAudioChunk();
        };

        audio.play().catch(err => {
            console.error('Failed to play audio:', err);
            currentAudioIndex++;
            playNextAudioChunk();
        });
    }

    function stopGoogleTTS() {
        // Stop all audio in queue
        currentAudioQueue.forEach(audio => {
            audio.pause();
            audio.currentTime = 0;
        });
        currentAudioQueue = [];
        currentAudioIndex = 0;
    }

    function splitTextIntoChunks(text, maxLength) {
        const sentences = text.match(/[^.!?]+[.!?]+/g) || [text];
        const chunks = [];
        let currentChunk = '';

        sentences.forEach(sentence => {
            if ((currentChunk + sentence).length > maxLength) {
                if (currentChunk) chunks.push(currentChunk.trim());
                currentChunk = sentence;
            } else {
                currentChunk += ' ' + sentence;
            }
        });

        if (currentChunk) chunks.push(currentChunk.trim());
        return chunks;
    }

    function pauseTTS() {
        // Pause current audio
        if (currentAudioQueue[currentAudioIndex]) {
            currentAudioQueue[currentAudioIndex].pause();
        }
        isPlaying = false;
        updateTTSButton(false);
        stopVoiceVisualizer();
    }

    function stopTTS() {
        stopGoogleTTS();
        isPlaying = false;
        updateTTSButton(false);
        stopVoiceVisualizer();
    }

    function updateTTSButton(playing) {
        const btn = document.getElementById('ttsBtn');
        const text = document.getElementById('ttsText');
        const stopBtn = document.getElementById('ttsStop');

        if (playing) {
            text.textContent = 'Jeda';
            stopBtn.classList.remove('hidden');
            stopBtn.style.display = 'flex';
        } else {
            text.textContent = 'Dengarkan';
            stopBtn.classList.add('hidden');
        }
    }

    function startVoiceVisualizer() {
        const visualizer = document.getElementById('voiceVisualizer');
        visualizer.classList.remove('hidden');
        visualizer.style.display = 'flex';

        // Animate bars
        const bars = visualizer.querySelectorAll('.voice-bar');
        bars.forEach((bar, index) => {
            animateBar(bar, index);
        });
    }

    function animateBar(bar, index) {
        const animate = () => {
            if (!isPlaying) {
                bar.style.height = '8px';
                return;
            }

            const randomHeight = Math.random() * 32 + 8; // 8-40px
            bar.style.height = randomHeight + 'px';
            bar.style.transition = 'height 0.1s ease';

            setTimeout(animate, 100 + index * 20);
        };
        animate();
    }

    function stopVoiceVisualizer() {
        const visualizer = document.getElementById('voiceVisualizer');
        visualizer.classList.add('hidden');

        const bars = visualizer.querySelectorAll('.voice-bar');
        bars.forEach(bar => {
            bar.style.height = '8px';
        });
    }
    </script>

    <style>
        .voice-bar, .voice-input-bar {
            height: 8px;
            animation: pulse 0.6s ease-in-out infinite;
        }

        .voice-bar:nth-child(1), .voice-input-bar:nth-child(1) { animation-delay: 0s; }
        .voice-bar:nth-child(2), .voice-input-bar:nth-child(2) { animation-delay: 0.1s; }
        .voice-bar:nth-child(3), .voice-input-bar:nth-child(3) { animation-delay: 0.2s; }
        .voice-bar:nth-child(4), .voice-input-bar:nth-child(4) { animation-delay: 0.3s; }
        .voice-bar:nth-child(5), .voice-input-bar:nth-child(5) { animation-delay: 0.4s; }
        .voice-bar:nth-child(6) { animation-delay: 0.5s; }
        .voice-bar:nth-child(7) { animation-delay: 0.6s; }
        .voice-bar:nth-child(8) { animation-delay: 0.7s; }

        @keyframes pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        #loadingModal {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .recording {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.6) !important;
        }

        @media print {
            /* Hide everything except map */
            body * {
                visibility: hidden !important;
            }

            /* Show map container and all children */
            #map, #map * {
                visibility: visible !important;
            }

            /* Make map full page */
            #map {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                height: 100vh !important;
                z-index: 9999 !important;
            }

            /* Ensure Leaflet tiles are visible */
            .leaflet-container,
            .leaflet-pane,
            .leaflet-tile-pane,
            .leaflet-tile,
            .leaflet-marker-icon,
            .leaflet-marker-pane,
            .leaflet-popup-pane {
                visibility: visible !important;
                display: block !important;
            }

            /* Hide controls during print */
            .leaflet-control-container {
                display: none !important;
            }
        }
    </style>

    <script>
        // Close initial loading modal when page is fully loaded
        window.addEventListener('load', function() {
            const initialLoadModal = document.getElementById('initialLoadModal');
            if (initialLoadModal) {
                // Add fade out animation
                initialLoadModal.style.transition = 'opacity 0.5s ease-out';
                initialLoadModal.style.opacity = '0';

                // Remove from DOM after animation
                setTimeout(() => {
                    initialLoadModal.remove();
                }, 500);
            }
        });
    </script>
</body>
</html>
