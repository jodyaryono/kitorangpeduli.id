@props([
    'status' => 'empty',
    'message' => '',
    'provinceCode' => '',
    'regencyCode' => '',
    'districtCode' => '',
    'gender' => '',
    'birthDate' => '',
    'registrationNumber' => '',
    'currentLength' => 0,
])

@if($status === 'empty')
<div class="flex items-center gap-3 p-4 bg-gray-500/10 border border-gray-500/30 rounded-lg">
    <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
        </svg>
    </div>
    <div>
        <p class="text-gray-400 font-medium">{{ $message }}</p>
        <p class="text-gray-500 text-sm">Format: PPKKCC DDMMYY NNNN</p>
    </div>
</div>

@elseif($status === 'incomplete')
<div class="flex items-center gap-3 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
    <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-yellow-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-yellow-500 font-medium">{{ $message }}</p>
        <div class="mt-2 w-full bg-gray-700 rounded-full h-2">
            <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300" style="width: {{ ($currentLength / 16) * 100 }}%"></div>
        </div>
    </div>
</div>

@elseif($status === 'error')
<div class="flex items-center gap-3 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
    <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <div>
        <p class="text-red-500 font-medium">{{ $message }}</p>
    </div>
</div>

@elseif($status === 'success')
<div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-green-500 font-semibold">NIK Valid - Data berhasil diparsing</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
        {{-- Kode Provinsi --}}
        <div class="bg-gray-800/50 rounded-lg p-3 text-center border border-gray-700">
            <div class="text-xs text-gray-400 mb-1">Provinsi</div>
            <div class="text-xl font-bold text-blue-400 font-mono">{{ $provinceCode }}</div>
        </div>

        {{-- Kode Kabupaten/Kota --}}
        <div class="bg-gray-800/50 rounded-lg p-3 text-center border border-gray-700">
            <div class="text-xs text-gray-400 mb-1">Kab/Kota</div>
            <div class="text-xl font-bold text-purple-400 font-mono">{{ $regencyCode }}</div>
        </div>

        {{-- Kode Kecamatan --}}
        <div class="bg-gray-800/50 rounded-lg p-3 text-center border border-gray-700">
            <div class="text-xs text-gray-400 mb-1">Kecamatan</div>
            <div class="text-xl font-bold text-indigo-400 font-mono">{{ $districtCode }}</div>
        </div>

        {{-- Jenis Kelamin --}}
        <div class="bg-gray-800/50 rounded-lg p-3 text-center border border-gray-700">
            <div class="text-xs text-gray-400 mb-1">Jenis Kelamin</div>
            <div class="text-lg font-bold font-mono {{ $gender === 'Perempuan' ? 'text-pink-400' : 'text-cyan-400' }}">
                {{ $gender === 'Perempuan' ? '♀' : '♂' }} {{ $gender }}
            </div>
        </div>

        {{-- Tanggal Lahir --}}
        <div class="bg-gray-800/50 rounded-lg p-3 text-center border border-gray-700">
            <div class="text-xs text-gray-400 mb-1">Tanggal Lahir</div>
            <div class="text-sm font-bold text-orange-400">{{ $birthDate }}</div>
        </div>

        {{-- No Urut --}}
        <div class="bg-gray-800/50 rounded-lg p-3 text-center border border-gray-700">
            <div class="text-xs text-gray-400 mb-1">No. Urut</div>
            <div class="text-xl font-bold text-gray-300 font-mono">{{ $registrationNumber }}</div>
        </div>
    </div>
</div>
@endif
