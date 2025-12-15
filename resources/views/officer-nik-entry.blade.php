@extends('layouts.app')

@section('title', 'Input NIK - Portal Officer')

@push('styles')
<style>
    .glass-card {
        border: 1px solid rgba(148, 163, 184, 0.35);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.04);
        box-shadow: 0 20px 50px rgba(0,0,0,0.25);
    }
    .glass-card.dark {
        border-color: rgba(148, 163, 184, 0.4);
        background: rgba(17, 24, 39, 0.85);
    }
</style>
@endpush

@section('content')
<div class="bg-gray-900 text-gray-100 py-10 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 space-y-8">
        <!-- Back Button -->
        <a href="{{ route('officer.entry') }}" class="inline-flex items-center gap-2 text-amber-400 hover:text-amber-300 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Kuesioner
        </a>

        <!-- Questionnaire Info -->
        <div class="glass-card dark p-6">
            <div class="flex items-start gap-4">
                <div class="bg-amber-500 rounded-lg p-3">
                    <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-white mb-2">{{ $questionnaire->title }}</h1>
                    <p class="text-gray-300 text-sm mb-3">{{ $questionnaire->description }}</p>
                    <div class="flex flex-wrap gap-3 text-sm">
                        <span class="bg-gray-800 px-3 py-1 rounded-full text-gray-300">
                            🏛️ {{ $questionnaire->opd->name ?? 'Umum' }}
                        </span>
                        <span class="bg-amber-500/20 text-amber-300 px-3 py-1 rounded-full">
                            📝 {{ $questionnaire->questions()->count() }} pertanyaan
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Officer Info -->
        <div class="glass-card dark p-5">
            <div class="text-xs uppercase tracking-wide text-gray-400 mb-2">Petugas</div>
            <div class="text-white font-semibold">{{ $user->name }}</div>
            <div class="text-gray-300 text-sm">{{ $user->email }} • {{ $user->opd->name ?? 'Semua OPD' }}</div>
        </div>

        <!-- NIK Entry Form -->
        <form method="POST" action="{{ route('officer.entry.store') }}" class="space-y-6" novalidate>
            @csrf
            <input type="hidden" name="questionnaire_id" value="{{ $questionnaire->id }}">

            <!-- Protection Info Alert -->
            <div class="glass-card border border-green-700/50 bg-gradient-to-r from-green-900/30 to-green-800/20 p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-green-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm">
                        <p class="text-green-300 font-semibold">Perlindungan Data Responden</p>
                        <p class="text-gray-300 text-xs mt-1">Jika responden sudah menyelesaikan kuesioner ini, sistem akan mencegah Anda mengisinya kembali. Data responden terlindungi dari perubahan.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- NIK Input Card -->
                <div class="glass-card dark p-6 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Masukkan NIK Responden</h3>
                        <p class="text-sm text-gray-400">Ketik atau pilih dari 5 NIK terakhir yang Anda input.</p>
                    </div>

                    <div class="space-y-3">
                        <label for="nik" class="text-sm font-semibold text-white">NIK Responden</label>
                        <input
                            id="nik"
                            name="nik"
                            type="text"
                            list="nik-options"
                            maxlength="16"
                            inputmode="numeric"
                            autocomplete="off"
                            value="{{ old('nik') }}"
                            class="block w-full rounded-lg border-2 border-gray-500 bg-white px-4 py-3 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500"
                            placeholder="Ketik atau pilih NIK 16 digit"
                        />
                        <datalist id="nik-options">
                            @foreach ($nikOptions as $opt)
                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                            @endforeach
                        </datalist>
                        @error('nik')
                            <p class="text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <div id="nik-preview" class="rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-gray-200 min-h-[60px]">
                            <span class="text-gray-400">Mulai ketik NIK untuk melihat info singkat.</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-bold px-6 py-3 rounded-lg shadow-lg transition">
                        Mulai Isi Kuesioner →
                    </button>
                </div>

                <!-- Register New Respondent Card -->
                <div class="glass-card dark p-6 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">NIK Belum Terdaftar?</h3>
                        <p class="text-sm text-gray-400">Daftarkan responden terlebih dahulu sebelum mengisi kuesioner.</p>
                    </div>

                    <div class="bg-gray-800/50 rounded-lg p-4 space-y-2 border border-gray-700">
                        <div class="flex items-start gap-2">
                            <span class="text-amber-400 mt-0.5">1.</span>
                            <p class="text-sm text-gray-300">Klik tombol "Daftarkan Responden Baru" di bawah</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-amber-400 mt-0.5">2.</span>
                            <p class="text-sm text-gray-300">Isi data lengkap responden (NIK, Nama, dll)</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-amber-400 mt-0.5">3.</span>
                            <p class="text-sm text-gray-300">Kembali ke halaman ini dan masukkan NIK yang baru didaftarkan</p>
                        </div>
                    </div>

                    <a href="{{ route('officer.respondent.create', ['questionnaire_id' => $questionnaire->id]) }}"
                       class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition border border-green-500">
                        + Daftarkan Responden Baru
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </a>

                    <p class="text-xs text-gray-400 text-center">Akan dibuka di halaman baru. Setelah selesai, kembali ke halaman ini.</p>
            </div>
        </form>
    </div>
</div>

<!-- Top Respondents List Section - FULL WIDTH -->
@if($topRespondents && count($topRespondents) > 0)
<div class="bg-gray-800/30 py-10">
    <div class="max-w-7xl mx-auto px-4 space-y-8">
        <div class="mt-0">
            <div class="flex items-center gap-3 mb-6">
                <h3 class="text-xl font-bold text-white">
                    <span class="text-green-400">👥</span> Responden Terbaru (Kuesioner Ini)
                </h3>
                <span class="bg-green-500/30 text-green-300 text-xs font-bold px-3 py-1 rounded-full">{{ count($topRespondents) }}</span>
            </div>

            <!-- Search Top Respondents -->
            <div class="mb-6">
                <form method="GET" action="{{ route('officer.entry.questionnaire', $questionnaire->id) }}" class="flex gap-2">
                    <input type="text"
                           name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Cari berdasarkan NIK atau Nama..."
                           class="flex-1 rounded-lg border-2 border-gray-600 bg-gray-800 px-4 py-2 text-sm text-white placeholder-gray-500 focus:border-green-500 focus:ring-2 focus:ring-green-500">
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('officer.entry.questionnaire', $questionnaire->id) }}"
                           class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Respondents List -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-green-500/20 border-b border-green-500/30">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-green-300">Nama</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-green-300">NIK</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-green-300">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-green-300">Diperbarui</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-green-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $visibleCount = 10;
                            $totalCount = count($topRespondents);
                        @endphp
                        @foreach($topRespondents as $index => $entry)
                            @if($index < $visibleCount)
                                <tr class="border-b border-gray-700 hover:bg-gray-800/50 transition {{ $index % 2 === 0 ? 'bg-gray-900/30' : 'bg-gray-800/10' }}">
                                    <td class="px-4 py-3 text-sm font-medium text-white">{{ $entry->respondent->nama_lengkap ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $entry->respondent->nik ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($entry->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-500/30 text-green-300 text-xs font-bold rounded">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-500/30 text-amber-300 text-xs font-bold rounded">
                                                <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Proses
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500">{{ $entry->updated_at?->diffForHumans() ?? 'Baru saja' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if($entry->status === 'in_progress')
                                            <a href="{{ route('questionnaire.start', ['id' => $questionnaire->id]) }}"
                                               class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded transition inline-block">
                                                Lanjutkan
                                            </a>
                                        @else
                                            <span class="text-gray-600 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($totalCount > $visibleCount)
                <div class="mt-4 text-center">
                    <button type="button" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold rounded transition" onclick="alert('Showing top 10. Load more function coming soon!')">
                        📋 Tampilkan {{ $totalCount - $visibleCount }} data lagi
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nikInput = document.getElementById('nik');
        const nikPreview = document.getElementById('nik-preview');
        const nikOptions = @json($nikOptions);
        const nikMap = new Map(nikOptions.map((o) => [o.value, o]));

        function updateNikPreview(value) {
            if (!nikPreview) return;
            const trimmed = (value || '').trim();
            if (!trimmed) {
                nikPreview.innerHTML = '<span class="text-gray-400">Mulai ketik NIK untuk melihat info singkat.</span>';
                return;
            }
            const opt = nikMap.get(trimmed);
            if (opt) {
                nikPreview.innerHTML = `<div class="font-semibold text-white text-base">${opt.value}</div><div class="text-sm text-gray-300 mt-1">${opt.meta}</div>${opt.subtitle ? `<div class="text-xs text-gray-400 mt-1">${opt.subtitle}</div>` : ''}`;
                return;
            }
            if (trimmed.length === 16) {
                nikPreview.innerHTML = '<span class="text-green-400">✓ NIK akan dicek saat Anda mulai mengisi kuesioner.</span>';
            } else {
                nikPreview.innerHTML = `<span class="text-amber-400">⚠ Lengkapi 16 digit NIK (${trimmed.length}/16)</span>`;
            }
        }

        if (nikInput) {
            updateNikPreview(nikInput.value || '');
            nikInput.addEventListener('input', (e) => updateNikPreview(e.target.value));
            nikInput.addEventListener('change', (e) => updateNikPreview(e.target.value));

            // Only allow numeric input
            nikInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush
