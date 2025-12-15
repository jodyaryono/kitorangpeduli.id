<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KitorangPeduli.id - Survey Papua')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        papua: {
                            black: '#1a1a1a',
                            yellow: '#FFD700',
                            red: '#DC2626',
                            dark: '#0d0d0d',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .papua-gradient { background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%); }
        .papua-gradient-gold { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); }
        .papua-text-gradient { background: linear-gradient(135deg, #FFD700, #FFA500); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* Custom animations for registration form */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-step.active {
            animation: slideIn 0.3s ease-out;
        }

        /* Input focus effects */
        input:focus, select:focus, textarea:focus {
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        /* Custom checkbox/radio styling */
        .gender-option:has(input:checked) {
            border-color: #FFD700 !important;
            background-color: #FEF3C7 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="papua-gradient shadow-xl border-b-4 border-yellow-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <span class="text-2xl mr-2">🦅</span>
                        <span class="text-yellow-400 text-xl font-bold">Kitorang</span>
                        <span class="text-white text-xl font-bold">Peduli</span>
                        <span class="text-red-500 text-xl font-bold">.id</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @if(session('respondent'))
                        <span class="text-yellow-400 text-sm font-medium">{{ session('respondent.nama_lengkap') }}</span>
                        <a href="{{ route('logout') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition border border-red-500">
                            Keluar
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-yellow-400 hover:text-yellow-300 text-sm font-medium transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="bg-yellow-500 text-black px-4 py-2 rounded-lg text-sm font-bold hover:bg-yellow-400 transition">
                            Daftar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="papua-gradient text-white py-8 mt-12 border-t-4 border-yellow-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="flex items-center justify-center mb-3">
                    <span class="text-3xl mr-2">🦅</span>
                    <span class="text-yellow-400 text-xl font-bold">Kitorang</span>
                    <span class="text-white text-xl font-bold">Peduli</span>
                    <span class="text-red-500 text-xl font-bold">.id</span>
                </div>
                <p class="text-gray-400 text-sm mt-2">Platform Survey untuk Pembangunan Kota Jayapura yang Lebih Baik</p>
                <div class="flex justify-center gap-2 mt-4">
                    <span class="w-8 h-2 bg-black rounded"></span>
                    <span class="w-8 h-2 bg-yellow-500 rounded"></span>
                    <span class="w-8 h-2 bg-red-600 rounded"></span>
                </div>
                <p class="text-gray-500 text-xs mt-4">&copy; {{ date('Y') }} KitorangPeduli.id. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Toast Notifications -->
    @if(session('success'))
    <div id="toast-success" class="fixed bottom-4 right-4 bg-yellow-500 text-black px-6 py-3 rounded-lg shadow-lg z-50 font-medium">
        ✓ {{ session('success') }}
    </div>
    <script>setTimeout(() => document.getElementById('toast-success').remove(), 3000);</script>
    @endif

    @if(session('error'))
    <div id="toast-error" class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 font-medium">
        ✕ {{ session('error') }}
    </div>
    <script>setTimeout(() => document.getElementById('toast-error').remove(), 3000);</script>
    @endif

    @stack('scripts')
</body>
</html>
