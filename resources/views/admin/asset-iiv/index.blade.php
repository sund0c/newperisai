{{-- resources/views/admin/asset-iiv/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Infrastruktur Informasi Vital (IIV)')

@push('styles')
<style>
    /* Badge IIV */
    .badge-iiv {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .badge-kritis   { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
    .badge-terbatas { background: #fff7ed; color: #ea580c; border: 1px solid #fdba74; }
    .badge-minor    { background: #f0fdf4; color: #16a34a; border: 1px solid #86efac; }
    .badge-belum    { background: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; }

    /* Stat cards */
    .stat-card { transition: transform .15s; }
    .stat-card:hover { transform: translateY(-2px); }

    /* Modal overlay */
    .modal-overlay {
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
    }

    /* Opsi radio IIV */
    .iiv-option { cursor: pointer; transition: all .15s; }
    .iiv-option:hover { border-color: #3b82f6 !important; background: #eff6ff; }
    .iiv-option.selected-kritis   { border-color: #dc2626 !important; background: #fef2f2; }
    .iiv-option.selected-terbatas { border-color: #ea580c !important; background: #fff7ed; }
    .iiv-option.selected-minor    { border-color: #16a34a !important; background: #f0fdf4; }

    .radio-dot-kritis   { background: #dc2626; }
    .radio-dot-terbatas { background: #ea580c; }
    .radio-dot-minor    { background: #16a34a; }

    /* Progress dimensi */
    .dim-row { border-bottom: 1px solid #f1f5f9; }
    .dim-row:last-child { border-bottom: none; }

    /* Toast */
    #toast {
        position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
        transform: translateY(120%); transition: transform .3s ease;
    }
    #toast.show { transform: translateY(0); }
</style>
@endpush

@section('content')
<div class="space-y-5" x-data="iivPage()">

    {{-- Header ──────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Infrastruktur Informasi Vital</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Penilaian dampak aset terhadap 5 dimensi kritikal &mdash; Tahun
                <span class="font-semibold text-indigo-600">{{ $tahunContext->tahun }}</span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.asset-iiv.export-pdf', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm font-medium hover:bg-red-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    {{-- Stat Cards ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        {{-- Total --}}
        <div class="stat-card bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">Total Aset</p>
            <p class="text-3xl font-bold text-slate-800 mt-1">{{ $stats['total'] }}</p>
        </div>
        {{-- Kritis --}}
        <div class="stat-card bg-white rounded-xl border border-red-200 p-4 shadow-sm">
            <p class="text-xs text-red-500 font-medium uppercase tracking-wide">🔴 Kritis</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['kritis'] }}</p>
        </div>
        {{-- Terbatas --}}
        <div class="stat-card bg-white rounded-xl border border-orange-200 p-4 shadow-sm">
            <p class="text-xs text-orange-500 font-medium uppercase tracking-wide">🟠 Terbatas</p>
            <p class="text-3xl font-bold text-orange-600 mt-1">{{ $stats['terbatas'] }}</p>
        </div>
        {{-- Minor --}}
        <div class="stat-card bg-white rounded-xl border border-green-200 p-4 shadow-sm">
            <p class="text-xs text-green-600 font-medium uppercase tracking-wide">🟢 Minor</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['minor'] }}</p>
        </div>
        {{-- Belum dinilai --}}
        <div class="stat-card bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wide">⬜ Belum Dinilai</p>
            <p class="text-3xl font-bold text-slate-400 mt-1">{{ $stats['belum'] }}</p>
        </div>
    </div>

    {{-- Filter Bar ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
        <form method="GET" action="{{ route('admin.asset-iiv.index') }}"
              class="flex flex-wrap items-end gap-3">

            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-slate-500 font-medium mb-1">Cari Aset</label>
                <div class="relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Kode atau nama aset..."
                           class="w-full pl-8 pr-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
            </div>

            {{-- Filter OPD (admin only) --}}
            @if($isAdmin)
            <div class="min-w-[200px]">
                <label class="block text-xs text-slate-500 font-medium mb-1">OPD</label>
                <select name="opd_id"
                        class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">Semua OPD</option>
                    @foreach($opds as $opd)
                        <option value="{{ $opd->id }}" @selected(request('opd_id') == $opd->id)>
                            {{ $opd->nama_opd }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Filter Nilai IIV --}}
            <div class="min-w-[160px]">
                <label class="block text-xs text-slate-500 font-medium mb-1">Nilai IIV</label>
                <select name="nilai_iiv"
                        class="w-full py-2 px-3 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">Semua</option>
                    <option value="3" @selected(request('nilai_iiv') == '3')>🔴 KRITIS</option>
                    <option value="2" @selected(request('nilai_iiv') == '2')>🟠 TERBATAS</option>
                    <option value="1" @selected(request('nilai_iiv') == '1')>🟢 MINOR</option>
                </select>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                Terapkan
            </button>
            @if(request()->hasAny(['search', 'opd_id', 'nilai_iiv']))
            <a href="{{ route('admin.asset-iiv.index') }}"
               class="px-4 py-2 border border-slate-300 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- Tabel ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide w-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">Kode Aset</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">Nama Aset</th>
                        @if($isAdmin)
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">OPD</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">Klasifikasi</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wide">Ops</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wide">Data</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wide">Fin</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wide">Umum</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wide">Ketergt.</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 uppercase tracking-wide">IIV</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assets as $index => $asset)
                    <tr class="hover:bg-slate-50 transition cursor-pointer"
                        @click="openModal({{ json_encode([
                            'id'          => $asset->id,
                            'kode'        => $asset->kode_aset,
                            'nama'        => $asset->nama_aset,
                            'opd'         => optional($asset->opd)->nama_opd ?? '-',
                            'klas'        => optional($asset->klasifikasiAset)->nama_klasifikasi ?? '-',
                            'iiv'         => $asset->iiv ? [
                                'dampak_operasional'    => $asset->iiv->dampak_operasional,
                                'dampak_data_informasi' => $asset->iiv->dampak_data_informasi,
                                'dampak_finansial'      => $asset->iiv->dampak_finansial,
                                'dampak_umum'           => $asset->iiv->dampak_umum,
                                'dampak_ketergantungan' => $asset->iiv->dampak_ketergantungan,
                                'nilai_iiv'             => $asset->iiv->nilai_iiv,
                            ] : null,
                        ]) }})">

                        <td class="px-4 py-3 text-slate-400">
                            {{ $assets->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-indigo-700 font-semibold">
                            {{ $asset->kode_aset }}
                        </td>
                        <td class="px-4 py-3 text-slate-800 font-medium max-w-[200px]">
                            <div class="truncate">{{ $asset->nama_aset }}</div>
                            @if($asset->subKlasifikasiAset)
                            <div class="text-xs text-slate-400 truncate">{{ $asset->subKlasifikasiAset->nama_sub_klasifikasi }}</div>
                            @endif
                        </td>
                        @if($isAdmin)
                        <td class="px-4 py-3 text-slate-600 max-w-[160px]">
                            <div class="truncate text-xs">{{ optional($asset->opd)->nama_opd ?? '-' }}</div>
                        </td>
                        @endif
                        <td class="px-4 py-3">
                            <span class="text-xs text-slate-500">{{ optional($asset->klasifikasiAset)->nama_klasifikasi ?? '-' }}</span>
                        </td>

                        @php
                            $iiv       = $asset->iiv;
                            $labelMap  = \App\Models\AssetIiv::labelMap();
                            $badgeClass = fn($v) => match((int)$v) {
                                3 => 'badge-iiv badge-kritis',
                                2 => 'badge-iiv badge-terbatas',
                                1 => 'badge-iiv badge-minor',
                                default => 'badge-iiv badge-belum',
                            };
                            $shortLabel = fn($v) => match((int)$v) {
                                3 => 'K', 2 => 'T', 1 => 'M', default => '—',
                            };
                        @endphp

                        <td class="px-4 py-3 text-center">
                            <span class="{{ $badgeClass($iiv?->dampak_operasional) }} text-[10px]">
                                {{ $shortLabel($iiv?->dampak_operasional) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $badgeClass($iiv?->dampak_data_informasi) }} text-[10px]">
                                {{ $shortLabel($iiv?->dampak_data_informasi) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $badgeClass($iiv?->dampak_finansial) }} text-[10px]">
                                {{ $shortLabel($iiv?->dampak_finansial) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $badgeClass($iiv?->dampak_umum) }} text-[10px]">
                                {{ $shortLabel($iiv?->dampak_umum) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $badgeClass($iiv?->dampak_ketergantungan) }} text-[10px]">
                                {{ $shortLabel($iiv?->dampak_ketergantungan) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($iiv)
                                <span class="{{ $badgeClass($iiv->nilai_iiv) }}">
                                    {{ $labelMap[$iiv->nilai_iiv] ?? '-' }}
                                </span>
                            @else
                                <span class="badge-iiv badge-belum">Belum</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 11 : 10 }}" class="px-6 py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="font-medium">Tidak ada data aset</p>
                            <p class="text-sm mt-1">Sesuaikan filter pencarian Anda</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($assets->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $assets->links() }}
        </div>
        @endif
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-4 text-xs text-slate-500">
        <span class="font-semibold">Kolom:</span>
        <span><b>Ops</b> = Dampak Operasional</span>
        <span><b>Data</b> = Dampak Data/Informasi</span>
        <span><b>Fin</b> = Dampak Finansial</span>
        <span><b>Umum</b> = Dampak Umum/Sosial</span>
        <span><b>Ketergt.</b> = Dampak Ketergantungan</span>
        <span class="ml-4"><b>K</b>=KRITIS &nbsp; <b>T</b>=TERBATAS &nbsp; <b>M</b>=MINOR</span>
    </div>

    {{-- ================================================================
         MODAL PENILAIAN IIV
    ================================================================= --}}
    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.self="closeModal()"
         style="display:none;">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col"
             @click.stop>

            {{-- Modal Header --}}
            <div class="flex items-start justify-between p-5 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <h2 class="text-lg font-bold text-slate-800">Penilaian IIV</h2>
                    </div>
                    <p class="text-sm text-slate-500 mt-1 ml-10">
                        <span class="font-mono text-indigo-600 font-semibold" x-text="current.kode"></span>
                        &mdash; <span x-text="current.nama"></span>
                    </p>
                </div>
                <button @click="closeModal()"
                        class="text-slate-400 hover:text-slate-600 transition rounded-lg p-1 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body: 5 dimensi --}}
            <div class="overflow-y-auto p-5 space-y-6 flex-1">

                @php
                $dimensions = [
                    ['key' => 'dampak_operasional',    'icon' => '⚙️',  'title' => 'Dampak Operasional',
                     'hint' => 'Seberapa besar gangguan terhadap kelangsungan operasional layanan pemerintahan?'],
                    ['key' => 'dampak_data_informasi', 'icon' => '🗄️',  'title' => 'Dampak Data / Informasi',
                     'hint' => 'Seberapa besar potensi kerugian atas integritas atau kerahasiaan data/informasi?'],
                    ['key' => 'dampak_finansial',      'icon' => '💰',  'title' => 'Dampak Finansial',
                     'hint' => 'Estimasi kerugian finansial jika aset mengalami gangguan atau kegagalan.'],
                    ['key' => 'dampak_umum',           'icon' => '🏙️',  'title' => 'Dampak Umum / Sosial',
                     'hint' => 'Potensi kegaduhan, kepanikan, atau gangguan ketentraman masyarakat.'],
                    ['key' => 'dampak_ketergantungan', 'icon' => '🔗',  'title' => 'Dampak Ketergantungan',
                     'hint' => 'Seberapa besar sistem/layanan lain bergantung pada aset ini?'],
                ];
                @endphp

                @foreach($dimensions as $dim)
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-lg">{{ $dim['icon'] }}</span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-700">{{ $dim['title'] }}</h3>
                            <p class="text-xs text-slate-400">{{ $dim['hint'] }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        @foreach($options[$dim['key']] as $opt)
                        @php
                            $colorClass = match($opt['value']) {
                                3 => 'kritis',
                                2 => 'terbatas',
                                default => 'minor',
                            };
                            $borderNormal = match($opt['value']) {
                                3 => 'border-red-200',
                                2 => 'border-orange-200',
                                default => 'border-green-200',
                            };
                        @endphp
                        <label class="iiv-option relative block border-2 {{ $borderNormal }} rounded-xl p-3 cursor-pointer"
                               :class="form['{{ $dim['key'] }}'] == {{ $opt['value'] }} ? 'selected-{{ $colorClass }}' : ''"
                               @click="form['{{ $dim['key'] }}'] = {{ $opt['value'] }}">
                            <input type="radio"
                                   name="{{ $dim['key'] }}"
                                   value="{{ $opt['value'] }}"
                                   class="sr-only">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-3 h-3 rounded-full radio-dot-{{ $colorClass }}"></div>
                                <span class="text-xs font-bold uppercase tracking-wide
                                    {{ $opt['value'] == 3 ? 'text-red-700' : ($opt['value'] == 2 ? 'text-orange-700' : 'text-green-700') }}">
                                    {{ $opt['label'] }}
                                </span>
                                {{-- checkmark --}}
                                <svg x-show="form['{{ $dim['key'] }}'] == {{ $opt['value'] }}"
                                     class="w-4 h-4 ml-auto {{ $opt['value'] == 3 ? 'text-red-600' : ($opt['value'] == 2 ? 'text-orange-600' : 'text-green-600') }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-snug">{{ $opt['desc'] }}</p>
                        </label>
                        @endforeach
                    </div>
                </div>
                @if(!$loop->last)
                <hr class="border-slate-100">
                @endif
                @endforeach

                {{-- Hasil IIV final (preview) --}}
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-700">Nilai IIV Final</span>
                        <span class="badge-iiv text-sm"
                              :class="{
                                  'badge-kritis'   : previewIiv == 3,
                                  'badge-terbatas' : previewIiv == 2,
                                  'badge-minor'    : previewIiv == 1,
                                  'badge-belum'    : previewIiv == 0,
                              }"
                              x-text="previewIiv == 3 ? 'KRITIS' : (previewIiv == 2 ? 'TERBATAS' : (previewIiv == 1 ? 'MINOR' : '—'))">
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">
                        Nilai tertinggi dari ke-5 dimensi di atas.
                        Satu dimensi KRITIS sudah cukup menjadikan aset ini <b>KRITIS</b>.
                    </p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3 p-5 border-t border-slate-100">
                <button @click="closeModal()"
                        class="px-4 py-2 border border-slate-300 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Batal
                </button>
                <button @click="submitForm()"
                        :disabled="saving || previewIiv == 0"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center gap-2">
                    <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan Penilaian'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

{{-- Toast Notification --}}
<div id="toast"
     class="flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-medium"
     :class="toastType == 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'"
     x-data x-text="$store.toast?.msg ?? ''">
</div>
@endsection

@push('scripts')
<script>
function iivPage() {
    return {
        modalOpen : false,
        saving    : false,
        current   : {},
        form      : {
            dampak_operasional    : 0,
            dampak_data_informasi : 0,
            dampak_finansial      : 0,
            dampak_umum           : 0,
            dampak_ketergantungan : 0,
        },
        toastType : 'success',

        get previewIiv() {
            const vals = Object.values(this.form).map(Number);
            if (vals.includes(0)) return 0;
            return Math.max(...vals);
        },

        openModal(asset) {
            this.current = asset;
            if (asset.iiv) {
                this.form = {
                    dampak_operasional    : asset.iiv.dampak_operasional,
                    dampak_data_informasi : asset.iiv.dampak_data_informasi,
                    dampak_finansial      : asset.iiv.dampak_finansial,
                    dampak_umum           : asset.iiv.dampak_umum,
                    dampak_ketergantungan : asset.iiv.dampak_ketergantungan,
                };
            } else {
                this.form = {
                    dampak_operasional    : 0,
                    dampak_data_informasi : 0,
                    dampak_finansial      : 0,
                    dampak_umum           : 0,
                    dampak_ketergantungan : 0,
                };
            }
            this.modalOpen = true;
        },

        closeModal() {
            this.modalOpen = false;
        },

        async submitForm() {
            if (this.previewIiv === 0) return;
            this.saving = true;
            try {
                const url  = `/admin/asset-iiv/upsert/${this.current.id}`;
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                const res  = await fetch(url, {
                    method  : 'POST',
                    headers : {
                        'Content-Type'     : 'application/json',
                        'X-CSRF-TOKEN'     : csrf,
                        'Accept'           : 'application/json',
                    },
                    body : JSON.stringify(this.form),
                });
                const data = await res.json();
                if (res.ok) {
                    this.showToast('success', data.message ?? 'Berhasil disimpan.');
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    const msg = data.message ?? 'Terjadi kesalahan.';
                    this.showToast('error', msg);
                }
            } catch (e) {
                this.showToast('error', 'Gagal menghubungi server.');
            } finally {
                this.saving = false;
            }
        },

        showToast(type, msg) {
            this.toastType = type;
            const el = document.getElementById('toast');
            el.textContent = msg;
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 3000);
        },
    };
}
</script>
@endpush
