@extends('layouts.app')

@section('title', 'Masuk - KitorangPeduli.id')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-yellow-500">
            <div class="papua-gradient px-8 py-6">
                <h2 class="text-2xl font-bold text-yellow-400 text-center">Masuk ke Akun</h2>
                <p class="text-gray-300 text-center text-sm mt-1">Gunakan nomor HP yang terdaftar</p>
            </div>

            <div class="p-8">
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        @foreach($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.send-otp') }}" method="POST" id="loginForm">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Nomor HP (WhatsApp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">+62</span>
                            <input type="tel" name="no_hp" id="no_hp"
                                   value="{{ old('no_hp') }}"
                                   class="w-full pl-14 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                   placeholder="8123456789"
                                   pattern="[0-9]{9,13}"
                                   required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Contoh: 81234567890 (tanpa 0 di depan)</p>
                    </div>

                    <button type="submit"
                            class="w-full bg-gray-900 text-yellow-400 py-3 rounded-lg font-medium hover:bg-black transition flex items-center justify-center border border-yellow-500">
                        <span>Kirim Kode OTP</span>
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm">
                        Belum punya akun?
                        <a href="{{ route('register') }}{{ request('intended') ? '?intended=' . request('intended') . '&id=' . request('id') : '' }}"
                           class="text-yellow-600 font-medium hover:text-yellow-500 underline">
                            Daftar Sekarang
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-gray-600 text-sm hover:text-gray-800">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
