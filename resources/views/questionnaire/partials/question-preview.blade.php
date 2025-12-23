<div class="bg-white rounded-xl shadow-md p-6 border-l-4 {{ $indent }}
    @if($question->is_required) border-red-400 @else border-blue-400 @endif">

    <!-- Question Header -->
    <div class="flex items-start justify-between mb-4">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                    {{ $questionNumber }}
                </span>
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $question->question_text }}
                    @if($question->is_required)
                        <span class="text-red-500">*</span>
                    @endif
                </h3>
            </div>

            @if($question->description)
                <p class="text-sm text-gray-600 ml-11">{{ $question->description }}</p>
            @endif
        </div>

        <span class="px-3 py-1 text-xs font-medium rounded-full
            @if($question->question_type === 'text') bg-green-100 text-green-800
            @elseif($question->question_type === 'single_choice') bg-purple-100 text-purple-800
            @elseif($question->question_type === 'multiple_choice') bg-pink-100 text-pink-800
            @elseif($question->question_type === 'dropdown') bg-indigo-100 text-indigo-800
            @elseif($question->question_type === 'province') bg-blue-100 text-blue-800
            @elseif($question->question_type === 'regency') bg-blue-100 text-blue-800
            @elseif($question->question_type === 'district') bg-blue-100 text-blue-800
            @elseif($question->question_type === 'village') bg-blue-100 text-blue-800
            @elseif($question->question_type === 'puskesmas') bg-teal-100 text-teal-800
            @elseif($question->question_type === 'field_officer') bg-cyan-100 text-cyan-800
            @elseif($question->question_type === 'scale') bg-yellow-100 text-yellow-800
            @elseif($question->question_type === 'date') bg-orange-100 text-orange-800
            @elseif($question->question_type === 'location') bg-red-100 text-red-800
            @elseif($question->question_type === 'file') bg-gray-100 text-gray-800
            @else bg-gray-100 text-gray-800
            @endif">
            {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
        </span>
    </div>

    <!-- Question Body based on Type -->
    <div class="ml-11">
        @switch($question->question_type)
            @case('text')
            @case('textarea')
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                       placeholder="Jawaban..." disabled>
                @break

            @case('single_choice')
            @case('dropdown')
                <div class="space-y-2">
                    @foreach($question->options as $option)
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="question_{{ $question->id }}" class="text-blue-600" disabled>
                            <span>{{ $option->option_text }}</span>
                        </label>
                    @endforeach
                </div>
                @break

            @case('multiple_choice')
                <div class="space-y-2">
                    @foreach($question->options as $option)
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" class="text-blue-600 rounded" disabled>
                            <span>{{ $option->option_text }}</span>
                        </label>
                    @endforeach
                </div>
                @break

            @case('province')
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
                    <option>Pilih Provinsi...</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">📍 Data dari tabel wilayah (cascade)</p>
                @break

            @case('regency')
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
                    <option>Pilih Kabupaten/Kota...</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">📍 Data dari tabel wilayah (cascade dari Provinsi)</p>
                @break

            @case('district')
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
                    <option>Pilih Kecamatan...</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">📍 Data dari tabel wilayah (cascade dari Kabupaten)</p>
                @break

            @case('village')
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
                    <option>Pilih Kelurahan/Desa...</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">📍 Data dari tabel wilayah (cascade dari Kecamatan)</p>
                @break

            @case('puskesmas')
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
                    <option>Pilih Puskesmas...</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">🏥 Data dari tabel puskesmas (lookup)</p>
                @break

            @case('field_officer')
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
                    <option>Pilih Nama Pencacah...</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">👤 Data dari tabel users dengan role field_officer (lookup)</p>
                @break

            @case('scale')
                <div class="flex items-center gap-2">
                    @for($i = 1; $i <= ($question->scale_max ?? 5); $i++)
                        <button class="w-12 h-12 border-2 border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500" disabled>
                            {{ $i }}
                        </button>
                    @endfor
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                    <span>{{ $question->scale_min_label ?? 'Min' }}</span>
                    <span>{{ $question->scale_max_label ?? 'Max' }}</span>
                </div>
                @break

            @case('date')
                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" disabled>
                @break

            @case('file')
            @case('image')
            @case('video')
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">Upload {{ $question->question_type }}</p>
                </div>
                @break

            @case('location')
                <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-gray-600">Lokasi GPS</span>
                    </div>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-white text-sm"
                           placeholder="Latitude, Longitude" disabled>
                </div>
                @break

            @case('family_members')
                <div class="space-y-4">
                    <!-- List Anggota Keluarga -->
                    <div class="border-2 border-gray-300 rounded-lg bg-white p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-700">Daftar Anggota Keluarga</h4>
                            <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700" disabled>
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                </svg>
                                Tambah Anggota
                            </button>
                        </div>

                        <!-- Preview 3 Family Members -->
                        @for($i = 1; $i <= 3; $i++)
                        <div class="border border-gray-200 rounded-lg p-4 mb-3 bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-gray-800">Anggota {{ $i }}</h5>
                                            <p class="text-sm text-gray-500">NIK: 16 digit</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm mt-3">
                                        <div>
                                            <span class="text-gray-500">Hub. Keluarga:</span>
                                            <span class="font-medium text-gray-800 ml-2">-</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Jenis Kelamin:</span>
                                            <span class="font-medium text-gray-800 ml-2">-</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Tanggal Lahir:</span>
                                            <span class="font-medium text-gray-800 ml-2">-</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Umur:</span>
                                            <span class="font-medium text-gray-800 ml-2">- tahun</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Status Kawin:</span>
                                            <span class="font-medium text-gray-800 ml-2">-</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Agama:</span>
                                            <span class="font-medium text-gray-800 ml-2">-</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Pendidikan:</span>
                                            <span class="font-medium text-gray-800 ml-2 italic">📚 lookup</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Pekerjaan:</span>
                                            <span class="font-medium text-gray-800 ml-2 italic">💼 lookup</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-2 ml-4">
                                    <button class="p-2 text-blue-600 hover:bg-blue-50 rounded" disabled title="Edit">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <button class="p-2 text-red-600 hover:bg-red-50 rounded" disabled title="Hapus">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endfor

                        <div class="text-center py-3 text-sm text-gray-500">
                            Total: 3 anggota keluarga
                        </div>
                    </div>

                    <div class="flex items-start gap-2 text-xs text-gray-600 bg-blue-50 p-3 rounded-lg">
                        <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-800">Info:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Entry per anggota keluarga satu-persatu dengan form</li>
                                <li>Data tersimpan ke table 'residents' dengan family_id yang sama</li>
                                <li>Pendidikan & Pekerjaan menggunakan lookup dari database</li>
                                <li>Dapat edit dan hapus per anggota</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @break

            @case('health_per_member')
                <div class="space-y-4">
                    <!-- Health Check per Member -->
                    <div class="bg-gradient-to-r from-green-50 to-teal-50 border-2 border-green-300 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            <h4 class="font-semibold text-gray-800">Pertanyaan Kesehatan Per Anggota Keluarga</h4>
                        </div>

                        <p class="text-sm text-gray-600 mb-4">
                            Pertanyaan ini akan ditampilkan untuk setiap anggota keluarga yang sesuai dengan kriteria
                        </p>

                        <!-- Example Member Cards -->
                        @for($i = 1; $i <= 2; $i++)
                        <div class="bg-white border border-green-200 rounded-lg p-4 mb-3">
                            <div class="flex items-center gap-3 mb-3 pb-3 border-b">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h6 class="font-semibold text-gray-800">Anggota {{ $i }}</h6>
                                    <p class="text-xs text-gray-500">Umur: {{ $i == 1 ? '25' : '8' }} tahun</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="pl-4 border-l-2 border-green-300">
                                    <p class="text-xs font-medium text-gray-700 mb-2">✓ Semua umur:</p>
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="radio" disabled> Ya / Tidak - Memiliki JKN?
                                    </label>
                                </div>

                                @if($i == 1)
                                <div class="pl-4 border-l-2 border-blue-300">
                                    <p class="text-xs font-medium text-gray-700 mb-2">✓ Umur ≥15 tahun:</p>
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="radio" disabled> Ya / Tidak - Merokok?
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endfor
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 p-3 rounded text-xs">
                        <strong>Info:</strong> Conditional per umur/gender. Settings: age_min, age_max, gender, condition
                    </div>
                </div>
                @break

            @default
                <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                       placeholder="Jawaban..." disabled>
        @endswitch

        @if($question->validation_rules)
            <div class="mt-3 text-xs text-gray-500">
                <span class="font-medium">Validasi:</span> {{ $question->validation_rules }}
            </div>
        @endif
    </div>
</div>


