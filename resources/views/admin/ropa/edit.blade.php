{{-- resources/views/admin/ropa/edit.blade.php --}}
@extends('layouts.admin')

@section('title', $ropaActivity->kode . ' — ' . $ropaActivity->nama_aktivitas)
@section('page-title', 'Detail Aktivitas RoPA')
@section('page-subtitle', $ropaActivity->kode . ' · ' . ($ropaActivity->opd?->namaopd ?? '-') . ' · Tahun ' . ($ropaActivity->tahunAktif?->tahun ?? '-'))

@section('content')

@php
$tabs = ['umum'=>'Informasi Umum','data'=>'Data Pribadi','penyimpanan'=>'Penyimpanan & Pemrosesan','pengamanan'=>'Pengamanan','penerima'=>'Penerima Data','risiko'=>'Hak & Risiko'];
$selectedUmum     = $ropaActivity->personalDataTypes->where('is_spesifik', false)->pluck('jenis_data')->toArray();
$selectedSpesifik = $ropaActivity->personalDataTypes->where('is_spesifik', true)->pluck('jenis_data')->toArray();
$selectedDasar    = $ropaActivity->legalBases->pluck('dasar_pemrosesan')->toArray();
$selectedHak      = $ropaActivity->subjectRights->pluck('pasal')->toArray();
@endphp

@if (session('success'))
    <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm mb-4">
        <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{!! session('success') !!}</span>
    </div>
@endif
@if ($errors->any())
    <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm mb-4">
        <svg class="h-5 w-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

@if (!$isEditable)
    <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 shadow-sm mb-4">
        <svg class="h-5 w-5 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span>Aktivitas ini berasal dari tahun non-aktif dan <strong>tidak dapat diedit</strong>.</span>
    </div>
@endif

<form method="POST" action="{{ route('admin.ropa.update', $ropaActivity) }}">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.ropa.index') }}"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-xs font-mono font-semibold text-indigo-600">{{ $ropaActivity->kode }}</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $ropaActivity->nama_aktivitas }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.ropa.detail-pdf', $ropaActivity) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50
                           px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak PDF
                </a>
                @if ($isEditable)
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700
                               px-4 py-1.5 text-sm font-semibold text-white transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                @endif
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-gray-200 px-6 overflow-x-auto">
            <nav class="flex gap-0 -mb-px">
                @foreach ($tabs as $key => $label)
                    <button type="button" onclick="switchTab('{{ $key }}')" id="tab-btn-{{ $key }}"
                        class="tab-btn whitespace-nowrap px-4 py-3 text-sm border-b-2 font-medium transition-colors
                               {{ $loop->first ? 'text-indigo-600 border-indigo-600' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="p-6">

            {{-- TAB 1: Informasi Umum --}}
            <div id="tab-umum" class="tab-panel space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kode Aktivitas</label>
                        <input type="text" value="{{ $ropaActivity->kode }}" readonly
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono">
                    </div>
                    @if (auth()->user()->hasRole(['Super Admin', 'admin']))
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">OPD</label>
                        <select name="opd_id" {{ !$isEditable ? 'disabled' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                            <option value="">Pilih OPD...</option>
                            @foreach ($opds as $opd)
                                <option value="{{ $opd->id }}" {{ $ropaActivity->opd_id == $opd->id ? 'selected' : '' }}>{{ $opd->namaopd }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div></div>
                    @endif
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Aktivitas Pemrosesan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_aktivitas" value="{{ old('nama_aktivitas', $ropaActivity->nama_aktivitas) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Penanggung Jawab Proses <span class="text-red-500">*</span></label>
                        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab', $ropaActivity->penanggung_jawab) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi & Tujuan Pemrosesan <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi_tujuan" rows="4" {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('deskripsi_tujuan', $ropaActivity->deskripsi_tujuan) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Proses / Kegiatan Sebelumnya</label>
                        <input type="text" name="proses_sebelumnya" value="{{ old('proses_sebelumnya', $ropaActivity->proses_sebelumnya) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Proses / Kegiatan Setelahnya</label>
                        <input type="text" name="proses_setelahnya" value="{{ old('proses_setelahnya', $ropaActivity->proses_setelahnya) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sistem / Aplikasi yang Digunakan</label>
                        <div id="assets-container" class="space-y-2">
                            @foreach ($ropaActivity->assets as $assetRel)
                            <div class="asset-row flex flex-wrap items-center gap-2">
                                <select name="assets[{{ $loop->index }}][asset_instance_id]"
                                    {{ !$isEditable ? 'disabled' : '' }}
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Pilih dari inventaris aset (opsional) --</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ $assetRel->asset_instance_id === $asset->id ? 'selected' : '' }}>
                                            {{ $asset->kode_aset }} — {{ $asset->nama_aset }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" name="assets[{{ $loop->index }}][nama_manual]"
                                    value="{{ $assetRel->nama_manual }}"
                                    placeholder="atau ketik nama manual..."
                                    {{ !$isEditable ? 'readonly' : '' }}
                                    class="w-52 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <select name="assets[{{ $loop->index }}][peran_aset]"
                                    {{ !$isEditable ? 'disabled' : '' }}
                                    class="w-36 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    @foreach (\App\Models\RopaAsset::PERAN_LABELS as $val => $lbl)
                                        <option value="{{ $val }}" {{ $assetRel->peran_aset === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                @if ($isEditable)
                                <button type="button" onclick="this.closest('.asset-row').remove()"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200
                                           text-gray-400 hover:text-red-600 hover:border-red-300 transition-colors flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @if ($isEditable)
                        <button type="button" onclick="addAssetRow()"
                            class="mt-2 inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Aset / Aplikasi
                        </button>
                        @endif
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Catatan / Keterangan Tambahan</label>
                        <textarea name="catatan" rows="2" {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('catatan', $ropaActivity->catatan) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- TAB 2: Data Pribadi --}}
            <div id="tab-data" class="tab-panel hidden space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Subjek Data Pribadi <span class="text-red-500">*</span></label>
                        <input type="text" name="subjek_data" value="{{ old('subjek_data', $ropaActivity->subjek_data) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            placeholder="Contoh: Masyarakat umum (pelapor)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sumber Pemerolehan Data <span class="text-red-500">*</span></label>
                        <input type="text" name="sumber_pemerolehan" value="{{ old('sumber_pemerolehan', $ropaActivity->sumber_pemerolehan) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            placeholder="Contoh: Subjek data secara langsung melalui portal LAPOR!"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Pribadi Umum yang Diproses</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach (\App\Models\RopaPersonalDataType::UMUM as $jenis)
                            <label class="flex items-start gap-2 {{ $isEditable ? 'cursor-pointer' : '' }}">
                                <input type="checkbox" name="data_umum[]" value="{{ $jenis }}"
                                    {{ in_array($jenis, old('data_umum', $selectedUmum)) ? 'checked' : '' }}
                                    {{ !$isEditable ? 'disabled' : '' }}
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700">{{ $jenis }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Data Pribadi Spesifik
                        <span class="text-xs font-normal text-red-500 ml-1">(Pasal 4 ayat 2 UU PDP)</span>
                    </p>
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @foreach (\App\Models\RopaPersonalDataType::SPESIFIK as $jenis)
                            <label class="flex items-start gap-2 {{ $isEditable ? 'cursor-pointer' : '' }}">
                                <input type="checkbox" name="data_spesifik[]" value="{{ $jenis }}"
                                    {{ in_array($jenis, old('data_spesifik', $selectedSpesifik)) ? 'checked' : '' }}
                                    {{ !$isEditable ? 'disabled' : '' }}
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                                <span class="text-xs text-gray-700">{{ $jenis }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- TAB 3: Penyimpanan & Pemrosesan --}}
            <div id="tab-penyimpanan" class="tab-panel hidden space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Penyimpanan Data Pribadi</label>
                    <textarea name="penyimpanan_data" rows="3" {{ !$isEditable ? 'readonly' : '' }}
                        placeholder="Contoh: Platform SP4N-LAPOR!, database lokal Diskominfos, arsip fisik untuk aduan tatap muka"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('penyimpanan_data', $ropaActivity->penyimpanan_data) }}</textarea>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Pemrosesan</p>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-6">
                        <label class="flex items-center gap-2 {{ $isEditable ? 'cursor-pointer' : '' }}">
                            <input type="checkbox" name="metode_elektronik" value="1"
                                {{ old('metode_elektronik', $ropaActivity->metode_elektronik) ? 'checked' : '' }}
                                {{ !$isEditable ? 'disabled' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-gray-700">Elektronik</span>
                        </label>
                        <label class="flex items-center gap-2 {{ $isEditable ? 'cursor-pointer' : '' }}">
                            <input type="checkbox" name="metode_non_elektronik" value="1"
                                {{ old('metode_non_elektronik', $ropaActivity->metode_non_elektronik) ? 'checked' : '' }}
                                {{ !$isEditable ? 'disabled' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-gray-700">Non-Elektronik</span>
                        </label>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dasar Pemrosesan <span class="text-red-500">*</span></p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach (\App\Models\RopaLegalBasis::LABELS as $val => $label)
                            <label class="flex items-start gap-2 {{ $isEditable ? 'cursor-pointer' : '' }}">
                                <input type="checkbox" name="dasar_pemrosesan[]" value="{{ $val }}"
                                    {{ in_array($val, old('dasar_pemrosesan', $selectedDasar)) ? 'checked' : '' }}
                                    {{ !$isEditable ? 'disabled' : '' }}
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Referensi Dasar Hukum</label>
                    <input type="text" name="referensi_dasar_hukum"
                        value="{{ old('referensi_dasar_hukum', $ropaActivity->referensi_dasar_hukum) }}"
                        {{ !$isEditable ? 'readonly' : '' }}
                        placeholder="Contoh: UU No.25/2009 tentang Pelayanan Publik, Perpres No.76/2013"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Masa Retensi Data Pribadi</label>
                    <textarea name="masa_retensi" rows="2" {{ !$isEditable ? 'readonly' : '' }}
                        placeholder="Contoh: 5 tahun sejak pengaduan selesai, kemudian diarsipkan sesuai JRA"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('masa_retensi', $ropaActivity->masa_retensi) }}</textarea>
                </div>
            </div>

            {{-- TAB 4: Pengamanan --}}
            <div id="tab-pengamanan" class="tab-panel hidden space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Langkah Teknis (Technical Safeguards)</label>
                    <textarea name="langkah_teknis" rows="5" {{ !$isEditable ? 'readonly' : '' }}
                        placeholder="Contoh: HTTPS/TLS pada seluruh jalur transmisi, pseudonimisasi identitas pelapor dalam laporan publik, kontrol akses berbasis peran (RBAC), audit log akses data pengaduan"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('langkah_teknis', $ropaActivity->langkah_teknis) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Langkah Organisasi (Organisational Safeguards)</label>
                    <textarea name="langkah_organisasi" rows="5" {{ !$isEditable ? 'readonly' : '' }}
                        placeholder="Contoh: SOP Penanganan Pengaduan, pelatihan petugas kerahasiaan data, perjanjian kerahasiaan dengan OPD terkait"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('langkah_organisasi', $ropaActivity->langkah_organisasi) }}</textarea>
                </div>
            </div>

            {{-- TAB 5: Penerima Data --}}
            <div id="tab-penerima" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-gray-500">Penerima internal tidak wajib mengisi kolom Peran dan Kontak.</p>
                    @if ($isEditable)
                    <button type="button" onclick="addRecipientRow()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 border border-indigo-200
                               px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Penerima
                    </button>
                    @endif
                </div>
                <div id="recipients-container" class="space-y-3">
                    @forelse ($ropaActivity->recipients as $recipient)
                    <div class="recipient-row bg-gray-50 rounded-xl border border-gray-200 p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Profil Penerima</label>
                                <input type="text" name="recipients[{{ $loop->index }}][profil_penerima]"
                                    value="{{ $recipient->profil_penerima }}" {{ !$isEditable ? 'readonly' : '' }}
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tipe</label>
                                <select name="recipients[{{ $loop->index }}][tipe]"
                                    {{ !$isEditable ? 'disabled' : '' }}
                                    onchange="togglePeranField(this)"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="internal" {{ $recipient->tipe === 'internal' ? 'selected' : '' }}>Internal</option>
                                    <option value="eksternal" {{ $recipient->tipe === 'eksternal' ? 'selected' : '' }}>Eksternal</option>
                                </select>
                            </div>
                            <div class="peran-field {{ $recipient->tipe !== 'eksternal' ? 'hidden' : '' }}">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Peran</label>
                                <select name="recipients[{{ $loop->index }}][peran]"
                                    {{ !$isEditable ? 'disabled' : '' }}
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">— pilih peran —</option>
                                    @foreach (\App\Models\RopaRecipient::PERAN_LABELS as $val => $lbl)
                                        <option value="{{ $val }}" {{ $recipient->peran === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="peran-field {{ $recipient->tipe !== 'eksternal' ? 'hidden' : '' }}">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Kontak / PIC</label>
                                <input type="text" name="recipients[{{ $loop->index }}][kontak_pic]"
                                    value="{{ $recipient->kontak_pic }}" {{ !$isEditable ? 'readonly' : '' }}
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Tujuan Pengiriman</label>
                                <input type="text" name="recipients[{{ $loop->index }}][tujuan_pengiriman]"
                                    value="{{ $recipient->tujuan_pengiriman }}" {{ !$isEditable ? 'readonly' : '' }}
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Mekanisme Pengiriman</label>
                                <input type="text" name="recipients[{{ $loop->index }}][mekanisme_pengiriman]"
                                    value="{{ $recipient->mekanisme_pengiriman }}" {{ !$isEditable ? 'readonly' : '' }}
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Jenis Data yang Dikirim</label>
                                <input type="text" name="recipients[{{ $loop->index }}][jenis_data_dikirim]"
                                    value="{{ $recipient->jenis_data_dikirim }}" {{ !$isEditable ? 'readonly' : '' }}
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                        @if ($isEditable)
                        <div class="mt-2 flex justify-end">
                            <button type="button" onclick="this.closest('.recipient-row').remove()"
                                class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus penerima ini</button>
                        </div>
                        @endif
                    </div>
                    @empty
                        <div id="no-recipient-msg" class="text-center py-8 text-sm text-gray-400">
                            Belum ada penerima data.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB 6: Hak & Risiko --}}
            <div id="tab-risiko" class="tab-panel hidden space-y-5">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Hak Subjek Data Pribadi yang Berlaku</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach (\App\Models\RopaSubjectRight::HAK as $pasal => $namaHak)
                            <label class="flex items-start gap-2 {{ $isEditable ? 'cursor-pointer' : '' }}">
                                <input type="checkbox" name="hak_subjek[]" value="{{ $pasal }}"
                                    {{ in_array($pasal, old('hak_subjek', $selectedHak)) ? 'checked' : '' }}
                                    {{ !$isEditable ? 'disabled' : '' }}
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700">{{ $namaHak }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Narasi Asesmen Risiko</label>
                    <textarea name="narasi_risiko" rows="4" {{ !$isEditable ? 'readonly' : '' }}
                        placeholder="Contoh: Risiko tinggi kondisional — apabila pengaduan mengandung data spesifik; potensi keterbukaan identitas pelapor jika disposisi tidak terkontrol."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('narasi_risiko', $ropaActivity->narasi_risiko) }}</textarea>
                </div>
            </div>

        </div>

        @if ($isEditable)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <a href="{{ route('admin.ropa.index') }}"
                class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Kembali
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700
                       px-5 py-2 text-sm font-semibold text-white transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
        @endif

    </div>
</form>

@include('admin.ropa._form_tabs')
@endsection
