{{-- resources/views/admin/asset-se/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kategorisasi Sistem Elektronik')
@section('page-title', 'Kategorisasi Sistem Elektronik')
@section('page-subtitle', 'Penilaian kategori SE per aset · Versi: ' . (optional($seVersion)->kode ?? '—') . ' · Tahun '
    . (optional($tahunContext)->tahun ?? '-'))

@section('content')

    {{-- ══════════════════════════════════════════════════════════
     STAT CARDS
══════════════════════════════════════════════════════════ --}}
    <div class="mb-4">
        <div class="flex gap-3">

            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Aktif</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Tahun {{ optional($tahunContext)->tahun ?? '-' }}</p>
            </div>

            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($stats['total'] > 0)
                            {{ round(($stats['strategis'] / $stats['total']) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['strategis']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Strategis</p>
            </div>

            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($stats['total'] > 0)
                            {{ round(($stats['tinggi'] / $stats['total']) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-amber-500">{{ number_format($stats['tinggi']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Tinggi</p>
            </div>

            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($stats['total'] > 0)
                            {{ round(($stats['rendah'] / $stats['total']) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-green-600">{{ number_format($stats['rendah']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Rendah</p>
            </div>

            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($stats['total'] > 0)
                            {{ round(($stats['belum'] / $stats['total']) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-400">{{ number_format($stats['belum']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Belum Dinilai</p>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
     NO VERSION WARNING
══════════════════════════════════════════════════════════ --}}
    @if (!$seVersion)
        <div
            class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 shadow-sm mb-4">
            <svg class="h-5 w-5 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.539-1.333-3.308 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>Belum ada versi SE yang aktif. Silakan aktifkan versi di menu <a
                    href="{{ route('admin.master-se.index') }}" class="font-semibold underline">Master SE</a>.</span>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
     FILTER BAR
══════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <form method="GET" action="{{ route('admin.asset-se.index') }}" id="filterForm">
            <div class="px-6 py-4 flex items-center gap-3">

                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau kode aset..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                @if ($isAdmin)
                    <div class="flex-1">
                        <select name="opd_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua OPD</option>
                            @foreach ($opds as $opd)
                                <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                                    {{ $opd->namaopd }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="w-48 shrink-0">
                    <select name="kategori_se"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori SE</option>
                        <option value="STRATEGIS" {{ request('kategori_se') === 'STRATEGIS' ? 'selected' : '' }}>Strategis
                        </option>
                        <option value="TINGGI" {{ request('kategori_se') === 'TINGGI' ? 'selected' : '' }}>Tinggi
                        </option>
                        <option value="RENDAH" {{ request('kategori_se') === 'RENDAH' ? 'selected' : '' }}>Rendah
                        </option>
                        <option value="unassessed" {{ request('kategori_se') === 'unassessed' ? 'selected' : '' }}>Belum
                            Dinilai</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['search', 'opd_id', 'kategori_se']))
                        <a href="{{ route('admin.asset-se.index') }}"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════
     FLASH
══════════════════════════════════════════════════════════ --}}
    @if (session('success'))
        <div
            class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm mb-4">
            <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{!! session('success') !!}</span>
        </div>
    @endif
    @if (session('error'))
        <div
            class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm mb-4">
            <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
     TABLE
══════════════════════════════════════════════════════════ --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        <div class="px-6 py-4 border-b border-gray-100">

            {{-- Toolbar default --}}
            <div id="toolbarDefault" class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Daftar Penilaian Kategorisasi SE</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Menampilkan <strong class="text-gray-700">{{ $assets->count() }}</strong> aset
                        dari total <strong class="text-gray-700">{{ $assets->total() }}</strong> aset
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button id="btnSelectPage" onclick="selectCurrentPage()"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white
                               px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pilih Halaman Ini
                    </button>
                    <button onclick="document.getElementById('modalExportPDF').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50
                               px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Export PDF
                    </button>
                </div>
            </div>

            {{-- Bulk bar --}}
            <div id="bulkBar" class="hidden items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-800"><span id="bulkCount">0</span> aset dipilih</p>
                        <p class="text-xs text-gray-400">Pilih jawaban SE untuk semua aset terpilih</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="openBulkSEModal()"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700
                               px-4 py-2 text-sm font-semibold text-white transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Nilai SE
                    </button>
                    <button onclick="clearAllChecks()"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white
                               hover:bg-gray-50 px-3 py-2 text-sm font-medium text-gray-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal Pilih
                    </button>
                </div>
            </div>

        </div>
        <div class="overflow-x-auto">
            @if ($assets->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2z" />
                    </svg>
                    <p class="text-sm text-gray-400">Belum ada aset ditemukan.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="w-12 px-4 py-3 text-center">
                                <input type="checkbox" id="checkAll" onchange="toggleAllOnPage(this.checked)"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer align-middle">
                            </th>
                            <th
                                class="px-3 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-10">
                                #
                            </th>
                            <th
                                class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36 whitespace-nowrap">
                                @php $isSortKode = request('sort') === 'kode_aset'; @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'kode_aset', 'direction' => $isSortKode && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                                    class="inline-flex items-center gap-1 hover:text-gray-700 transition-colors {{ $isSortKode ? 'text-blue-600' : '' }}">
                                    Kode Aset
                                    @if ($isSortKode)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ request('direction') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                @php $isSortNama = request('sort') === 'nama_aset'; @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_aset', 'direction' => $isSortNama && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                                    class="inline-flex items-center gap-1 hover:text-gray-700 transition-colors {{ $isSortNama ? 'text-blue-600' : '' }}">
                                    Nama Aset
                                    @if ($isSortNama)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ request('direction') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            @if ($isAdmin)
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    OPD
                                </th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Klas / Sub
                                Klas</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                                Skor</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">
                                Kategori SE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="assetTableBody">
                        @forelse($assets as $i => $asset)
                            @php
                                $se = $asset->sePenilaian;
                                $kat = optional($se)->kategori_se;
                                if ($kat === 'STRATEGIS') {
                                    $kategoriClass = 'bg-purple-100 text-purple-700';
                                } elseif ($kat === 'TINGGI') {
                                    $kategoriClass = 'bg-amber-100 text-amber-700';
                                } elseif ($kat === 'RENDAH') {
                                    $kategoriClass = 'bg-green-100 text-green-700';
                                } else {
                                    $kategoriClass = 'bg-gray-100 text-gray-400';
                                }
                                $kategoriLabel = $kat ?? 'Belum dinilai';
                                $jawabansJson = $se ? json_encode($se->jawabans) : 'null';
                            @endphp
                            <tr class="asset-row hover:bg-gray-50 transition-colors" data-id="{{ $asset->id }}"
                                data-kode="{{ $asset->kode_aset }}" data-nama="{{ addslashes($asset->nama_aset) }}">

                                <td class="w-12 px-4 py-3 text-center">
                                    <input type="checkbox"
                                        class="row-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600
                                       focus:ring-indigo-500 cursor-pointer align-middle"
                                        value="{{ $asset->id }}" onchange="onRowCheckChange()">
                                </td>

                                <td class="px-3 py-3 text-xs text-gray-400">
                                    {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}
                                </td>

                                {{-- Kode aset — persis pola IIV: onclick inline dengan parameter --}}
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <button type="button"
                                        onclick="openSEModal(
                                    '{{ $asset->id }}',
                                    '{{ addslashes($asset->kode_aset) }}',
                                    '{{ addslashes($asset->nama_aset) }}',
                                    {{ $jawabansJson }}
                                )"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
           bg-indigo-50 text-indigo-600
           border border-indigo-200 transition-colors">
                                        {{ $asset->kode_aset }}
                                    </button>
                                </td>

                                <td class="px-6 py-3 max-w-[200px]">
                                    <div class="text-xs font-medium text-gray-800 font-mono">
                                        {{ $asset->nama_aset ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5" font-mono>{{ $asset->keterangan ?? '' }}
                                    </div>
                                </td>

                                @if ($isAdmin)
                                    <td class="px-6 py-3">
                                        <div class="text-xs font-medium text-gray-800 font-mono">
                                            {{ optional($asset->opd)->namaopd ?? '-' }}
                                        </div>
                                    </td>
                                @endif

                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-800 font-mono">
                                        {{ optional(optional($asset->subKlasifikasi)->klasifikasi)->klasifikasiaset ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono">
                                        {{ optional($asset->subKlasifikasi)->subklasifikasiaset ?? '-' }}</div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($se)
                                        <span
                                            class="text-sm font-bold text-gray-800 font-mono">{{ $se->total_nilai }}</span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $kategoriClass }}">
                                        {{ $kategoriLabel }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 8 : 7 }}"
                                    class="px-6 py-12 text-center text-sm text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span>Tidak ada aset ditemukan</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if ($assets->hasPages())
                    <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                        {{ $assets->withQueryString()->links() }}
                    </div>
                @endif
        </div>

        @if ($assets->total() > 0)
            <p class="mt-2 text-xs text-gray-400 text-right">
                Menampilkan {{ $assets->firstItem() }}–{{ $assets->lastItem() }} dari {{ $assets->total() }} aset
            </p>
        @endif
        @endif

        {{-- ══════════════════════════════════════════════════════════
     MODAL EXPORT PDF
══════════════════════════════════════════════════════════ --}}
        <div id="modalExportPDF" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
            onclick="if(event.target===this) this.classList.add('hidden')">
            <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Export PDF — Kategorisasi SE</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Pilih filter data yang akan diekspor</p>
                    </div>
                    <button onclick="document.getElementById('modalExportPDF').classList.add('hidden')"
                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.asset-se.export-pdf') }}" method="GET" target="_blank"
                    class="px-6 py-5 space-y-4">
                    @if ($isAdmin)
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">OPD</label>
                            <select name="opd_id"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="">Semua OPD</option>
                                @foreach ($opds as $opd)
                                    <option value="{{ $opd->id }}">{{ $opd->namaopd }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kategori SE</label>
                        <select name="kategori_se"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="STRATEGIS">Strategis</option>
                            <option value="TINGGI">Tinggi</option>
                            <option value="RENDAH">Rendah</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-1">
                        <button type="button" onclick="document.getElementById('modalExportPDF').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-300 bg-red-50
                               px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Export PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
     MODAL PENILAIAN SE — individual
══════════════════════════════════════════════════════════ --}}
        <div id="modalSE" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
            onclick="if(event.target===this) closeSEModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col" style="max-height:90vh;">

                <div
                    class="flex-shrink-0 flex items-start justify-between rounded-t-2xl bg-gradient-to-r from-indigo-700 to-indigo-600 px-6 py-4">
                    <div>
                        <p id="seModalKode" class="text-xs font-mono font-semibold text-indigo-200"></p>
                        <h2 id="seModalNama" class="mt-0.5 text-base font-bold text-white"></h2>
                        <p class="mt-1 text-xs text-indigo-100">
                            Penilaian SE · Versi: <span
                                class="font-semibold">{{ optional($seVersion)->kode ?? '—' }}</span>
                            · 10 Indikator · a=5 / b=2 / c=1
                        </p>
                    </div>
                    <button onclick="closeSEModal()"
                        class="ml-4 flex-shrink-0 rounded-lg p-1.5 text-indigo-200 hover:bg-indigo-800 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Progress --}}
                <div class="flex-shrink-0 px-6 pt-3 pb-1 bg-white border-b border-gray-100">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-500">Progress Pengisian</span>
                        <span id="seProgressText" class="text-xs font-semibold text-indigo-600">0 /
                            {{ $seVersion ? $seVersion->indikators->count() : 10 }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div id="seProgressBar" class="h-full bg-indigo-500 rounded-full transition-all duration-300"
                            style="width:0%"></div>
                    </div>
                </div>

                {{-- Indikator list --}}
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3" id="seIndikatorList">
                    @if ($seVersion)
                        @foreach ($seVersion->indikators as $ind)
                            <div class="se-indikator-block rounded-xl border border-gray-200 overflow-hidden"
                                data-indikator-id="{{ $ind->id }}">
                                <div class="flex items-start gap-3 px-4 py-3 bg-gray-50 border-b border-gray-100">
                                    <span
                                        class="inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full
                                             bg-indigo-100 text-xs font-bold text-indigo-600 mt-0.5">
                                        {{ $ind->urutan }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 leading-snug">{{ $ind->pertanyaan }}
                                        </p>
                                        @if ($ind->keterangan)
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $ind->keterangan }}</p>
                                        @endif
                                    </div>
                                    <span
                                        class="se-ind-status flex-shrink-0 inline-flex items-center rounded-full
                                             px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-400">
                                        Belum
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 divide-x divide-gray-100">
                                    @foreach ([['val' => 'a', 'score' => 5, 'color' => 'red', 'label' => 'A', 'pilihan' => $ind->pilihan_1], ['val' => 'b', 'score' => 2, 'color' => 'amber', 'label' => 'B', 'pilihan' => $ind->pilihan_2], ['val' => 'c', 'score' => 1, 'color' => 'green', 'label' => 'C', 'pilihan' => $ind->pilihan_3]] as $opt)
                                        <label
                                            class="se-opt flex flex-col gap-1 p-3 cursor-pointer transition-all
                                                  hover:bg-{{ $opt['color'] }}-50"
                                            data-indikator-id="{{ $ind->id }}" data-val="{{ $opt['val'] }}"
                                            data-color="{{ $opt['color'] }}">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" name="se_ind_{{ $ind->id }}"
                                                    value="{{ $opt['val'] }}"
                                                    class="se-radio h-3.5 w-3.5 flex-shrink-0 border-gray-300"
                                                    onchange="onSERadioChange(this)"
                                                    data-indikator-id="{{ $ind->id }}"
                                                    data-val="{{ $opt['val'] }}" data-score="{{ $opt['score'] }}">
                                                <span
                                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold
                                                         bg-{{ $opt['color'] }}-100 text-{{ $opt['color'] }}-700">
                                                    {{ $opt['label'] }} · {{ $opt['score'] }}
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-gray-500 leading-snug pl-5">{{ $opt['pilihan'] }}
                                            </p>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="py-12 text-center text-sm text-gray-400">
                            Belum ada versi SE aktif.
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div
                    class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4">
                    <div>
                        <div id="sePreviewWrap" class="hidden flex items-center gap-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori SE:</span>
                            <span id="sePreviewBadge"
                                class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold"></span>
                            <span id="sePreviewTotal" class="text-xs text-gray-400"></span>
                        </div>
                        <p id="sePreviewHint" class="text-xs text-gray-400 italic">Jawab semua indikator untuk melihat
                            kategori</p>
                    </div>
                    <div class="flex gap-3 flex-shrink-0">
                        <button type="button" onclick="closeSEModal()"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button id="btnSimpanSE" type="button" disabled onclick="submitSE()"
                            class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm
                               hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all
                               flex items-center gap-2">
                            <svg id="seSpinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                            </svg>
                            Simpan Penilaian
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════
     MODAL BULK SE
══════════════════════════════════════════════════════════ --}}
        <div id="modalBulkSE" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
            onclick="if(event.target===this) closeBulkSEModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col" style="max-height:90vh;">

                <div
                    class="flex-shrink-0 flex items-start justify-between rounded-t-2xl bg-gradient-to-r from-indigo-800 to-indigo-700 px-6 py-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/40 border border-indigo-400/50 px-2.5 py-0.5 text-xs font-semibold text-indigo-100">
                                Penilaian Massal
                            </span>
                        </div>
                        <h2 class="text-base font-bold text-white">
                            Nilai SE untuk <span id="bulkSEModalCount">0</span> Aset
                        </h2>
                        <p class="mt-1 text-xs text-indigo-200">Jawaban yang dipilih akan diterapkan ke semua aset yang
                            dicentang</p>
                    </div>
                    <button onclick="closeBulkSEModal()"
                        class="ml-4 flex-shrink-0 rounded-lg p-1.5 text-indigo-200 hover:bg-indigo-900 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-shrink-0 border-b border-gray-100 bg-gray-50 px-6 py-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Aset yang akan dinilai</p>
                        <button onclick="toggleBulkSEList()" id="btnToggleBulkSEList"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            Lihat daftar ▾
                        </button>
                    </div>
                    <div id="bulkSEAssetList" class="hidden flex flex-wrap gap-1.5 max-h-32 overflow-y-auto"></div>
                </div>

                <div class="flex-shrink-0 px-6 pt-3 pb-1 bg-white border-b border-gray-100">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-500">Progress Pengisian</span>
                        <span id="bulkSEProgressText" class="text-xs font-semibold text-indigo-600">0 /
                            {{ $seVersion ? $seVersion->indikators->count() : 10 }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div id="bulkSEProgressBar" class="h-full bg-indigo-500 rounded-full transition-all duration-300"
                            style="width:0%"></div>
                    </div>
                </div>

                <form id="formBulkSE" method="POST" action="{{ route('admin.asset-se.bulk-update') }}"
                    class="flex flex-col flex-1 min-h-0">
                    @csrf
                    <div id="bulkSEHiddenIds"></div>

                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3" id="bulkSEIndikatorList">
                        @if ($seVersion)
                            @foreach ($seVersion->indikators as $ind)
                                <div class="bulk-se-indikator-block rounded-xl border border-gray-200 overflow-hidden"
                                    data-indikator-id="{{ $ind->id }}">
                                    <div class="flex items-start gap-3 px-4 py-3 bg-gray-50 border-b border-gray-100">
                                        <span
                                            class="inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full
                                                 bg-indigo-100 text-xs font-bold text-indigo-600 mt-0.5">
                                            {{ $ind->urutan }}
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-800 leading-snug">
                                                {{ $ind->pertanyaan }}
                                            </p>
                                            @if ($ind->keterangan)
                                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $ind->keterangan }}</p>
                                            @endif
                                        </div>
                                        <span
                                            class="bulk-se-ind-status flex-shrink-0 inline-flex items-center rounded-full
                                                 px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-400">
                                            Belum
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-3 divide-x divide-gray-100">
                                        @foreach ([['val' => 'a', 'score' => 5, 'color' => 'red', 'label' => 'A', 'pilihan' => $ind->pilihan_1], ['val' => 'b', 'score' => 2, 'color' => 'amber', 'label' => 'B', 'pilihan' => $ind->pilihan_2], ['val' => 'c', 'score' => 1, 'color' => 'green', 'label' => 'C', 'pilihan' => $ind->pilihan_3]] as $opt)
                                            <label
                                                class="bulk-se-opt flex flex-col gap-1 p-3 cursor-pointer transition-all
                                                      hover:bg-{{ $opt['color'] }}-50"
                                                data-indikator-id="{{ $ind->id }}" data-val="{{ $opt['val'] }}"
                                                data-color="{{ $opt['color'] }}">
                                                <div class="flex items-center gap-2">
                                                    <input type="radio" name="{{ $ind->id }}"
                                                        value="{{ $opt['val'] }}"
                                                        class="bulk-se-radio h-3.5 w-3.5 flex-shrink-0 border-gray-300"
                                                        onchange="onBulkSERadioChange(this)"
                                                        data-indikator-id="{{ $ind->id }}"
                                                        data-score="{{ $opt['score'] }}">
                                                    <span
                                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold
                                                             bg-{{ $opt['color'] }}-100 text-{{ $opt['color'] }}-700">
                                                        {{ $opt['label'] }} · {{ $opt['score'] }}
                                                    </span>
                                                </div>
                                                <p class="text-[10px] text-gray-500 leading-snug pl-5">
                                                    {{ $opt['pilihan'] }}
                                                </p>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div
                        class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4">
                        <div>
                            <div id="bulkSEPreviewWrap" class="hidden flex items-center gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori
                                    SE:</span>
                                <span id="bulkSEPreviewBadge"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold"></span>
                                <span id="bulkSEPreviewTotal" class="text-xs text-gray-400"></span>
                            </div>
                            <p id="bulkSEPreviewHint" class="text-xs text-gray-400 italic">Jawab semua indikator untuk
                                melihat
                                kategori</p>
                        </div>
                        <div class="flex gap-3 flex-shrink-0">
                            <button type="button" onclick="closeBulkSEModal()"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button id="btnBulkSimpanSE" type="submit" disabled
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm
                                   hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Simpan ke <span id="btnBulkSECount" class="ml-0.5">0</span> Aset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script>
            const SE_BASE_URL = '{{ url('admin/asset-se') }}';
            const SE_CSRF = '{{ csrf_token() }}';
            const SE_TOTAL_IND = {{ $seVersion ? $seVersion->indikators->count() : 10 }};
            const SE_SCORE_MAP = {
                a: 5,
                b: 2,
                c: 1
            };
            const SE_LABELS = {
                STRATEGIS: 'STRATEGIS',
                TINGGI: 'TINGGI',
                RENDAH: 'RENDAH'
            };
            const SE_CLASSES = {
                STRATEGIS: 'bg-purple-100 text-purple-700',
                TINGGI: 'bg-amber-100 text-amber-700',
                RENDAH: 'bg-green-100 text-green-700',
            };

            let currentSEAssetId = null;

            function hitungKategori(total) {
                if (total >= 35) return 'STRATEGIS';
                if (total >= 16) return 'TINGGI';
                return 'RENDAH';
            }

            // ─── Checkbox ────────────────────────────────────────────

            function getCheckedIds() {
                return [...document.querySelectorAll('.row-checkbox:checked')].map(function(cb) {
                    return cb.value;
                });
            }

            function onRowCheckChange() {
                var checked = document.querySelectorAll('.row-checkbox:checked').length;
                var total = document.querySelectorAll('.row-checkbox').length;
                var ca = document.getElementById('checkAll');
                ca.indeterminate = checked > 0 && checked < total;
                ca.checked = checked === total && total > 0;
                document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                    cb.closest('tr').classList.toggle('bg-indigo-50/40', cb.checked);
                });
                updateBulkBar(checked);
            }

            function toggleAllOnPage(checked) {
                document.querySelectorAll('.row-checkbox').forEach(function(cb) {
                    cb.checked = checked;
                    cb.closest('tr').classList.toggle('bg-indigo-50/40', checked);
                });
                updateBulkBar(checked ? document.querySelectorAll('.row-checkbox').length : 0);
            }

            function selectCurrentPage() {
                var allChecked = document.querySelectorAll('.row-checkbox:checked').length ===
                    document.querySelectorAll('.row-checkbox').length;
                toggleAllOnPage(!allChecked);
                document.getElementById('checkAll').checked = !allChecked;
            }

            function clearAllChecks() {
                toggleAllOnPage(false);
                var ca = document.getElementById('checkAll');
                ca.checked = false;
                ca.indeterminate = false;
            }

            function updateBulkBar(count) {
                var def = document.getElementById('toolbarDefault');
                var bulk = document.getElementById('bulkBar');
                document.getElementById('bulkCount').textContent = count;
                if (count > 0) {
                    def.classList.add('hidden');
                    bulk.classList.remove('hidden');
                    bulk.classList.add('flex');
                } else {
                    def.classList.remove('hidden');
                    bulk.classList.add('hidden');
                    bulk.classList.remove('flex');
                }
            }

            document.getElementById('filterForm').addEventListener('submit', clearAllChecks);

            // ─── Individual Modal ─────────────────────────────────────

            function openSEModal(assetId, kode, nama, jawabans) {
                currentSEAssetId = assetId;
                document.getElementById('seModalKode').textContent = kode;
                document.getElementById('seModalNama').textContent = nama;

                // Reset
                document.querySelectorAll('#seIndikatorList .se-radio').forEach(function(r) {
                    r.checked = false;
                });
                document.querySelectorAll('#seIndikatorList .se-opt').forEach(function(lbl) {
                    lbl.classList.remove('bg-red-50', 'bg-amber-50', 'bg-green-50');
                });
                document.querySelectorAll('#seIndikatorList .se-ind-status').forEach(function(s) {
                    s.textContent = 'Belum';
                    s.className =
                        'se-ind-status flex-shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-400';
                });

                // Prefill jawaban lama
                if (jawabans && typeof jawabans === 'object') {
                    for (var indId in jawabans) {
                        var val = jawabans[indId];
                        var radio = document.querySelector('#seIndikatorList input[name="se_ind_' + indId + '"][value="' + val +
                            '"]');
                        if (radio) {
                            radio.checked = true;
                            highlightSEOpt(radio.closest('.se-opt'));
                            updateIndikatorStatus(indId, val, '#seIndikatorList', '.se-ind-status');
                        }
                    }
                }

                updateSEProgress();
                updateSEPreview();
                document.getElementById('modalSE').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeSEModal() {
                document.getElementById('modalSE').classList.add('hidden');
                document.body.style.overflow = '';
            }

            // ─── Bulk Modal ───────────────────────────────────────────

            function openBulkSEModal() {
                var ids = getCheckedIds();
                if (ids.length === 0) return;

                document.getElementById('bulkSEModalCount').textContent = ids.length;
                document.getElementById('btnBulkSECount').textContent = ids.length;

                var listEl = document.getElementById('bulkSEAssetList');
                var hiddenEl = document.getElementById('bulkSEHiddenIds');
                listEl.innerHTML = '';
                hiddenEl.innerHTML = '';

                ids.forEach(function(id) {
                    var row = document.querySelector('.asset-row[data-id="' + id + '"]');
                    var kode = row ? row.dataset.kode : id;
                    var nama = row ? row.dataset.nama : '';

                    var chip = document.createElement('span');
                    chip.className =
                        'inline-flex items-center gap-1 rounded-lg bg-white border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-700 shadow-sm';
                    chip.innerHTML = '<span class="font-mono text-indigo-600 font-semibold">' + kode + '</span>';
                    if (nama) chip.title = nama;
                    listEl.appendChild(chip);

                    var inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = 'asset_ids[]';
                    inp.value = id;
                    hiddenEl.appendChild(inp);
                });

                // Reset bulk form
                document.querySelectorAll('#bulkSEIndikatorList .bulk-se-radio').forEach(function(r) {
                    r.checked = false;
                });
                document.querySelectorAll('#bulkSEIndikatorList .bulk-se-opt').forEach(function(lbl) {
                    lbl.classList.remove('bg-red-50', 'bg-amber-50', 'bg-green-50');
                });
                document.querySelectorAll('#bulkSEIndikatorList .bulk-se-ind-status').forEach(function(s) {
                    s.textContent = 'Belum';
                    s.className =
                        'bulk-se-ind-status flex-shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-400';
                });

                updateBulkSEProgress();
                document.getElementById('bulkSEPreviewWrap').classList.add('hidden');
                document.getElementById('bulkSEPreviewHint').classList.remove('hidden');
                document.getElementById('btnBulkSimpanSE').disabled = true;
                document.getElementById('modalBulkSE').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeBulkSEModal() {
                document.getElementById('modalBulkSE').classList.add('hidden');
                document.body.style.overflow = '';
            }

            function toggleBulkSEList() {
                var list = document.getElementById('bulkSEAssetList');
                var btn = document.getElementById('btnToggleBulkSEList');
                list.classList.toggle('hidden');
                btn.textContent = list.classList.contains('hidden') ? 'Lihat daftar ▾' : 'Sembunyikan ▴';
            }

            // ─── Helpers ──────────────────────────────────────────────

            function highlightSEOpt(label) {
                if (!label) return;
                var color = label.dataset.color;
                var block = label.closest('.se-indikator-block, .bulk-se-indikator-block');
                if (block) {
                    block.querySelectorAll('.se-opt, .bulk-se-opt').forEach(function(l) {
                        l.classList.remove('bg-red-50', 'bg-amber-50', 'bg-green-50');
                    });
                }
                label.classList.add('bg-' + color + '-50');
            }

            function updateIndikatorStatus(indId, val, scope, statusSel) {
                var scoreLabels = {
                    a: 'A·5',
                    b: 'B·2',
                    c: 'C·1'
                };
                var colorMap = {
                    a: 'bg-red-100 text-red-700',
                    b: 'bg-amber-100 text-amber-700',
                    c: 'bg-green-100 text-green-700'
                };
                var blocks = document.querySelectorAll(scope + ' [data-indikator-id="' + indId + '"].se-indikator-block, ' +
                    scope + ' [data-indikator-id="' + indId + '"].bulk-se-indikator-block');
                blocks.forEach(function(blk) {
                    var st = blk.querySelector(statusSel);
                    if (!st) return;
                    st.textContent = scoreLabels[val] || val;
                    st.className = st.className.replace(/bg-\S+/g, '').replace(/text-\S+/g, '').trim() +
                        ' ' + (colorMap[val] || 'bg-gray-100 text-gray-400');
                });
            }

            function onSERadioChange(radio) {
                highlightSEOpt(radio.closest('.se-opt'));
                updateIndikatorStatus(radio.dataset.indikatorId, radio.dataset.val, '#seIndikatorList', '.se-ind-status');
                updateSEProgress();
                updateSEPreview();
            }

            function onBulkSERadioChange(radio) {
                highlightSEOpt(radio.closest('.bulk-se-opt'));
                updateIndikatorStatus(radio.dataset.indikatorId, radio.value, '#bulkSEIndikatorList', '.bulk-se-ind-status');
                updateBulkSEProgress();
                updateBulkSEPreview();
            }

            function updateSEProgress() {
                var answered = new Set();
                document.querySelectorAll('#seIndikatorList .se-radio:checked').forEach(function(r) {
                    answered.add(r.dataset.indikatorId);
                });
                var count = answered.size;
                var pct = SE_TOTAL_IND > 0 ? Math.round((count / SE_TOTAL_IND) * 100) : 0;
                document.getElementById('seProgressBar').style.width = pct + '%';
                document.getElementById('seProgressText').textContent = count + ' / ' + SE_TOTAL_IND;
            }

            function updateBulkSEProgress() {
                var answered = new Set();
                document.querySelectorAll('#bulkSEIndikatorList .bulk-se-radio:checked').forEach(function(r) {
                    answered.add(r.dataset.indikatorId);
                });
                var count = answered.size;
                var pct = SE_TOTAL_IND > 0 ? Math.round((count / SE_TOTAL_IND) * 100) : 0;
                document.getElementById('bulkSEProgressBar').style.width = pct + '%';
                document.getElementById('bulkSEProgressText').textContent = count + ' / ' + SE_TOTAL_IND;
            }

            function collectAnswers(scope, radioSel) {
                var jawabans = {};
                document.querySelectorAll(scope + ' ' + radioSel + ':checked').forEach(function(r) {
                    jawabans[r.dataset.indikatorId] = r.value;
                });
                return jawabans;
            }

            function renderPreview(total, wrapId, hintId, badgeId, totalId, btnId) {
                var kat = hitungKategori(total);
                var badge = document.getElementById(badgeId);
                badge.textContent = SE_LABELS[kat];
                badge.className = 'inline-flex items-center rounded-full px-3 py-1 text-sm font-bold ' + SE_CLASSES[kat];
                var tot = document.getElementById(totalId);
                if (tot) tot.textContent = 'Total nilai: ' + total + ' / 50';
                document.getElementById(wrapId).classList.remove('hidden');
                document.getElementById(hintId).classList.add('hidden');
                var btn = document.getElementById(btnId);
                if (btn) btn.disabled = false;
            }

            function updateSEPreview() {
                var jawabans = collectAnswers('#seIndikatorList', '.se-radio');
                if (Object.keys(jawabans).length < SE_TOTAL_IND) {
                    document.getElementById('sePreviewWrap').classList.add('hidden');
                    document.getElementById('sePreviewHint').classList.remove('hidden');
                    document.getElementById('btnSimpanSE').disabled = true;
                    return;
                }
                var total = 0;
                for (var k in jawabans) {
                    total += SE_SCORE_MAP[jawabans[k]] || 0;
                }
                renderPreview(total, 'sePreviewWrap', 'sePreviewHint', 'sePreviewBadge', 'sePreviewTotal', 'btnSimpanSE');
            }

            function updateBulkSEPreview() {
                var jawabans = collectAnswers('#bulkSEIndikatorList', '.bulk-se-radio');
                if (Object.keys(jawabans).length < SE_TOTAL_IND) {
                    document.getElementById('bulkSEPreviewWrap').classList.add('hidden');
                    document.getElementById('bulkSEPreviewHint').classList.remove('hidden');
                    document.getElementById('btnBulkSimpanSE').disabled = true;
                    return;
                }
                var total = 0;
                for (var k in jawabans) {
                    total += SE_SCORE_MAP[jawabans[k]] || 0;
                }
                renderPreview(total, 'bulkSEPreviewWrap', 'bulkSEPreviewHint', 'bulkSEPreviewBadge', 'bulkSEPreviewTotal',
                    'btnBulkSimpanSE');
            }

            // ─── Submit individual (AJAX) ─────────────────────────────

            function submitSE() {
                var jawabans = collectAnswers('#seIndikatorList', '.se-radio');
                if (Object.keys(jawabans).length < SE_TOTAL_IND) return;

                var btn = document.getElementById('btnSimpanSE');
                var spinner = document.getElementById('seSpinner');
                btn.disabled = true;
                spinner.classList.remove('hidden');

                fetch(SE_BASE_URL + '/' + currentSEAssetId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': SE_CSRF,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(jawabans),
                    })
                    .then(function(res) {
                        return res.json().then(function(data) {
                            return {
                                ok: res.ok,
                                data: data
                            };
                        });
                    })
                    .then(function(r) {
                        if (r.ok) {
                            closeSEModal();
                            window.location.reload();
                        } else {
                            alert(r.data.message || 'Terjadi kesalahan.');
                            btn.disabled = false;
                        }
                    })
                    .catch(function() {
                        alert('Gagal menghubungi server.');
                        btn.disabled = false;
                    })
                    .finally(function() {
                        spinner.classList.add('hidden');
                    });
            }

            // ─── Keyboard ─────────────────────────────────────────────

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSEModal();
                    closeBulkSEModal();
                }
            });
        </script>
    @endpush
