{{-- resources/views/admin/assets/detail.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Aset')
@section('page-title', 'Detail Aset')
@section('page-subtitle', 'Kelengkapan data — ' . ($asset->subKlasifikasi->klasifikasi->klasifikasiaset ?? ''))

@section('content')

    @if (session('success'))
        <div
            class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Breadcrumb --}}
    <div class="mb-4 flex items-center gap-2 text-xs text-gray-400">
        <a href="{{ route('admin.assets.index') }}" class="hover:text-gray-600 transition-colors">Aset</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-600">Detail: {{ $asset->kode_aset }}</span>
    </div>

    {{-- Info Dasar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-5">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-700">Informasi Dasar Aset</p>
            <span class="font-mono text-xs font-semibold text-gray-600 bg-gray-100 px-2.5 py-1 rounded">
                {{ $asset->kode_aset }}
            </span>
        </div>
        <div class="px-6 py-6 grid grid-cols-2 gap-x-8 gap-y-6 text-sm">
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Nama Aset</p>
                <p class="font-semibold text-gray-800">{{ $asset->nama_aset }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">OPD</p>
                <p class="text-gray-700">{{ $asset->opd->namaopd ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Klasifikasi</p>
                <p class="text-gray-700">{{ $asset->subKlasifikasi->klasifikasi->klasifikasiaset ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 mb-1">Sub Klasifikasi</p>
                <p class="text-gray-700">{{ $asset->subKlasifikasi->subklasifikasiaset ?? '-' }}</p>
            </div>
            @if ($asset->keterangan)
                <div class="col-span-2">
                    <p class="text-xs font-medium text-gray-400 mb-1">Keterangan</p>
                    <p class="text-gray-700">{{ $asset->keterangan }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Form Kelengkapan --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-700">Kelengkapan Data</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $detail ? 'Data sudah diisi — klik Perbarui untuk mengubah' : 'Belum ada data — isi form di bawah' }}
                </p>
            </div>
            @if ($detail)
                <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    Lengkap
                </span>
            @else
                <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Belum Lengkap
                </span>
            @endif
        </div>

        <form method="POST"
            action="{{ $detail ? route('admin.assets.detail.update', $asset) : route('admin.assets.detail.store', $asset) }}"
            class="px-6 pt-6 pb-8">
            @csrf
            @if ($detail)
                @method('PUT')
            @endif

            {{-- ════════════════════════════════════════
             PL — Perangkat Lunak
        ════════════════════════════════════════ --}}
            @if ($kodeklas === 'pl')
                <div x-data="{
                    lisensi: '{{ old('lisensi', $detail?->lisensi ?? '') }}',
                    vendor: '{{ old('vendor', $detail?->vendor ?? '') }}',
                    lokasi: '{{ old('lokasi_hosting', $detail?->lokasi_hosting ?? '') }}'
                }" class="grid grid-cols-2 gap-x-6 gap-y-0">
                    {{-- URL --}}
                    <div class="col-span-2 pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">URL Aplikasi <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="url" value="{{ old('url', $detail?->url ?? '') }}"
                            placeholder="https://aplikasi.baliprov.go.id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <p class="mt-1 text-xs text-gray-400">Pastikan menyertakan http:// atau https://.</p>
                        @error('url')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Versi --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Versi <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="versi" value="{{ old('versi', $detail?->versi ?? '') }}"
                            placeholder="cth: 3.2.1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('versi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lisensi --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Lisensi <span
                                class="text-red-500">*</span></label>
                        <select name="lisensi" x-model="lisensi" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Proprietary', 'Open Source', 'Freeware', 'In-House'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('lisensi', $detail?->lisensi) === $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('lisensi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tgl Lisensi --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Tgl. Lisensi Berakhir
                            <span x-show="lisensi === 'Proprietary'" x-cloak class="text-red-500">*</span>
                            <span x-show="lisensi !== 'Proprietary'" class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <input type="date" name="tgl_lisensi_berakhir"
                            value="{{ old('tgl_lisensi_berakhir', $detail?->tgl_lisensi_berakhir?->format('Y-m-d')) }}"
                            :required="lisensi === 'Proprietary'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        @error('tgl_lisensi_berakhir')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Vendor --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Vendor / Pengembang <span
                                class="text-red-500">*</span></label>
                        <select name="vendor" x-model="vendor" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Diskominfos Prov Bali', 'Mandiri', 'Pihak Ketiga'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('vendor', $detail?->vendor) === $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lead Developer — muncul jika Mandiri atau Pihak Ketiga --}}
                    <div x-show="vendor === 'Mandiri' || vendor === 'Pihak Ketiga'" x-cloak>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Lead Developer
                            <span x-show="vendor === 'Mandiri' || vendor === 'Pihak Ketiga'" class="text-red-500">*</span>
                        </label>
                        <input type="text" name="lead_developer"
                            value="{{ old('lead_developer', $detail?->lead_developer ?? '') }}"
                            placeholder="cth: I Putu Sundika / PT Teknologi Maju"
                            :required="vendor === 'Mandiri' || vendor === 'Pihak Ketiga'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        @error('lead_developer')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Platform --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Platform <span
                                class="text-red-500">*</span></label>
                        <select name="platform" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Web', 'Mobile', 'Desktop'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('platform', $detail?->platform) === $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('platform')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi Hosting --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi Hosting <span
                                class="text-red-500">*</span></label>
                        <select name="lokasi_hosting" x-model="lokasi" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Pusat Data BALIPROV', 'PDN KOMDIGI', 'Cloud AWS Diskominfos Prov Bali', 'Lain-lain'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('lokasi_hosting', $detail?->lokasi_hosting) === $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('lokasi_hosting')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Server Lainnya — muncul jika Lain-lain --}}
                    <div x-show="lokasi === 'Lain-lain'" x-cloak>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Lokasi Server <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_server_lainnya"
                            value="{{ old('nama_server_lainnya', $detail?->nama_server_lainnya ?? '') }}"
                            placeholder="cth: AWS Singapore, GCP Jakarta" :required="lokasi === 'Lain-lain'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        @error('nama_server_lainnya')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Server --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Server <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_server"
                            value="{{ old('nama_server', $detail?->nama_server ?? '') }}"
                            placeholder="cth: srv-simpeg-01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('nama_server')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            @endif

            {{-- ════════════════════════════════════════
             PK — Perangkat Keras
        ════════════════════════════════════════ --}}
            @if ($kodeklas === 'pk')
                <div class="grid grid-cols-2 gap-x-6 gap-y-0">

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Merk <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="merk" value="{{ old('merk', $detail?->merk) }}"
                            placeholder="cth: TP-Link"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('merk')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Model <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model', $detail?->model) }}"
                            placeholder="cth: AC-1200 Archer C6"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('model')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Serial Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="serial_number"
                            value="{{ old('serial_number', $detail?->serial_number ?? '') }}" placeholder="cth: SN123456"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('serial_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Perolehan</label>
                        <input type="number" name="tahun_perolehan"
                            value="{{ old('tahun_perolehan', $detail?->tahun_perolehan) }}" min="1990"
                            max="2099" placeholder="{{ date('Y') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak diketahui</p>
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kondisi <span
                                class="text-red-500">*</span></label>
                        <select name="kondisi" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('kondisi', $detail?->kondisi) === $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('kondisi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">IP Address <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="ip_address"
                            value="{{ old('ip_address', $detail?->ip_address ?? '') }}" placeholder="cth: 192.168.1.1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('ip_address')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2 pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi Fisik <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="lokasi_fisik"
                            value="{{ old('lokasi_fisik', $detail?->lokasi_fisik) }}"
                            placeholder="cth: Ruang Server Lt. 3 Gedung Kantor"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('lokasi_fisik')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2 pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Spesifikasi <span
                                class="text-red-500">*</span></label>
                        <textarea name="spesifikasi" rows="3" required
                            placeholder="cth: 4 vCPU, 8GB RAM, 100GB SSD — OS: Ubuntu 24.04 LTS"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('spesifikasi', $detail?->spesifikasi ?? '') }}</textarea>
                        @error('spesifikasi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            @endif

            {{-- ════════════════════════════════════════
             SP — Sarana Pendukung
        ════════════════════════════════════════ --}}
            @if ($kodeklas === 'sp')
                <div class="grid grid-cols-2 gap-x-6 gap-y-0">

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Merk <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="merk" value="{{ old('merk', $detail?->merk) }}"
                            placeholder="cth: APC"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('merk')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Model <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ old('model', $detail?->model) }}"
                            placeholder="cth: Smart-UPS 3000VA"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('model')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Serial Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="serial_number"
                            value="{{ old('serial_number', $detail?->serial_number ?? '') }}"
                            placeholder="cth: AS2109450123"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('serial_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kapasitas <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="kapasitas" value="{{ old('kapasitas', $detail?->kapasitas ?? '') }}"
                            placeholder="cth: 3000VA / 2700W"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('kapasitas')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tahun Perolehan</label>
                        <input type="number" name="tahun_perolehan"
                            value="{{ old('tahun_perolehan', $detail?->tahun_perolehan) }}" min="1990"
                            max="2099" placeholder="{{ date('Y') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <p class="mt-1 text-xs text-gray-400">Kosongkan jika tidak diketahui</p>
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kondisi <span
                                class="text-red-500">*</span></label>
                        <select name="kondisi" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('kondisi', $detail?->kondisi) === $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('kondisi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-2 pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi Fisik <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="lokasi_fisik"
                            value="{{ old('lokasi_fisik', $detail?->lokasi_fisik) }}"
                            placeholder="cth: Ruang Server Data Center Pemprov Bali"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('lokasi_fisik')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            @endif

            {{-- ════════════════════════════════════════
             SK — SDM dan Pihak Ketiga
        ════════════════════════════════════════ --}}
            @if ($kodeklas === 'sk')
                <div class="grid grid-cols-2 gap-x-6 gap-y-0">

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jabatan <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $detail?->jabatan) }}"
                            placeholder="cth: Pranata Komputer Ahli Muda"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('jabatan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Unit Kerja <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="unit_kerja" value="{{ old('unit_kerja', $detail?->unit_kerja) }}"
                            placeholder="cth: Bidang Persandian dan Keamanan Informasi"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('unit_kerja')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">No. HP <span
                                class="text-red-500">*</span></label>
                        <input type="tel" name="no_hp" value="{{ old('no_hp', $detail?->no_hp ?? '') }}"
                            placeholder="cth: 08123456789"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('no_hp')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $detail?->email ?? '') }}"
                            placeholder="cth: nama@baliprov.go.id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tipe <span
                                class="text-red-500">*</span></label>
                        <select name="tipe" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Internal', 'Vendor', 'Kontraktor'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('tipe', $detail?->tipe) === $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipe')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tgl. Kontrak Berakhir</label>
                        <input type="date" name="tgl_kontrak_berakhir"
                            value="{{ old('tgl_kontrak_berakhir', $detail?->tgl_kontrak_berakhir?->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        <p class="mt-1 text-xs text-gray-400">Kosongkan jika ASN / tidak berbatas waktu</p>
                    </div>

                    <div class="col-span-2 pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Akses Sistem <span
                                class="text-red-500">*</span></label>
                        <textarea name="akses_sistem" rows="3" required placeholder="cth: PERISAI, Baliprov-CSIRT, Lenterasiber"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('akses_sistem', $detail?->akses_sistem ?? '') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Pisahkan dengan koma. Pisahkan dengan koma.</p>
                        @error('akses_sistem')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            @endif

            {{-- ════════════════════════════════════════
             DI — Data dan Informasi
        ════════════════════════════════════════ --}}
            @if ($kodeklas === 'di')
                <div x-data="{
                    bentuk: '{{ old('bentuk', $detail?->bentuk ?? '') }}',
                    enkripsi: '{{ old('enkripsi', $detail?->enkripsi ?? '') }}'
                }" class="grid grid-cols-2 gap-x-6 gap-y-0">
                    {{-- Bentuk --}}
                    <div class="col-span-2 pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Bentuk Informasi <span
                                class="text-red-500">*</span></label>
                        <select name="bentuk" x-model="bentuk" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Elektronik', 'Fisik', 'Keduanya'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('bentuk', $detail?->bentuk) === $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('bentuk')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi Fisik --}}
                    <div x-show="bentuk === 'Fisik' || bentuk === 'Keduanya'" x-cloak>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Lokasi Fisik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="lokasi_fisik"
                            value="{{ old('lokasi_fisik', $detail?->lokasi_fisik ?? '') }}"
                            placeholder="cth: Lemari Arsip Terkunci, Ruang TU Lt. 2"
                            :required="bentuk === 'Fisik' || bentuk === 'Keduanya'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        @error('lokasi_fisik')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi Elektronik --}}
                    <div x-show="bentuk === 'Elektronik' || bentuk === 'Keduanya'" x-cloak>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Lokasi Elektronik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="lokasi_elektronik"
                            value="{{ old('lokasi_elektronik', $detail?->lokasi_elektronik ?? '') }}"
                            placeholder="cth: Database Server SIMPEG, Data Center Pemprov Bali"
                            :required="bentuk === 'Elektronik' || bentuk === 'Keduanya'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        @error('lokasi_elektronik')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Format --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Format <span
                                class="text-red-500">*</span></label>
                        <select name="format" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Dokumen', 'Spreadsheet', 'Database', 'Laporan', 'Rekaman', 'Sertifikat', 'Source Code', 'Lainnya'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('format', $detail?->format) === $opt ? 'selected' : '' }}>{{ $opt }}
                                </option>
                            @endforeach
                        </select>
                        @error('format')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Klasifikasi Data --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Klasifikasi Data <span
                                class="text-red-500">*</span></label>
                        <select name="klasifikasi_data" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Publik', 'Terbatas', 'Rahasia', 'Sangat Rahasia'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('klasifikasi_data', $detail?->klasifikasi_data) === $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">
                            Sesuai mekanisme PPID: <span class="font-medium">Publik</span> = dapat diakses umum,
                            <span class="font-medium">Terbatas</span> = internal OPD,
                            <span class="font-medium">Rahasia</span> = terbatas pejabat tertentu,
                            <span class="font-medium">Sangat Rahasia</span> = keamanan negara.
                        </p>
                        @error('klasifikasi_data')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Retensi --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Retensi <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="retensi" value="{{ old('retensi', $detail?->retensi ?? '') }}"
                            placeholder="cth: 5 atau 2,5 atau Permanen"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        <p class="mt-1 text-xs text-gray-400">Wajib angka dalam satuan Tahun (koma diizinkan), atau
                            "Permanen".</p>
                        @error('retensi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Enkripsi --}}
                    <div class="pb-5">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Enkripsi <span
                                class="text-red-500">*</span></label>
                        <select name="enkripsi" x-model="enkripsi" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih --</option>
                            @foreach (['Ya', 'Tidak'] as $opt)
                                <option value="{{ $opt }}"
                                    {{ old('enkripsi', $detail?->enkripsi) === $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('enkripsi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Metode Enkripsi — muncul jika Ya --}}
                    <div x-show="enkripsi === 'Ya'" x-cloak>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Metode Enkripsi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="metode_enkripsi"
                            value="{{ old('metode_enkripsi', $detail?->metode_enkripsi ?? '') }}"
                            placeholder="cth: AES-256, RSA-2048, TLS 1.3" :required="enkripsi === 'Ya'"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        @error('metode_enkripsi')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            @endif

            {{-- Klasifikasi tidak dikenali --}}
            @if (!in_array($kodeklas, ['pl', 'pk', 'sp', 'sk', 'di']))
                <div class="py-8 text-center text-gray-400 text-sm">
                    Kelengkapan data untuk klasifikasi ini belum tersedia.
                </div>
            @endif

            {{-- ── Footer buttons --}}
            @if (in_array($kodeklas, ['pl', 'pk', 'sp', 'sk', 'di']))
                <div class="flex items-center justify-between pt-6 mt-8 border-t border-gray-100">
                    <a href="{{ route('admin.assets.index') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        ← Kembali
                    </a>
                    <div class="flex items-center gap-3">
                        {{-- Export PDF --}}
                        @if ($detail)
                            <a href="{{ route('admin.assets.detail.export-pdf', $asset) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-red-200
                           bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 13h4M10 17h4M10 9h1" />
                                </svg>
                                Export PDF
                            </a>
                        @endif
                        {{-- Simpan / Perbarui --}}
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            {{ $detail ? 'Perbarui Data' : 'Simpan Data' }}
                        </button>
                    </div>
                </div>
            @else
                <div class="pt-6 mt-6 border-t border-gray-100">
                    <a href="{{ route('admin.assets.index') }}"
                        class="inline-flex px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        ← Kembali
                    </a>
                </div>
            @endif

        </form>
    </div>

@endsection
