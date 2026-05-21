{{-- resources/views/admin/asset-iiv/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Infrastruktur Informasi Vital')
@section('page-title', 'Infrastruktur Informasi Vital')
@section('page-subtitle', 'Penilaian dampak aset terhadap 5 dimensi kritikal · Tahun ' . ($tahunContext?->tahun ?? '-'))

@section('content')

    {{-- ══════════════════════════════════════════════════════════
     STAT CARDS
══════════════════════════════════════════════════════════ --}}
    <div class="mb-4">
        <div class="flex gap-3">

            {{-- Total Aktif --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Aktif</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($stats['total']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Tahun {{ $tahunContext?->tahun ?? '-' }}</p>
            </div>

            {{-- Vital --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($stats['total'] > 0)
                            {{ round(($stats['vital'] / $stats['total']) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-red-600">{{ number_format($stats['vital']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Vital</p>
            </div>

            {{-- Tidak Vital --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($stats['total'] > 0)
                            {{ round(($stats['tidak_vital'] / $stats['total']) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-green-600">{{ number_format($stats['tidak_vital']) }}</p>
                <p class="text-xs text-gray-400 mt-1">Tidak Vital</p>
            </div>

            {{-- Belum Dinilai --}}
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
     FILTER BAR
══════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <form method="GET" action="{{ route('admin.asset-iiv.index') }}" id="filterForm">
            <div class="px-6 py-4 flex items-center gap-3">

                {{-- Search --}}
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau kode aset..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- OPD (admin only) --}}
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

                {{-- Nilai IIV --}}
                <div class="w-44 shrink-0">
                    <select name="nilai_iiv"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Nilai IIV</option>
                        <option value="2" {{ request('nilai_iiv') == '2' ? 'selected' : '' }}>Vital</option>
                        <option value="1" {{ request('nilai_iiv') == '1' ? 'selected' : '' }}>Tidak Vital</option>
                        <option value="unassessed" {{ request('nilai_iiv') === 'unassessed' ? 'selected' : '' }}>Belum
                            Dinilai</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['search', 'opd_id', 'nilai_iiv']))
                        <a href="{{ route('admin.asset-iiv.index') }}"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════════════════════
     FLASH MESSAGE
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

    {{-- ══════════════════════════════════════════════════════════
     TABLE
══════════════════════════════════════════════════════════ --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100">

            {{-- Row default --}}
            <div id="toolbarDefault" class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Daftar Penilaian IIV</p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 13h4M10 17h4M10 9h1" />
                        </svg>
                        Export PDF
                    </button>
                </div>
            </div>

            {{-- Row bulk — menggantikan default saat ada yang dicentang --}}
            <div id="bulkBar" class="hidden items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            <span id="bulkCount">0</span> aset dipilih
                        </p>
                        <p class="text-xs text-gray-400">Pilih nilai IIV yang akan diterapkan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="openBulkIIVModal()"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-700
                                   px-4 py-2 text-sm font-semibold text-white transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Nilai IIV
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
                            {{-- Checkbox Select All --}}
                            <th class="w-12 px-4 py-3 text-center">
                                <input type="checkbox" id="checkAll" onchange="toggleAllOnPage(this.checked)"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600
                                      focus:ring-indigo-500 cursor-pointer align-middle">
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
                                class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Ops</th>
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Data</th>
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Fin</th>
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Umum</th>
                            <th
                                class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Ketergt.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                IIV</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="assetTableBody">
                        @forelse($assets as $i => $asset)
                            @php
                                $iiv = $asset->iiv;
                                $dimBadge = function ($v) {
                                    return match ((int) $v) {
                                        3 => ['label' => 'K', 'class' => 'bg-red-100 text-red-700'],
                                        2 => ['label' => 'T', 'class' => 'bg-amber-100 text-amber-700'],
                                        1 => ['label' => 'M', 'class' => 'bg-green-100 text-green-700'],
                                        default => ['label' => '–', 'class' => 'bg-gray-100 text-gray-400'],
                                    };
                                };
                                $dims = [
                                    $iiv?->dampak_operasional,
                                    $iiv?->dampak_data_informasi,
                                    $iiv?->dampak_finansial,
                                    $iiv?->dampak_umum,
                                    $iiv?->dampak_ketergantungan,
                                ];
                            @endphp
                            <tr class="asset-row hover:bg-gray-50 transition-colors" data-id="{{ $asset->id }}"
                                data-kode="{{ $asset->kode_aset }}" data-nama="{{ addslashes($asset->nama_aset) }}"
                                data-ops="{{ $iiv?->dampak_operasional ?? '' }}"
                                data-data="{{ $iiv?->dampak_data_informasi ?? '' }}"
                                data-fin="{{ $iiv?->dampak_finansial ?? '' }}"
                                data-umum="{{ $iiv?->dampak_umum ?? '' }}"
                                data-ktrg="{{ $iiv?->dampak_ketergantungan ?? '' }}">

                                {{-- Checkbox per-row --}}
                                <td class="w-12 px-4 py-3 text-center">
                                    <input type="checkbox"
                                        class="row-checkbox h-4 w-4 rounded border-gray-300 text-indigo-600
                                          focus:ring-indigo-500 cursor-pointer align-middle"
                                        value="{{ $asset->id }}" onchange="onRowCheckChange()">
                                </td>

                                <td class="px-3 py-3 text-xs text-gray-400">
                                    {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}
                                </td>

                                {{-- Kode Aset — trigger modal individual --}}
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <button type="button"
                                        onclick="openIIVModal(
                                    '{{ $asset->id }}',
                                    '{{ addslashes($asset->kode_aset) }}',
                                    '{{ addslashes($asset->nama_aset) }}',
                                    {{ $iiv?->dampak_operasional ?? 'null' }},
                                    {{ $iiv?->dampak_data_informasi ?? 'null' }},
                                    {{ $iiv?->dampak_finansial ?? 'null' }},
                                    {{ $iiv?->dampak_umum ?? 'null' }},
                                    {{ $iiv?->dampak_ketergantungan ?? 'null' }}
                                )"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
           bg-indigo-50 text-indigo-600
           border border-indigo-200 transition-colors">
                                        {{ $asset->kode_aset }}
                                    </button>
                                </td>

                                {{-- Nama Aset --}}
                                <td class="px-6 py-3 max-w-[200px]">
                                    <div class="text-xs font-medium font-mono text-gray-800">
                                        {{ $asset->nama_aset ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $asset->keterangan ?? '' }}</div>
                                </td>

                                {{-- OPD --}}
                                @if ($isAdmin)
                                    <td class="px-6 py-3">
                                        <div class="text-xs font-medium text-gray-800 font-mono">
                                            {{ $asset->opd?->namaopd ?? '-' }}
                                        </div>
                                    </td>
                                @endif

                                {{-- Klas / Sub Klas --}}
                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-800 font-mono">
                                        {{ $asset->subKlasifikasi->klasifikasi->klasifikasiaset ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">
                                        {{ $asset->subKlasifikasi->subklasifikasiaset ?? '-' }}</div>
                                </td>

                                {{-- 5 dimensi --}}
                                @foreach ($dims as $dimVal)
                                    @php $b = $dimBadge($dimVal); @endphp
                                    <td class="px-3 py-3 text-center">
                                        <span
                                            class="inline-flex h-6 w-6 items-center justify-center rounded-md text-xs font-bold {{ $b['class'] }}">
                                            {{ $b['label'] }}
                                        </span>
                                    </td>
                                @endforeach

                                {{-- Nilai IIV --}}
                                <td class="px-4 py-3 text-center">
                                    @if ($iiv)
                                        @php
                                            $nilaiClass = match ($iiv->nilai_iiv) {
                                                2 => 'bg-red-100 text-red-700',
                                                1 => 'bg-green-100 text-green-700',
                                                default => 'bg-gray-100 text-gray-400',
                                            };
                                            $nilaiLabel = match ($iiv->nilai_iiv) {
                                                2 => 'VITAL',
                                                1 => 'TIDAK VITAL',
                                                default => '-',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $nilaiClass }}">
                                            {{ $nilaiLabel }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-400">
                                            Belum dinilai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdmin ? 12 : 11 }}"
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
                        <h3 class="text-sm font-semibold text-gray-800">Export PDF — Infrastruktur Informasi Vital</h3>
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
                <form action="{{ route('admin.asset-iiv.export-pdf') }}" method="GET" target="_blank"
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
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nilai IIV</label>
                        <select name="nilai_iiv"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="2">Vital</option>
                            <option value="1">Tidak Vital</option>
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
     MODAL PENILAIAN IIV — individual (klik kode aset)
══════════════════════════════════════════════════════════ --}}
        <div id="modalIIV" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
            onclick="if(event.target===this) closeIIVModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl flex flex-col" style="max-height:90vh;">

                {{-- Header --}}
                <div
                    class="flex-shrink-0 flex items-start justify-between rounded-t-2xl bg-gradient-to-r from-indigo-700 to-indigo-600 px-6 py-4">
                    <div>
                        <p id="iivModalKode" class="text-xs font-mono font-semibold text-indigo-200"></p>
                        <h2 id="iivModalNama" class="mt-0.5 text-base font-bold text-white"></h2>
                        <p class="mt-1 text-xs text-indigo-100">Penilaian IIV — 5 Dimensi Dampak Infrastruktur Informasi
                            Vital
                        </p>
                    </div>
                    <button onclick="closeIIVModal()"
                        class="ml-4 flex-shrink-0 rounded-lg p-1.5 text-indigo-200 hover:bg-indigo-800 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- 5 Kolom dimensi --}}
                <div class="grid grid-cols-5 divide-x divide-gray-100 overflow-y-auto flex-1">
                    @php
                        $dimensiConfig = [
                            [
                                'key' => 'dampak_operasional',
                                'icon' => 'Ops',
                                'title' => 'Dampak Operasional',
                                'sub' => 'Gangguan kelangsungan layanan',
                                'color' => 'red',
                            ],
                            [
                                'key' => 'dampak_data_informasi',
                                'icon' => 'Data',
                                'title' => 'Dampak Data / Informasi',
                                'sub' => 'Kerugian atas data/informasi',
                                'color' => 'purple',
                            ],
                            [
                                'key' => 'dampak_finansial',
                                'icon' => 'Fin',
                                'title' => 'Dampak Finansial',
                                'sub' => 'Estimasi kerugian finansial',
                                'color' => 'amber',
                            ],
                            [
                                'key' => 'dampak_umum',
                                'icon' => 'Umum',
                                'title' => 'Dampak Umum / Sosial',
                                'sub' => 'Potensi kegaduhan masyarakat',
                                'color' => 'orange',
                            ],
                            [
                                'key' => 'dampak_ketergantungan',
                                'icon' => 'Ketergt.',
                                'title' => 'Dampak Ketergantungan',
                                'sub' => 'Cascading failure sistem lain',
                                'color' => 'blue',
                            ],
                        ];
                    @endphp

                    @foreach ($dimensiConfig as $dim)
                        <div class="p-4 space-y-2">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                <span
                                    class="inline-flex h-7 px-2 items-center justify-center rounded-lg
                                     bg-{{ $dim['color'] }}-100 text-xs font-bold text-{{ $dim['color'] }}-700 whitespace-nowrap">
                                    {{ $dim['icon'] }}
                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-900 leading-tight">{{ $dim['title'] }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $dim['sub'] }}</p>
                                </div>
                            </div>
                            @foreach ([3 => ['KRITIS', 'red'], 2 => ['TERBATAS', 'amber'], 1 => ['MINOR', 'green']] as $val => [$badgeLabel, $color])
                                <label
                                    class="iiv-opt flex items-start gap-2 rounded-xl border-2 border-gray-200 p-2.5 cursor-pointer transition-all
                                       hover:border-{{ $color }}-300 hover:bg-{{ $color }}-50"
                                    data-group="{{ $dim['key'] }}" data-value="{{ $val }}"
                                    data-color="{{ $color }}">
                                    <input type="radio" name="{{ $dim['key'] }}" value="{{ $val }}"
                                        class="mt-0.5 h-3.5 w-3.5 flex-shrink-0 border-gray-300"
                                        onchange="updateIIVPreview()">
                                    <div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold
                                             bg-{{ $color }}-100 text-{{ $color }}-700">
                                            {{ $badgeLabel }}
                                        </span>
                                        <p class="mt-1 text-[10px] text-gray-500 leading-snug">
                                            {{ collect($options[$dim['key']])->firstWhere('value', $val)['desc'] ?? '' }}
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div
                    class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4">
                    <div>
                        <div id="iivPreviewWrap" class="hidden flex items-center gap-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nilai IIV
                                Final:</span>
                            <span id="iivPreviewBadge"
                                class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold"></span>
                            <span class="text-xs text-gray-400">= nilai tertinggi dari 5 dimensi</span>
                        </div>
                        <p id="iivPreviewHint" class="text-xs text-gray-400 italic">Pilih nilai semua dimensi untuk
                            preview
                            hasil IIV</p>
                    </div>
                    <div class="flex gap-3 flex-shrink-0">
                        <button type="button" onclick="closeIIVModal()"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button id="btnSimpanIIV" type="button" disabled onclick="submitIIV()"
                            class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm
                               hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all
                               flex items-center gap-2">
                            <svg id="iivSpinner" class="hidden w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
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
     BULK IIV MODAL — penilaian massal
══════════════════════════════════════════════════════════ --}}
        <div id="modalBulkIIV" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
            onclick="if(event.target===this) closeBulkIIVModal()">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl flex flex-col" style="max-height:90vh;">

                {{-- Header --}}
                <div
                    class="flex-shrink-0 flex items-start justify-between rounded-t-2xl bg-gradient-to-r from-indigo-800 to-indigo-700 px-6 py-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/40 border border-indigo-400/50 px-2.5 py-0.5 text-xs font-semibold text-indigo-100">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                                </svg>
                                Penilaian Massal
                            </span>
                        </div>
                        <h2 class="text-base font-bold text-white">
                            Nilai IIV untuk <span id="bulkModalCount">0</span> Aset
                        </h2>
                        <p class="mt-1 text-xs text-indigo-200">Nilai IIV yang dipilih akan diterapkan ke semua aset yang
                            dicentang</p>
                    </div>
                    <button onclick="closeBulkIIVModal()"
                        class="ml-4 flex-shrink-0 rounded-lg p-1.5 text-indigo-200 hover:bg-indigo-900 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Daftar aset terpilih (collapsible) --}}
                <div class="flex-shrink-0 border-b border-gray-100 bg-gray-50 px-6 py-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Aset yang akan dinilai</p>
                        <button onclick="toggleBulkList()" id="btnToggleBulkList"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                            Lihat daftar ▾
                        </button>
                    </div>
                    <div id="bulkAssetList" class="hidden flex flex-wrap gap-1.5 max-h-32 overflow-y-auto">
                        {{-- Diisi via JS --}}
                    </div>
                </div>

                {{-- Form: 5 kolom dimensi --}}
                <form id="formBulkIIV" method="POST" action="{{ route('admin.asset-iiv.bulk-update') }}"
                    class="flex flex-col flex-1 min-h-0">
                    @csrf

                    <div id="bulkHiddenIds"></div>

                    <div class="grid grid-cols-5 divide-x divide-gray-100 overflow-y-auto flex-1">
                        @foreach ($dimensiConfig as $dim)
                            <div class="p-4 space-y-2">
                                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                    <span
                                        class="inline-flex h-7 px-2 items-center justify-center rounded-lg
                                         bg-{{ $dim['color'] }}-100 text-xs font-bold text-{{ $dim['color'] }}-700 whitespace-nowrap">
                                        {{ $dim['icon'] }}
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold text-gray-900 leading-tight">{{ $dim['title'] }}
                                        </p>
                                        <p class="text-[10px] text-gray-400">{{ $dim['sub'] }}</p>
                                    </div>
                                </div>
                                @foreach ([3 => ['KRITIS', 'red'], 2 => ['TERBATAS', 'amber'], 1 => ['MINOR', 'green']] as $val => [$badgeLabel, $color])
                                    <label
                                        class="bulk-iiv-opt flex items-start gap-2 rounded-xl border-2 border-gray-200 p-2.5 cursor-pointer transition-all
                                           hover:border-{{ $color }}-300 hover:bg-{{ $color }}-50"
                                        data-group="bulk_{{ $dim['key'] }}" data-value="{{ $val }}"
                                        data-color="{{ $color }}">
                                        <input type="radio" name="{{ $dim['key'] }}" value="{{ $val }}"
                                            class="mt-0.5 h-3.5 w-3.5 flex-shrink-0 border-gray-300"
                                            onchange="updateBulkIIVPreview()">
                                        <div>
                                            <span
                                                class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold
                                                 bg-{{ $color }}-100 text-{{ $color }}-700">
                                                {{ $badgeLabel }}
                                            </span>
                                            <p class="mt-1 text-[10px] text-gray-500 leading-snug">
                                                {{ collect($options[$dim['key']])->firstWhere('value', $val)['desc'] ?? '' }}
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    {{-- Footer --}}
                    <div
                        class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4">
                        <div>
                            <div id="bulkIIVPreviewWrap" class="hidden flex items-center gap-2">
                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nilai IIV
                                    Final:</span>
                                <span id="bulkIIVPreviewBadge"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold"></span>
                                <span class="text-xs text-gray-400">= nilai tertinggi · diterapkan ke semua aset
                                    terpilih</span>
                            </div>
                            <p id="bulkIIVPreviewHint" class="text-xs text-gray-400 italic">Pilih nilai semua dimensi
                                untuk
                                preview hasil IIV</p>
                        </div>
                        <div class="flex gap-3 flex-shrink-0">
                            <button type="button" onclick="closeBulkIIVModal()"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button id="btnBulkSimpanIIV" type="submit" disabled
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm
                                   hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Simpan ke <span id="btnBulkIIVCount" class="ml-0.5">0</span> Aset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script>
            const IIV_BASE_URL = '{{ url('admin/asset-iiv') }}';
            const IIV_CSRF = '{{ csrf_token() }}';
            const IIV_LABELS = {
                2: 'VITAL',
                1: 'TIDAK VITAL'
            };
            const IIV_CLASSES = {
                2: 'bg-red-100 text-red-700',
                1: 'bg-green-100 text-green-700'
            };
            const IIV_DIMS = [
                'dampak_operasional', 'dampak_data_informasi',
                'dampak_finansial', 'dampak_umum', 'dampak_ketergantungan'
            ];

            let currentAssetId = null;

            // ─── Checkbox & Bulk Bar ──────────────────────────────

            function getCheckedIds() {
                return [...document.querySelectorAll('.row-checkbox:checked')].map(cb => cb.value);
            }

            function onRowCheckChange() {
                const checked = document.querySelectorAll('.row-checkbox:checked').length;
                const total = document.querySelectorAll('.row-checkbox').length;
                const checkAll = document.getElementById('checkAll');

                checkAll.indeterminate = checked > 0 && checked < total;
                checkAll.checked = checked === total && total > 0;

                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.closest('tr').classList.toggle('bg-indigo-50/40', cb.checked);
                });

                updateBulkBar(checked);
            }

            function toggleAllOnPage(checked) {
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.checked = checked;
                    cb.closest('tr').classList.toggle('bg-indigo-50/40', checked);
                });
                updateBulkBar(checked ? document.querySelectorAll('.row-checkbox').length : 0);
            }

            function selectCurrentPage() {
                const allChecked = document.querySelectorAll('.row-checkbox:checked').length ===
                    document.querySelectorAll('.row-checkbox').length;
                toggleAllOnPage(!allChecked);
                document.getElementById('checkAll').checked = !allChecked;
            }

            function clearAllChecks() {
                toggleAllOnPage(false);
                document.getElementById('checkAll').checked = false;
                document.getElementById('checkAll').indeterminate = false;
            }

            function updateBulkBar(count) {
                const defaultBar = document.getElementById('toolbarDefault');
                const bulkBar = document.getElementById('bulkBar');
                document.getElementById('bulkCount').textContent = count;

                if (count > 0) {
                    defaultBar.classList.add('hidden');
                    bulkBar.classList.remove('hidden');
                    bulkBar.classList.add('flex');
                } else {
                    defaultBar.classList.remove('hidden');
                    bulkBar.classList.add('hidden');
                    bulkBar.classList.remove('flex');
                }
            }

            document.getElementById('filterForm')?.addEventListener('submit', clearAllChecks);

            // ─── Individual IIV Modal ─────────────────────────────

            function openIIVModal(assetId, kode, nama, ops, data, fin, umum, ktrg) {
                currentAssetId = assetId;
                document.getElementById('iivModalKode').textContent = kode;
                document.getElementById('iivModalNama').textContent = nama;

                resetIIVForm('#modalIIV', '.iiv-opt');

                const existing = {
                    dampak_operasional: ops,
                    dampak_data_informasi: data,
                    dampak_finansial: fin,
                    dampak_umum: umum,
                    dampak_ketergantungan: ktrg,
                };
                for (const [name, val] of Object.entries(existing)) {
                    if (val !== null && val !== undefined) {
                        const radio = document.querySelector(`#modalIIV input[name="${name}"][value="${val}"]`);
                        if (radio) {
                            radio.checked = true;
                            highlightIIVLabel(radio.closest('.iiv-opt'));
                        }
                    }
                }

                updateIIVPreview();
                document.getElementById('modalIIV').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeIIVModal() {
                document.getElementById('modalIIV').classList.add('hidden');
                document.body.style.overflow = '';
            }

            // ─── Bulk IIV Modal ───────────────────────────────────

            function openBulkIIVModal() {
                const ids = getCheckedIds();
                if (ids.length === 0) return;

                document.getElementById('bulkModalCount').textContent = ids.length;
                document.getElementById('btnBulkIIVCount').textContent = ids.length;

                const listEl = document.getElementById('bulkAssetList');
                const hiddenEl = document.getElementById('bulkHiddenIds');
                listEl.innerHTML = '';
                hiddenEl.innerHTML = '';

                ids.forEach(id => {
                    const row = document.querySelector(`.asset-row[data-id="${id}"]`);
                    const kode = row?.dataset.kode ?? id;
                    const nama = row?.dataset.nama ?? '';

                    const chip = document.createElement('span');
                    chip.className =
                        'inline-flex items-center gap-1 rounded-lg bg-white border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-700 shadow-sm';
                    chip.innerHTML = `<span class="font-mono text-indigo-600 font-semibold">${kode}</span>`;
                    if (nama) chip.title = nama;
                    listEl.appendChild(chip);

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'asset_ids[]';
                    input.value = id;
                    hiddenEl.appendChild(input);
                });

                resetIIVForm('#modalBulkIIV', '.bulk-iiv-opt');
                document.getElementById('bulkIIVPreviewWrap').classList.add('hidden');
                document.getElementById('bulkIIVPreviewHint').classList.remove('hidden');
                document.getElementById('btnBulkSimpanIIV').disabled = true;

                document.getElementById('modalBulkIIV').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeBulkIIVModal() {
                document.getElementById('modalBulkIIV').classList.add('hidden');
                document.body.style.overflow = '';
            }

            function toggleBulkList() {
                const list = document.getElementById('bulkAssetList');
                const btn = document.getElementById('btnToggleBulkList');
                list.classList.toggle('hidden');
                btn.textContent = list.classList.contains('hidden') ? 'Lihat daftar ▾' : 'Sembunyikan ▴';
            }

            // ─── Shared helpers ───────────────────────────────────

            function resetIIVForm(scope, optClass) {
                document.querySelectorAll(`${scope} input[type=radio]`).forEach(r => r.checked = false);
                document.querySelectorAll(`${scope} ${optClass}`).forEach(lbl => {
                    lbl.classList.remove(
                        'border-red-500', 'bg-red-50',
                        'border-amber-500', 'bg-amber-50',
                        'border-green-500', 'bg-green-50',
                        'border-purple-500', 'bg-purple-50',
                        'border-orange-500', 'bg-orange-50',
                        'border-blue-500', 'bg-blue-50'
                    );
                    if (!lbl.classList.contains('border-gray-200')) lbl.classList.add('border-gray-200');
                });
            }

            function highlightIIVLabel(label) {
                if (!label) return;
                const group = label.dataset.group;
                const color = label.dataset.color;
                document.querySelectorAll(`[data-group="${group}"]`).forEach(l => {
                    l.classList.remove(
                        'border-red-500', 'bg-red-50',
                        'border-amber-500', 'bg-amber-50',
                        'border-green-500', 'bg-green-50',
                        'border-purple-500', 'bg-purple-50',
                        'border-orange-500', 'bg-orange-50',
                        'border-blue-500', 'bg-blue-50'
                    );
                    if (!l.classList.contains('border-gray-200')) l.classList.add('border-gray-200');
                });
                label.classList.remove('border-gray-200');
                label.classList.add(`border-${color}-500`, `bg-${color}-50`);
            }

            // Attach highlight untuk individual modal
            document.querySelectorAll('#modalIIV .iiv-opt input[type=radio]').forEach(radio => {
                radio.addEventListener('change', function() {
                    highlightIIVLabel(this.closest('.iiv-opt'));
                });
            });

            // Attach highlight untuk bulk modal
            document.querySelectorAll('#modalBulkIIV .bulk-iiv-opt input[type=radio]').forEach(radio => {
                radio.addEventListener('change', function() {
                    highlightIIVLabel(this.closest('.bulk-iiv-opt'));
                });
            });

            function getDimVals(scope) {
                return IIV_DIMS.map(d => {
                    const el = document.querySelector(`${scope} input[name="${d}"]:checked`);
                    return el ? parseInt(el.value) : null;
                });
            }

            function updateIIVPreview() {
                const vals = getDimVals('#modalIIV');
                renderIIVPreview(vals, 'iivPreviewWrap', 'iivPreviewHint', 'iivPreviewBadge', 'btnSimpanIIV');
            }

            function updateBulkIIVPreview() {
                const vals = getDimVals('#modalBulkIIV');
                renderIIVPreview(vals, 'bulkIIVPreviewWrap', 'bulkIIVPreviewHint', 'bulkIIVPreviewBadge', 'btnBulkSimpanIIV');
            }

            function renderIIVPreview(vals, wrapId, hintId, badgeId, btnId) {
                const wrap = document.getElementById(wrapId);
                const hint = document.getElementById(hintId);
                const badge = document.getElementById(badgeId);
                const btn = document.getElementById(btnId);

                if (vals.every(v => v !== null)) {
                    const max = Math.max(...vals);
                    badge.textContent = IIV_LABELS[max] ?? max;
                    badge.className = 'inline-flex items-center rounded-full px-3 py-1 text-sm font-bold ' + (IIV_CLASSES[
                        max] ?? 'bg-gray-100 text-gray-600');
                    wrap.classList.remove('hidden');
                    hint.classList.add('hidden');
                    btn.disabled = false;
                } else {
                    wrap.classList.add('hidden');
                    hint.classList.remove('hidden');
                    btn.disabled = true;
                }
            }

            // ─── Submit individual (AJAX) ─────────────────────────

            async function submitIIV() {
                const payload = {};
                for (const d of IIV_DIMS) {
                    const el = document.querySelector(`#modalIIV input[name="${d}"]:checked`);
                    if (!el) return;
                    payload[d] = parseInt(el.value);
                }

                const btn = document.getElementById('btnSimpanIIV');
                const spinner = document.getElementById('iivSpinner');
                btn.disabled = true;
                spinner.classList.remove('hidden');

                try {
                    const res = await fetch(`${IIV_BASE_URL}/${currentAssetId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': IIV_CSRF,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    if (res.ok) {
                        closeIIVModal();
                        window.location.reload();
                    } else {
                        alert(data.message ?? 'Terjadi kesalahan.');
                        btn.disabled = false;
                    }
                } catch (e) {
                    alert('Gagal menghubungi server.');
                    btn.disabled = false;
                } finally {
                    spinner.classList.add('hidden');
                }
            }

            // ─── Keyboard ─────────────────────────────────────────

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    closeIIVModal();
                    closeBulkIIVModal();
                }
            });
        </script>
    @endpush
