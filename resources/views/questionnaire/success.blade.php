@extends('layouts.app')

@section('title', 'Survey Berhasil - KitorangPeduli.id')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-lg w-full text-center">
        <div class="bg-white rounded-2xl shadow-xl p-8 border-t-4 border-yellow-500">
            <div class="w-24 h-24 bg-yellow-400 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-2">Terima Kasih!</h1>
            <p class="text-gray-600 mb-6">Jawaban Anda telah berhasil disimpan.</p>

            <div class="bg-gray-900 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-300">
                    <strong class="text-yellow-400">{{ $response->questionnaire->title }}</strong><br>
                    <span class="text-gray-400">Diselesaikan pada {{ $response->completed_at->format('d M Y, H:i') }} WIT</span>
                </p>
            </div>

            <div class="space-y-3">
                @if(isset($isOfficerAssisted) && $isOfficerAssisted)
                    <a href="{{ route('officer.entry') }}"
                       class="block w-full bg-amber-500 text-black py-3 rounded-lg font-semibold hover:bg-amber-400 transition shadow-lg">
                        Isi Kuesioner Lain (Responden Baru)
                    </a>
                    <a href="{{ url('/admin') }}"
                       class="block w-full bg-gray-100 text-gray-900 py-3 rounded-lg font-medium hover:bg-white transition border border-gray-300">
                        Kembali ke Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('home') }}"
                       class="block w-full bg-gray-900 text-yellow-400 py-3 rounded-lg font-medium hover:bg-black transition border border-yellow-500">
                        Kembali ke Beranda
                    </a>
                @endif
                <div class="flex justify-center gap-2 mt-4">
                    <span class="w-6 h-1 bg-black rounded"></span>
                    <span class="w-6 h-1 bg-yellow-500 rounded"></span>
                    <span class="w-6 h-1 bg-red-600 rounded"></span>
                </div>
                <p class="text-gray-500 text-sm mt-2">
                    {{ isset($isOfficerAssisted) && $isOfficerAssisted ? 'Data berhasil diinput oleh petugas' : 'Kontribusi Anda sangat berarti untuk pembangunan Kota Jayapura' }} 🦅
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
