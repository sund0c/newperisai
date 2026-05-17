{{-- resources/views/admin/dpia/edit.blade.php --}}
@extends('layouts.admin')

@section('title', $dpia->kode . ' — DPIA')
@section('page-title', 'Detail DPIA')
@section('page-subtitle', $dpia->kode . ' · ' . ($dpia->opd?->namaopd ?? '-') . ' · Tahun ' . ($dpia->tahunAktif?->tahun ?? '-'))

@section('content')

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
        <span>DPIA ini berasal dari tahun non-aktif dan <strong>tidak dapat diedit</strong>.</span>
    </div>
@endif

<form method="POST" action="{{ route('admin.dpia.update', $dpia) }}">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dpia.index') }}"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-xs font-mono font-semibold text-indigo-600">{{ $dpia->kode }}</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $dpia->nama_aktivitas }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.dpia.detail-pdf', $dpia) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50
                           px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak PDF
                </a>
                @if ($isEditable)
                    <button type="button" onclick="document.getElementById('modalHapus').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white
                               hover:bg-red-50 hover:border-red-300 hover:text-red-600
                               px-3 py-1.5 text-sm font-medium text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus
                    </button>
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

        <div class="p-6 space-y-6">

            {{-- IDENTITAS --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Identitas Dokumen</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kode DPIA</label>
                        <input type="text" value="{{ $dpia->kode }}" readonly
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Referensi RoPA</label>
                        <div class="flex items-center gap-2">
                            <input type="text" value="{{ $dpia->ropaActivity?->kode ?? '-' }}" readonly
                                class="w-32 px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono">
                            <a href="{{ route('admin.ropa.edit', $dpia->ropaActivity) }}"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                Lihat RoPA →
                            </a>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Aktivitas</label>
                        <input type="text" value="{{ $dpia->nama_aktivitas }}" readonly
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Penanggung Jawab <span class="text-red-500">*</span></label>
                        <input type="text" name="penanggung_jawab"
                            value="{{ old('penanggung_jawab', $dpia->penanggung_jawab) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pejabat Pelindung Data (PPD)</label>
                        <input type="text" name="ppd"
                            value="{{ old('ppd', $dpia->ppd) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal Penyusunan <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_penyusunan"
                            value="{{ old('tanggal_penyusunan', $dpia->tanggal_penyusunan?->format('Y-m-d')) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Versi</label>
                        <input type="text" name="versi"
                            value="{{ old('versi', $dpia->versi) }}"
                            {{ !$isEditable ? 'readonly' : '' }}
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                    </div>
                </div>
            </div>

            {{-- A. THRESHOLD --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">A. Threshold Analysis</p>
                <p class="text-xs text-gray-400 mb-3">Status terpenuhi otomatis dari indikator risiko RoPA. Isi keterangan untuk konteks tambahan.</p>
                <div class="space-y-2">
                    @foreach (\App\Models\DpiaThreshold::INDIKATOR_LABELS as $val => $label)
                        @php $threshold = $dpia->thresholds->firstWhere('indikator', $val); @endphp
                        <div class="grid grid-cols-12 gap-3 items-start border border-gray-200 rounded-lg px-3 py-2 bg-gray-50">
                            <div class="col-span-1 pt-0.5">
                                @if ($threshold?->terpenuhi)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-700">YA</span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500">Tidak</span>
                                @endif
                            </div>
                            <div class="col-span-4 text-xs text-gray-700 pt-1">{{ $label }}</div>
                            <div class="col-span-7">
                                <input type="text" name="threshold_keterangan[{{ $val }}]"
                                    value="{{ old('threshold_keterangan.'.$val, $threshold?->keterangan) }}"
                                    {{ !$isEditable ? 'readonly' : '' }}
                                    placeholder="Keterangan / alasan..."
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs
                                           focus:outline-none focus:ring-1 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-100' : '' }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- B. TIM & KONSULTASI --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">B. Tim yang Terlibat & Konsultasi</p>
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium text-gray-600">B.1 Anggota Tim</p>
                        @if ($isEditable)
                        <button type="button" onclick="addTimRow()"
                            class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Anggota
                        </button>
                        @endif
                    </div>
                    <div id="timContainer" class="space-y-2">
                        @foreach ($dpia->tim as $anggota)
                        <div class="tim-row flex items-center gap-2">
                            <input type="text" name="tim[{{ $loop->index }}][nama_anggota]"
                                value="{{ $anggota->nama_anggota }}" {{ !$isEditable ? 'readonly' : '' }}
                                placeholder="Nama anggota / instansi"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                            <input type="text" name="tim[{{ $loop->index }}][peran]"
                                value="{{ $anggota->peran }}" {{ !$isEditable ? 'readonly' : '' }}
                                placeholder="Peran / jabatan"
                                class="w-56 px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">
                            @if ($isEditable)
                            <button type="button" onclick="this.closest('.tim-row').remove()"
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
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">B.2 Konsultasi Pemangku Kepentingan</label>
                    <textarea name="konsultasi_stakeholder" rows="3" {{ !$isEditable ? 'readonly' : '' }}
                        placeholder="Jelaskan proses konsultasi..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('konsultasi_stakeholder', $dpia->konsultasi_stakeholder) }}</textarea>
                </div>
            </div>

            {{-- C. ASESMEN RISIKO --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">C. Asesmen Risiko</p>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">C.1 Kriteria Penilaian Risiko</label>
                    <textarea name="kriteria_risiko" rows="2" {{ !$isEditable ? 'readonly' : '' }}
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('kriteria_risiko', $dpia->kriteria_risiko) }}</textarea>
                </div>
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-medium text-gray-600">C.2 Identifikasi Ancaman & Rencana Mitigasi</label>
                        @if ($isEditable)
                        <button type="button" onclick="addRisikoRow()"
                            class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Risiko
                        </button>
                        @endif
                    </div>
                    <div id="risikoContainer" class="space-y-3">
                        @foreach ($dpia->risikos as $risiko)
                        <div class="risiko-row bg-gray-50 rounded-xl border border-gray-200 p-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Ancaman</label>
                                    <input type="text" name="risiko[{{ $loop->index }}][ancaman]"
                                        value="{{ $risiko->ancaman }}" {{ !$isEditable ? 'readonly' : '' }}
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-white' : '' }}">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Likelihood</label>
                                    <select name="risiko[{{ $loop->index }}][likelihood]"
                                        {{ !$isEditable ? 'disabled' : '' }}
                                        onchange="updateLevel(this)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        @foreach (['Rendah','Sedang','Tinggi'] as $v)
                                            <option value="{{ $v }}" {{ $risiko->likelihood === $v ? 'selected' : '' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Dampak</label>
                                    <select name="risiko[{{ $loop->index }}][dampak]"
                                        {{ !$isEditable ? 'disabled' : '' }}
                                        onchange="updateLevel(this)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        @foreach (['Rendah','Sedang','Tinggi'] as $v)
                                            <option value="{{ $v }}" {{ $risiko->dampak === $v ? 'selected' : '' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Level Risiko</label>
                                    @php
                                        $lvlClass = match($risiko->level) {
                                            'Tinggi' => 'bg-red-100 text-red-700',
                                            'Sedang' => 'bg-yellow-100 text-yellow-700',
                                            default  => 'bg-green-100 text-green-700',
                                        };
                                    @endphp
                                    <div class="level-badge px-3 py-2 rounded-lg text-xs font-semibold text-center {{ $lvlClass }}">
                                        {{ $risiko->level }}
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Rencana Mitigasi</label>
                                    <textarea name="risiko[{{ $loop->index }}][rencana_mitigasi]" rows="2"
                                        {{ !$isEditable ? 'readonly' : '' }}
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-white' : '' }}">{{ $risiko->rencana_mitigasi }}</textarea>
                                </div>
                            </div>
                            @if ($isEditable)
                            <div class="mt-2 flex justify-end">
                                <button type="button" onclick="this.closest('.risiko-row').remove()"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus risiko ini</button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">C.3 Evaluasi Risiko Residual</label>
                    <textarea name="evaluasi_residual" rows="3" {{ !$isEditable ? 'readonly' : '' }}
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('evaluasi_residual', $dpia->evaluasi_residual) }}</textarea>
                </div>
            </div>

            {{-- D. KESIMPULAN --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">D. Kesimpulan & Keputusan</p>
                <textarea name="kesimpulan" rows="4" {{ !$isEditable ? 'readonly' : '' }}
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ !$isEditable ? 'bg-gray-50' : '' }}">{{ old('kesimpulan', $dpia->kesimpulan) }}</textarea>
            </div>

        </div>

        @if ($isEditable)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <a href="{{ route('admin.dpia.index') }}"
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

{{-- Modal Hapus --}}
@if ($isEditable)
<div id="modalHapus"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
    onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
        <div class="px-6 py-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Hapus DPIA</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <p class="text-sm font-semibold text-gray-900 mb-1">{{ $dpia->kode }}</p>
            <p class="text-xs text-gray-500 mb-5">{{ $dpia->nama_aktivitas }}</p>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalHapus').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Batal
                </button>
                <form method="POST" action="{{ route('admin.dpia.destroy', $dpia) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700
                               text-white text-sm font-semibold rounded-lg transition-colors">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
const LEVEL_MATRIX = {
    'Tinggi': {'Rendah':'Sedang','Sedang':'Tinggi','Tinggi':'Tinggi'},
    'Sedang': {'Rendah':'Rendah','Sedang':'Sedang','Tinggi':'Tinggi'},
    'Rendah': {'Rendah':'Rendah','Sedang':'Rendah','Tinggi':'Sedang'},
};
const LEVEL_CLASS = {
    'Tinggi': 'bg-red-100 text-red-700',
    'Sedang': 'bg-yellow-100 text-yellow-700',
    'Rendah': 'bg-green-100 text-green-700',
};

function addTimRow() {
    const container = document.getElementById('timContainer');
    const idx = container.querySelectorAll('.tim-row').length;
    const div = document.createElement('div');
    div.className = 'tim-row flex items-center gap-2';
    div.innerHTML = `
        <input type="text" name="tim[${idx}][nama_anggota]" placeholder="Nama anggota / instansi"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <input type="text" name="tim[${idx}][peran]" placeholder="Peran / jabatan"
            class="w-56 px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <button type="button" onclick="this.closest('.tim-row').remove()"
            class="w-7 h-7 flex items-center justify-center rounded-lg border border-gray-200
                   text-gray-400 hover:text-red-600 hover:border-red-300 transition-colors flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    container.appendChild(div);
}

function addRisikoRow() {
    const container = document.getElementById('risikoContainer');
    const idx = container.querySelectorAll('.risiko-row').length;
    const div = document.createElement('div');
    div.className = 'risiko-row bg-gray-50 rounded-xl border border-gray-200 p-3';
    const opts = ['Rendah','Sedang','Tinggi'].map(v => `<option>${v}</option>`).join('');
    div.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Ancaman</label>
                <input type="text" name="risiko[${idx}][ancaman]" placeholder="Deskripsi ancaman..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Likelihood</label>
                <select name="risiko[${idx}][likelihood]" onchange="updateLevel(this)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">${opts}</select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dampak</label>
                <select name="risiko[${idx}][dampak]" onchange="updateLevel(this)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">${opts}</select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Level Risiko</label>
                <div class="level-badge px-3 py-2 rounded-lg text-xs font-semibold text-center bg-yellow-100 text-yellow-700">Sedang</div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Rencana Mitigasi</label>
                <textarea name="risiko[${idx}][rencana_mitigasi]" rows="2" placeholder="Rencana mitigasi..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
        </div>
        <div class="mt-2 flex justify-end">
            <button type="button" onclick="this.closest('.risiko-row').remove()"
                class="text-xs text-red-500 hover:text-red-700 font-medium">Hapus risiko ini</button>
        </div>`;
    container.appendChild(div);
}

function updateLevel(select) {
    const row = select.closest('.risiko-row');
    const likelihood = row.querySelector('select[name*="[likelihood]"]').value;
    const dampak     = row.querySelector('select[name*="[dampak]"]').value;
    const level      = LEVEL_MATRIX[likelihood]?.[dampak] ?? 'Sedang';
    const badge      = row.querySelector('.level-badge');
    badge.textContent = level;
    badge.className = `level-badge px-3 py-2 rounded-lg text-xs font-semibold text-center ${LEVEL_CLASS[level]}`;
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') document.getElementById('modalHapus')?.classList.add('hidden');
});
</script>
@endpush

@endsection
