@extends('layouts.app')

@section('title', 'Verifikasi OTP - KitorangPeduli.id')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-yellow-500">
            <div class="papua-gradient px-8 py-6">
                <h2 class="text-2xl font-bold text-yellow-400 text-center">Verifikasi OTP</h2>
                <p class="text-gray-300 text-center text-sm mt-1">Masukkan kode yang dikirim ke WhatsApp</p>
            </div>

            <div class="p-8">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <p class="text-sm">Kode OTP telah dikirim ke <strong>+{{ $no_hp }}</strong></p>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        @foreach($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.verify-otp') }}" method="POST">
                    @csrf
                    <input type="hidden" name="no_hp" value="{{ $no_hp }}">

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Kode OTP (6 digit)</label>
                        <input type="text" name="otp"
                               class="w-full px-4 py-4 text-center text-2xl font-bold tracking-widest border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                               placeholder="000000"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               autofocus
                               required>
                    </div>

                    <button type="submit"
                            class="w-full bg-gray-900 text-yellow-400 py-3 rounded-lg font-medium hover:bg-black transition border border-yellow-500">
                        Verifikasi & Masuk
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600 text-sm mb-2">Tidak menerima kode?</p>
                    <form action="{{ route('login.send-otp') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="no_hp" value="{{ $no_hp }}">
                        <button type="submit" class="text-yellow-600 font-medium hover:text-yellow-500 text-sm">
                            Kirim Ulang OTP
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-gray-600 text-sm hover:text-gray-800">
                ← Ganti Nomor HP
            </a>
        </div>
    </div>
</div>
@endsection
