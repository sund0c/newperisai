{{-- resources/views/admin/dpia/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Buat DPIA Baru')
@section('page-title', 'Buat DPIA Baru')
@section('page-subtitle', 'Data Protection Impact Assessment · Tahun ' . ($tahunContext?->tahun ?? '-'))

@section('content')

@if ($errors->any())
    <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm mb-4">
        <svg class="h-5 w-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <ul class="list-disc list-inside space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.dpia.store') }}">
    @csrf
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
                    <p class="text-xs text-gray-400">Kode akan digenerate otomatis</p>
                    <p class="text-sm font-semibold text-gray-800">DPIA Baru</p>
                </div>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 hover:bg-blue-700
                       px-4 py-1.5 text-sm font-semibold text-white transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Simpan DPIA
            </button>
        </div>

        <div class="p-6 space-y-6">

            {{-- IDENTITAS --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Identitas Dokumen</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Referensi RoPA <span class="text-red-500">*</span>
                        </label>
                        <select name="ropa_activity_id" id="ropaSelect" onchange="fillFromRopa(this)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">— Pilih aktivitas RoPA —</option>
                            @foreach ($ropaList as $ropa)
                                <option value="{{ $ropa->id }}"
                                    data-nama="{{ $ropa->nama_aktivitas }}"
                                    data-opd="{{ $ropa->opd?->namaopd ?? '-' }}"
                                    data-kode="{{ $ropa->kode }}"
                                    {{ old('ropa_activity_id') == $ropa->id ? 'selected' : '' }}>
                                    {{ $ropa->kode }} — {{ $ropa->nama_aktivitas }}
                                </option>
                            @endforeach
                        </select>
                        @if ($ropaList->isEmpty())
                            <p class="text-xs text-amber-600 mt-1">
                                Tidak ada RoPA yang memenuhi syarat DPIA (belum ada RoPA dengan indikator risiko tinggi, atau semua sudah memiliki DPIA).
                            </p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">Hanya RoPA dengan indikator risiko tinggi yang belum memiliki DPIA yang ditampilkan.</p>
                        @endif
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Nama Aktivitas</label>
                        <input type="text" id="namaAktivitas" value="{{ old('nama_aktivitas') }}" readonly
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 italic">
                        <p class="text-xs text-gray-400 mt-1">Otomatis diambil dari RoPA yang dipilih.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Penanggung Jawab <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="penanggung_jawab" value="{{ old('penanggung_jawab') }}"
                            placeholder="Contoh: Kepala Bidang Persandian dan Keamanan Informasi"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pejabat Pelindung Data (PPD)</label>
                        <input type="text" name="ppd" value="{{ old('ppd') }}"
                            placeholder="Contoh: Kepala Dinas Kominfos Prov. Bali"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                            Tanggal Penyusunan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_penyusunan" value="{{ old('tanggal_penyusunan', now()->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Versi</label>
                        <input type="text" name="versi" value="{{ old('versi', '1.0') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            {{-- A. THRESHOLD --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">A. Threshold Analysis</p>
                <p class="text-xs text-gray-400 mb-3">Otomatis dari indikator risiko RoPA yang dipilih. Isi keterangan untuk setiap trigger.</p>
                <div id="thresholdContainer">
                    <p class="text-xs text-gray-400 italic">Pilih RoPA terlebih dahulu untuk melihat threshold.</p>
                </div>
            </div>

            {{-- B. TIM & KONSULTASI --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">B. Tim yang Terlibat & Konsultasi</p>
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-medium text-gray-600">B.1 Anggota Tim</p>
                        <button type="button" onclick="addTimRow()"
                            class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Anggota
                        </button>
                    </div>
                    <div id="timContainer" class="space-y-2"></div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">B.2 Konsultasi Pemangku Kepentingan</label>
                    <textarea name="konsultasi_stakeholder" rows="3"
                        placeholder="Jelaskan proses konsultasi yang dilakukan dengan pemangku kepentingan internal dan eksternal..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('konsultasi_stakeholder') }}</textarea>
                </div>
            </div>

            {{-- C. ASESMEN RISIKO --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">C. Asesmen Risiko</p>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">C.1 Kriteria Penilaian Risiko</label>
                    <textarea name="kriteria_risiko" rows="3"
                        placeholder="Jelaskan skala Likelihood (Rendah/Sedang/Tinggi) dan Dampak (Rendah/Sedang/Tinggi) yang digunakan..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('kriteria_risiko') }}</textarea>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-medium text-gray-600">C.2 Identifikasi Ancaman & Rencana Mitigasi</label>
                        <button type="button" onclick="addRisikoRow()"
                            class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Risiko
                        </button>
                    </div>
                    <div id="risikoContainer" class="space-y-3"></div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">C.3 Evaluasi Risiko Residual</label>
                    <textarea name="evaluasi_residual" rows="3"
                        placeholder="Jelaskan risiko yang tersisa setelah semua rencana mitigasi diterapkan dan keputusan penerimaan risikonya..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('evaluasi_residual') }}</textarea>
                </div>
            </div>

            {{-- D. KESIMPULAN --}}
            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">D. Kesimpulan & Keputusan</p>
                <textarea name="kesimpulan" rows="4"
                    placeholder="Nyatakan kesimpulan DPIA: apakah aktivitas layak dilanjutkan, keputusan yang diambil, dan syarat-syarat yang harus dipenuhi..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('kesimpulan') }}</textarea>
            </div>

        </div>

        {{-- Save bar --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <a href="{{ route('admin.dpia.index') }}"
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
                Simpan DPIA
            </button>
        </div>

    </div>
</form>

@push('scripts')
<script>
const INDIKATOR_LABELS = @json(\App\Models\DpiaThreshold::INDIKATOR_LABELS);
const ROPA_INDICATORS  = @json($ropaList->mapWithKeys(fn($r) => [
    $r->id => $r->riskIndicators->pluck('indikator')->toArray()
]));

function fillFromRopa(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('namaAktivitas').value = opt.dataset.nama ?? '';
    renderThresholds(select.value);
}

function renderThresholds(ropaId) {
    const container = document.getElementById('thresholdContainer');
    if (!ropaId) {
        container.innerHTML = '<p class="text-xs text-gray-400 italic">Pilih RoPA terlebih dahulu.</p>';
        return;
    }
    const terpenuhi = ROPA_INDICATORS[ropaId] || [];
    let html = '<div class="space-y-2">';
    for (const [val, label] of Object.entries(INDIKATOR_LABELS)) {
        const ya = terpenuhi.includes(val);
        const badge = ya
            ? '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-700">YA</span>'
            : '<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-500">Tidak</span>';
        html += `
        <div class="grid grid-cols-12 gap-3 items-start border border-gray-200 rounded-lg px-3 py-2 bg-gray-50">
            <div class="col-span-1 pt-0.5">${badge}</div>
            <div class="col-span-4 text-xs text-gray-700 pt-1">${label}</div>
            <div class="col-span-7">
                <input type="text" name="threshold_keterangan[${val}]"
                    placeholder="Keterangan / alasan..."
                    class="w-full px-2 py-1.5 border border-gray-300 rounded-lg text-xs
                           focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>`;
    }
    html += '</div>';
    container.innerHTML = html;
}

// Tim rows
function addTimRow() {
    const container = document.getElementById('timContainer');
    const idx = container.querySelectorAll('.tim-row').length;
    const div = document.createElement('div');
    div.className = 'tim-row flex items-center gap-2';
    div.innerHTML = `
        <input type="text" name="tim[${idx}][nama_anggota]"
            placeholder="Nama anggota / instansi"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <input type="text" name="tim[${idx}][peran]"
            placeholder="Peran / jabatan"
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

// Risiko rows
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

function addRisikoRow(data = {}) {
    const container = document.getElementById('risikoContainer');
    const idx = container.querySelectorAll('.risiko-row').length;
    const div = document.createElement('div');
    div.className = 'risiko-row bg-gray-50 rounded-xl border border-gray-200 p-3';

    const likelihoods = ['Rendah','Sedang','Tinggi'];
    const dampaks     = ['Rendah','Sedang','Tinggi'];

    const likeOpts = likelihoods.map(v =>
        `<option value="${v}" ${data.likelihood===v?'selected':''}>${v}</option>`).join('');
    const dampOpts = dampaks.map(v =>
        `<option value="${v}" ${data.dampak===v?'selected':''}>${v}</option>`).join('');

    div.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Ancaman / Sumber Risiko</label>
                <input type="text" name="risiko[${idx}][ancaman]"
                    value="${data.ancaman||''}"
                    placeholder="Contoh: Keterbukaan identitas pelapor ke pihak tidak berwenang"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Likelihood</label>
                <select name="risiko[${idx}][likelihood]" onchange="updateLevel(this)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    ${likeOpts}
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Dampak</label>
                <select name="risiko[${idx}][dampak]" onchange="updateLevel(this)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    ${dampOpts}
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Level Risiko</label>
                <div class="level-badge px-3 py-2 rounded-lg text-xs font-semibold text-center ${LEVEL_CLASS[data.level||'Sedang']}">
                    ${data.level||'Sedang'}
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Rencana Mitigasi</label>
                <textarea name="risiko[${idx}][rencana_mitigasi]" rows="2"
                    placeholder="Jelaskan rencana kontrol atau tindakan mitigasi untuk mengurangi risiko ini..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">${data.mitigasi||''}</textarea>
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

// Init jika old data
@if (old('ropa_activity_id'))
    document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('ropaSelect');
        sel.value = '{{ old('ropa_activity_id') }}';
        fillFromRopa(sel);
    });
@endif
</script>
@endpush

@endsection
