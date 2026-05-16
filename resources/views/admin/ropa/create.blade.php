{{-- resources/views/admin/ropa/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Aktivitas RoPA')
@section('page-title', 'Tambah Aktivitas RoPA')
@section('page-subtitle', 'Daftarkan aktivitas pemrosesan data pribadi baru · Tahun ' . session('tahun_context'))

@section('content')

@php
$tabs = ['umum'=>'Informasi Umum','data'=>'Data Pribadi','penyimpanan'=>'Penyimpanan & Pemrosesan','pengamanan'=>'Pengamanan','penerima'=>'Penerima Data','risiko'=>'Hak & Risiko'];
@endphp

@if ($errors->any())
    <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm mb-4">
        <svg class="h-5 w-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.ropa.store') }}">
    @csrf
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
                    <p class="text-xs text-gray-400">Kode akan digenerate otomatis</p>
                    <p class="text-sm font-semibold text-gray-800">Aktivitas RoPA Baru</p>
                </div>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700
                       px-4 py-1.5 text-sm font-semibold text-white transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Simpan Aktivitas
            </button>
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
                        <input type="text" value="(otomatis)" readonly
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-400 font-mono italic">
                    </div>
                    @if (auth()->user()->hasRole(['Super Admin', 'admin']))
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">OPD <span class="text-red-500">*</span></label>
                        <select name="opd_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Pilih OPD...</option>
                            @foreach ($opds as $opd)
                                <option value="{{ $opd->id }}" {{ old('opd_id') == $opd->id ? 'selected' : '' }}>{{ $opd->namaopd }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div></div>
                    @endif
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Aktivitas Pemrosesan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_aktivitas" value="{{ old('nama_aktivitas') }}"
                            placeholder="Contoh: Pengelolaan Pengaduan Masyarakat (LAPOR!)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Penanggung Jawab Proses <span class="text-red-500">*</span></label>
                        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}"
                            placeholder="Contoh: Ka. Seksi Pengelolaan Informasi Publik"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi & Tujuan Pemrosesan <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi_tujuan" rows="4"
                            placeholder="Contoh: Penerimaan, pencatatan, penelaahan, dan penyelesaian pengaduan masyarakat terkait layanan publik melalui platform LAPOR! (SP4N). Tujuan: memastikan setiap pengaduan ditindaklanjuti secara transparan dan akuntabel."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('deskripsi_tujuan') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Proses / Kegiatan Sebelumnya</label>
                        <input type="text" name="proses_sebelumnya" value="{{ old('proses_sebelumnya') }}"
                            placeholder="Contoh: Penerimaan dan registrasi pengaduan oleh petugas"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Proses / Kegiatan Setelahnya</label>
                        <input type="text" name="proses_setelahnya" value="{{ old('proses_setelahnya') }}"
                            placeholder="Contoh: Verifikasi oleh OPD terkait, penutupan tiket, notifikasi kepada pelapor"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sistem / Aplikasi yang Digunakan</label>
                        <div id="assets-container" class="space-y-2"></div>
                        <button type="button" onclick="addAssetRow()"
                            class="mt-2 inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Aset / Aplikasi
                        </button>
                        <p class="text-xs text-gray-400 mt-1">Pilih dari inventaris aset yang sudah terdaftar, atau ketik nama manual jika belum terdaftar.</p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Catatan / Keterangan Tambahan</label>
                        <textarea name="catatan" rows="2"
                            placeholder="Contoh: Pelapor dapat memilih mode anonim; dalam mode ini nama dan kontak tidak disimpan."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- TAB 2: Data Pribadi --}}
            <div id="tab-data" class="tab-panel hidden space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Subjek Data Pribadi <span class="text-red-500">*</span></label>
                        <input type="text" name="subjek_data" value="{{ old('subjek_data') }}"
                            placeholder="Contoh: Masyarakat umum (pelapor)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sumber Pemerolehan Data <span class="text-red-500">*</span></label>
                        <input type="text" name="sumber_pemerolehan" value="{{ old('sumber_pemerolehan') }}"
                            placeholder="Contoh: Subjek data secara langsung melalui portal LAPOR!"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Pribadi Umum yang Diproses</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach (\App\Models\RopaPersonalDataType::UMUM as $jenis)
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="data_umum[]" value="{{ $jenis }}"
                                    {{ in_array($jenis, old('data_umum', [])) ? 'checked' : '' }}
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700">{{ $jenis }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Data Pribadi Spesifik yang Diproses
                        <span class="text-xs font-normal text-red-500 ml-1">(Pasal 4 ayat 2 UU PDP)</span>
                    </p>
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        @foreach (\App\Models\RopaPersonalDataType::SPESIFIK as $jenis)
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="data_spesifik[]" value="{{ $jenis }}"
                                    {{ in_array($jenis, old('data_spesifik', [])) ? 'checked' : '' }}
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
                    <textarea name="penyimpanan_data" rows="3"
                        placeholder="Contoh: Platform SP4N-LAPOR! (server KemenPAN-RB), database lokal aplikasi tindak lanjut Diskominfos, arsip fisik untuk aduan via tatap muka"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('penyimpanan_data') }}</textarea>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Pemrosesan</p>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="metode_elektronik" value="1"
                                {{ old('metode_elektronik') ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-gray-700">Elektronik (sistem / aplikasi / web)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="metode_non_elektronik" value="1"
                                {{ old('metode_non_elektronik') ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-gray-700">Non-Elektronik (manual, berkas fisik)</span>
                        </label>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dasar Pemrosesan <span class="text-red-500">*</span></p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach (\App\Models\RopaLegalBasis::LABELS as $val => $label)
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="dasar_pemrosesan[]" value="{{ $val }}"
                                    {{ in_array($val, old('dasar_pemrosesan', [])) ? 'checked' : '' }}
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Referensi Dasar Hukum</label>
                    <input type="text" name="referensi_dasar_hukum" value="{{ old('referensi_dasar_hukum') }}"
                        placeholder="Contoh: UU No.25/2009 tentang Pelayanan Publik, Perpres No.76/2013"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Masa Retensi Data Pribadi</label>
                    <textarea name="masa_retensi" rows="2"
                        placeholder="Contoh: 5 tahun sejak pengaduan dinyatakan selesai, kemudian diarsipkan sesuai JRA. Data identitas pelapor anonim tidak disimpan melampaui penyelesaian aduan."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('masa_retensi') }}</textarea>
                </div>
            </div>

            {{-- TAB 4: Pengamanan --}}
            <div id="tab-pengamanan" class="tab-panel hidden space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Langkah Teknis (Technical Safeguards)</label>
                    <textarea name="langkah_teknis" rows="5"
                        placeholder="Contoh: HTTPS/TLS pada seluruh jalur transmisi, pseudonimisasi identitas pelapor dalam laporan publik, kontrol akses petugas berbasis peran (RBAC), audit log akses data pengaduan, opsi pelaporan anonim"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('langkah_teknis') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Langkah Organisasi (Organisational Safeguards)</label>
                    <textarea name="langkah_organisasi" rows="5"
                        placeholder="Contoh: SOP Penanganan Pengaduan (melarang publikasi identitas pelapor tanpa persetujuan), pelatihan petugas kerahasiaan data, perjanjian kerahasiaan dengan OPD terkait yang menerima disposisi"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('langkah_organisasi') }}</textarea>
                </div>
            </div>

            {{-- TAB 5: Penerima Data --}}
            <div id="tab-penerima" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs text-gray-500">Penerima internal tidak wajib mengisi kolom Peran dan Kontak.</p>
                    <button type="button" onclick="addRecipientRow()"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 border border-indigo-200
                               px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Penerima
                    </button>
                </div>
                <div id="recipients-container" class="space-y-3">
                    <div id="no-recipient-msg" class="text-center py-8 text-sm text-gray-400">
                        Belum ada penerima data. Klik "Tambah Penerima" untuk menambahkan.
                    </div>
                </div>
            </div>

            {{-- TAB 6: Hak & Risiko --}}
            <div id="tab-risiko" class="tab-panel hidden space-y-5">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Hak Subjek Data Pribadi yang Berlaku</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach (\App\Models\RopaSubjectRight::HAK as $pasal => $namaHak)
                            <label class="flex items-start gap-2 cursor-pointer">
                                <input type="checkbox" name="hak_subjek[]" value="{{ $pasal }}"
                                    {{ in_array($pasal, old('hak_subjek', [])) ? 'checked' : '' }}
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs text-gray-700">{{ $namaHak }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Narasi Asesmen Risiko</label>
                    <textarea name="narasi_risiko" rows="4"
                        placeholder="Contoh: Risiko tinggi kondisional — apabila pengaduan mengandung data spesifik (kesehatan, anak, pidana); potensi keterbukaan identitas pelapor jika disposisi tidak terkontrol; pemrosesan skala besar (ribuan aduan/tahun)."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('narasi_risiko') }}</textarea>
                </div>
            </div>

        </div>

        {{-- Save bar --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <a href="{{ route('admin.ropa.index') }}"
                class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Batal
            </a>
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700
                       px-5 py-2 text-sm font-semibold text-white transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Simpan Aktivitas
            </button>
        </div>

    </div>
</form>

@include('admin.ropa._form_tabs')
@endsection
