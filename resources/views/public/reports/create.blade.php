@extends('layouts.admin')

@section('title', 'Buat Laporan Baru')
@section('page-title', 'Buat Laporan Baru')
@section('page-subtitle', 'Laporkan insiden atau kerentanan keamanan siber kepada CSIRT Provinsi Bali')

@section('content')

    <div class="px-6 py-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            <!-- Info Box sebagai Header Card -->
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-100">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">Panduan Pengisian Laporan</p>
                        <p class="text-xs text-blue-700 mt-0.5">Laporan wajib disertai video Proof of Concept (PoC)</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('public.reports.store') }}" method="POST" enctype="multipart/form-data"
                class="px-6 py-5 pb-6 space-y-5">
                @csrf

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Judul Laporan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                        class="w-full px-4 py-2.5 border @error('title') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: SQL Injection pada portal dinas-xxx.baliprov.go.id">
                    @error('title')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sistem yang Terdampak --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">URL Sistem Elektronik Pemprov Bali yang
                        Terdampak
                        <span
                            class="ml-1 px-1.5 py-0.5 bg-blue-100 text-blue-600 text-xs rounded font-medium">Opsional</span>
                    </label>
                    <input type="text" name="affected_system" value="{{ old('affected_system') }}" maxlength="255"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="https://example.baliprov.go.id/halaman-terdampak">
                    <p class="mt-1 text-xs text-gray-400">Opsional. Isi jika ada URL spesifik yang terdampak.</p>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Deskripsi Singkat Laporan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="6" required
                        class="w-full px-4 py-2.5 border @error('description') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="Jelaskan laporan Anda secara umum.">{{ old('description') }}</textarea>
                    <div class="flex justify-between mt-1">
                        @error('description')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @else
                            <p class="text-xs text-gray-400">Minimal 50 karakter.</p>
                        @enderror
                        <p class="text-xs text-gray-400" id="desc-count">0 karakter</p>
                    </div>
                </div>

                {{-- Jenis Insiden --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Jenis Insiden <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Pilih satu yang paling menggambarkan temuan Anda.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" id="incident-grid">

                        @php
                            $incidents = [
                                [
                                    'value' => 'data_breach',
                                    'label' => 'Data Breach',
                                    'desc' => 'Data sensitif bocor/terexpose',
                                ],
                                [
                                    'value' => 'web_defacement',
                                    'label' => 'Web Defacement',
                                    'desc' => 'Tampilan website berubah oleh attacker',
                                ],
                                [
                                    'value' => 'ransomware',
                                    'label' => 'Ransomware',
                                    'desc' => 'File terenkripsi, diminta tebusan',
                                ],
                                [
                                    'value' => 'phishing',
                                    'label' => 'Phishing',
                                    'desc' => 'Penipuan meniru institusi resmi',
                                ],
                                [
                                    'value' => 'malicious_software',
                                    'label' => 'Malicious Software',
                                    'desc' => 'Virus, trojan, cryptojacking',
                                ],
                                [
                                    'value' => 'exploit',
                                    'label' => 'Exploit',
                                    'desc' => 'Celah keamanan berhasil dieksploitasi',
                                ],
                                [
                                    'value' => 'account_hijacking',
                                    'label' => 'Account Hijacking',
                                    'desc' => 'Akun resmi diretas/disalahgunakan',
                                ],
                                [
                                    'value' => 'advanced_persistence_threat',
                                    'label' => 'Advanced Persistence Threat',
                                    'desc' => 'Serangan canggih dan terarah',
                                ],
                                [
                                    'value' => 'peringatan_keamanan',
                                    'label' => 'Peringatan Keamanan',
                                    'desc' => 'Alert dari BSSN/monitoring',
                                ],
                                ['value' => 'lainnya', 'label' => 'Lain-lain', 'desc' => 'Jelaskan di deskripsi'],
                            ];
                        @endphp

                        @foreach ($incidents as $incident)
                            <label class="incident-card relative cursor-pointer" data-value="{{ $incident['value'] }}">
                                <input type="radio" name="incident_type" value="{{ $incident['value'] }}" required
                                    {{ old('incident_type') == $incident['value'] ? 'checked' : '' }} class="sr-only">
                                <div
                                    class="card-box p-3 border-2 border-gray-200 rounded-lg hover:border-blue-300 transition-all select-none
                    {{ old('incident_type') == $incident['value'] ? 'border-blue-500 bg-blue-50' : '' }}">
                                    <p class="text-sm font-medium text-gray-800">{{ $incident['label'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $incident['desc'] }}</p>
                                </div>
                            </label>
                        @endforeach

                    </div>

                    {{-- Input Lain-lain --}}
                    <div id="lainnya-container" class="mt-3 {{ old('incident_type') == 'lainnya' ? '' : 'hidden' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sebutkan jenis insiden:</label>
                        <input type="text" name="incident_type_other" value="{{ old('incident_type_other') }}"
                            id="incident_type_other"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Jelaskan jenis insiden...">
                    </div>

                    @error('incident_type')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dampak Insiden --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tingkat Dampak pada Sistem <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-400 mb-3">
                        Pilih berdasarkan kemampuan eksploitasi yang Anda buktikan, bukan data yang Anda ambil.
                    </p>
                    <div class="space-y-2">

                        {{-- Sangat Berbahaya --}}
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="severity" value="critical"
                                {{ old('severity') == 'critical' ? 'checked' : '' }} class="sr-only peer" required>
                            <div
                                class="flex items-start gap-3 p-3.5 border-2 rounded-xl transition-all
                border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 hover:border-red-300">
                                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Sangat Berbahaya</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Sistem memungkinkan eksfiltrasi data total, remote code execution,
                                        atau layanan sudah tidak berfungsi akibat serangan
                                    </p>
                                </div>
                            </div>
                        </label>

                        {{-- Berbahaya --}}
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="severity" value="high"
                                {{ old('severity') == 'high' ? 'checked' : '' }} class="sr-only peer" required>
                            <div
                                class="flex items-start gap-3 p-3.5 border-2 rounded-xl transition-all
                border-gray-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                                <svg class="w-5 h-5 text-orange-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Berbahaya</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Data dapat diakses atau dimodifikasi tanpa izin,
                                        atau fungsi penting sistem dapat terganggu
                                    </p>
                                </div>
                            </div>
                        </label>

                        {{-- Cukup Berbahaya --}}
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="severity" value="medium"
                                {{ old('severity') == 'medium' ? 'checked' : '' }} class="sr-only peer" required>
                            <div
                                class="flex items-start gap-3 p-3.5 border-2 rounded-xl transition-all
                border-gray-200 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:border-yellow-300">
                                <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Cukup Berbahaya</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Akses tidak sah dapat terjadi namun terbatas scope-nya,
                                        atau dampak masih dapat dikontrol
                                    </p>
                                </div>
                            </div>
                        </label>

                        {{-- Perlu Diperhatikan --}}
                        <label class="relative cursor-pointer block">
                            <input type="radio" name="severity" value="low"
                                {{ old('severity') == 'low' ? 'checked' : '' }} class="sr-only peer" required>
                            <div
                                class="flex items-start gap-3 p-3.5 border-2 rounded-xl transition-all
                border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300">
                                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Perlu Diperhatikan</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Celah keamanan ditemukan dan dapat direproduksi,
                                        namun belum terjadi eksploitasi aktif
                                    </p>
                                </div>
                            </div>
                        </label>

                    </div>
                    @error('severity')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PoC Section --}}
                <div class="border-t border-gray-100 pt-5">
                    <p class="text-sm font-semibold text-gray-700 mb-1">Proof of Concept (PoC)</p>
                    <p class="text-xs text-gray-500 mb-4">
                        Laporan akan diproses lebih lanjut jika PoC dinyatakan valid.
                        PoC yang valid adalah jika link memang berisikan video PoC yang dapat diakses dan video dapat
                        ditonton dengan baik, serta dapat diujicoba kembali dengan langkah yang persis sama dalam video PoC
                        dan menghasilkan hasil yang sama dengan temuan laporan.
                        Untuk itu video PoC harus menunjukkan langkah detil dan jelas. Video PoC tidak boleh dihapus sebelum
                        proses dinyatakan Selesai.
                    </p>

                    {{-- Link Video --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Link Video PoC <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="poc_video_url" value="{{ old('poc_video_url') }}" required
                            class="w-full px-4 py-2.5 border @error('poc_video_url') border-red-400 bg-red-50 @else border-gray-300 @enderror rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="https://youtube.com/... atau https://drive.google.com/...">
                        <p class="mt-1 text-xs text-gray-400">YouTube, Google Drive, atau platform video lainnya.</p>
                        @error('poc_video_url')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Submit --}}
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('public.reports.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        ← Kembali
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {

            // ── Incident type cards ──────────────────────────────────────────
            var cards = document.querySelectorAll('.incident-card');
            var lainnyaContainer = document.getElementById('lainnya-container');

            function selectCard(card) {
                // Reset semua card
                cards.forEach(function(c) {
                    c.querySelector('.card-box').classList.remove('border-blue-500', 'bg-blue-50');
                    c.querySelector('.card-box').classList.add('border-gray-200');
                    c.querySelector('input[type="radio"]').checked = false;
                });

                // Aktifkan card yang dipilih
                var box = card.querySelector('.card-box');
                box.classList.remove('border-gray-200');
                box.classList.add('border-blue-500', 'bg-blue-50');
                card.querySelector('input[type="radio"]').checked = true;

                // Toggle field lain-lain
                if (card.dataset.value === 'lainnya') {
                    lainnyaContainer.classList.remove('hidden');
                    var otherInput = document.getElementById('incident_type_other');
                    if (otherInput) {
                        otherInput.focus();
                    }
                } else {
                    lainnyaContainer.classList.add('hidden');
                }
            }

            cards.forEach(function(card) {
                card.addEventListener('click', function() {
                    selectCard(card);
                });
            });

            // Inisialisasi state dari old() value
            cards.forEach(function(card) {
                var radio = card.querySelector('input[type="radio"]');
                if (radio && radio.checked) {
                    var box = card.querySelector('.card-box');
                    box.classList.remove('border-gray-200');
                    box.classList.add('border-blue-500', 'bg-blue-50');
                }
            });

            // ── Desc character counter ────────────────────────────────────────
            var desc = document.querySelector('textarea[name="description"]');
            var counter = document.getElementById('desc-count');
            if (desc && counter) {
                function updateCounter() {
                    var len = desc.value.length;
                    counter.textContent = len + ' karakter';
                    counter.className = len >= 50 ? 'text-xs text-green-600' : 'text-xs text-red-500';
                }
                desc.addEventListener('input', updateCounter);
                updateCounter();
            }

        })();
    </script>

@endsection
