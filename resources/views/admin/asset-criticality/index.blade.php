{{-- resources/views/admin/asset-criticality/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kritikalitas Aset')
@section('page-title', 'Kritikalitas Aset')
@section('page-subtitle', 'Penilaian Confidentiality, Integrity & Availability (CIA) per aset · Tahun ' .
    ($tahunContext?->tahun ?? '-'))

@section('content')

    {{-- ══════════════════════════════════════════════════════════
     STAT CARDS
══════════════════════════════════════════════════════════ --}}
    <div class="mb-4">
        <div class="flex gap-3">

            {{-- Total Aktif --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Aktif</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalAset) }}</p>
                <p class="text-xs text-gray-400 mt-1">Tahun {{ $tahunContext?->tahun ?? '-' }}</p>
            </div>

            {{-- Tinggi --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($totalAset > 0)
                            {{ round(($totalTinggi / $totalAset) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-red-600">{{ number_format($totalTinggi) }}</p>
                <p class="text-xs text-gray-400 mt-1">Tinggi</p>
            </div>

            {{-- Sedang --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($totalAset > 0)
                            {{ round(($totalSedang / $totalAset) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-amber-500">{{ number_format($totalSedang) }}</p>
                <p class="text-xs text-gray-400 mt-1">Sedang</p>
            </div>

            {{-- Rendah --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                <div class="flex items-center justify-end mb-2">
                    <span class="text-xs text-gray-400">
                        @if ($totalAset > 0)
                            {{ round(($totalRendah / $totalAset) * 100) }}%
                        @endif
                    </span>
                </div>
                <p class="text-3xl font-bold text-green-600">{{ number_format($totalRendah) }}</p>
                <p class="text-xs text-gray-400 mt-1">Rendah</p>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
     FILTER BAR
══════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <form method="GET" action="{{ route('admin.asset-criticality.index') }}" id="filterForm">
            <div class="px-6 py-4 flex items-center gap-3">

                {{-- Search --}}
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau kode aset..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- OPD --}}
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

                {{-- Klasifikasi --}}
                <div class="flex-1">
                    <select name="klasifikasi"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Klasifikasi</option>
                        @foreach (['PL' => 'Perangkat Lunak', 'PK' => 'Perangkat Keras', 'DI' => 'Data & Informasi', 'SDM' => 'Sumber Daya Manusia', 'SP' => 'Layanan/Proses'] as $kode => $nama)
                            <option value="{{ $kode }}" {{ request('klasifikasi') === $kode ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kritikalitas --}}
                <div class="w-40 shrink-0">
                    <select name="kritikalitas"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="3" {{ request('kritikalitas') == '3' ? 'selected' : '' }}>Tinggi</option>
                        <option value="2" {{ request('kritikalitas') == '2' ? 'selected' : '' }}>Sedang</option>
                        <option value="1" {{ request('kritikalitas') == '1' ? 'selected' : '' }}>Rendah</option>
                        <option value="unassessed" {{ request('kritikalitas') === 'unassessed' ? 'selected' : '' }}>Belum
                            Dinilai</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['search', 'opd_id', 'klasifikasi', 'kritikalitas']))
                        <a href="{{ route('admin.asset-criticality.index') }}"
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
            class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-sm mb-6">
            <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{!! session('success') !!}</span>
        </div>
    @endif

    {{-- Bulk bar tidak lagi floating — dipindah ke dalam toolbar tabel --}}

    {{-- ══════════════════════════════════════════════════════════
     TABLE
══════════════════════════════════════════════════════════ --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100">

            {{-- Row normal: info + tombol --}}
            <div id="toolbarDefault" class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Status Kritikal Aset</p>
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

            {{-- Row bulk: muncul menggantikan row default saat ada yang dicentang --}}
            <div id="bulkBar" class="hidden items-center justify-between gap-3">
                {{-- Kiri: info terpilih --}}
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            <span id="bulkCount">0</span> aset dipilih
                        </p>
                        <p class="text-xs text-gray-400">Pilih nilai CIA yang akan diterapkan</p>
                    </div>
                </div>
                {{-- Kanan: aksi --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="openBulkCIAModal()"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 hover:bg-blue-700
                               px-4 py-2 text-sm font-semibold text-white transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Nilai CIA
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
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600
                                  focus:ring-blue-500 cursor-pointer align-middle">
                            </th>
                            <th
                                class="px-3 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-10">
                                #
                            </th>
                            <th
                                class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-36 whitespace-nowrap">
                                @php $isSortKode = $sortBy === 'kode_aset'; @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'kode_aset', 'direction' => $isSortKode && $direction === 'asc' ? 'desc' : 'asc']) }}"
                                    class="inline-flex items-center gap-1 hover:text-gray-700 transition-colors {{ $isSortKode ? 'text-blue-600' : '' }}">
                                    Kode Aset
                                    @if ($isSortKode)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $direction === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                @php $isSortNama = $sortBy === 'nama_aset'; @endphp
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_aset', 'direction' => $isSortNama && $direction === 'asc' ? 'desc' : 'asc']) }}"
                                    class="inline-flex items-center gap-1 hover:text-gray-700 transition-colors {{ $isSortNama ? 'text-blue-600' : '' }}">
                                    Nama Aset
                                    @if ($isSortNama)
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $direction === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">OPD
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Klas / Sub
                                Klas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">C
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">I
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">A
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Kritikal
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="assetTableBody">
                        @forelse($assets as $i => $asset)
                            @php
                                $c = $asset->criticality;
                                $map = [
                                    1 => ['label' => 'R', 'class' => 'bg-green-100 text-green-700'],
                                    2 => ['label' => 'S', 'class' => 'bg-amber-100 text-amber-700'],
                                    3 => ['label' => 'T', 'class' => 'bg-red-100 text-red-700'],
                                ];
                            @endphp
                            <tr class="asset-row hover:bg-gray-50 transition-colors" data-id="{{ $asset->id }}"
                                data-kode="{{ $asset->kode_aset }}" data-nama="{{ addslashes($asset->nama_aset) }}"
                                data-c="{{ $c?->confidentiality ?? '' }}" data-i="{{ $c?->integrity ?? '' }}"
                                data-a="{{ $c?->availability ?? '' }}">

                                {{-- Checkbox per-row --}}
                                <td class="w-12 px-4 py-3 text-center">
                                    <input type="checkbox"
                                        class="row-checkbox h-4 w-4 rounded border-gray-300 text-blue-600
                                  focus:ring-blue-500 cursor-pointer align-middle"
                                        value="{{ $asset->id }}" onchange="onRowCheckChange()">
                                </td>

                                <td class="px-3 py-3 text-xs text-gray-400">
                                    {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}
                                </td>

                                {{-- Kode Aset sebagai tombol trigger modal individual --}}
                                <td class="px-3 py-3 whitespace-nowrap">
                                    <button type="button"
                                        onclick="openCIAModal('{{ $asset->id }}', '{{ $asset->kode_aset }}', '{{ addslashes($asset->nama_aset) }}', {{ $c?->confidentiality ?? 'null' }}, {{ $c?->integrity ?? 'null' }}, {{ $c?->availability ?? 'null' }})"
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
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono line-clamp-2">
                                        {{ $asset->keterangan ?? '-' }}</div>
                                </td>

                                {{-- OPD --}}
                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-800 font-mono">
                                        {{ $asset->opd?->namaopd ?? '-' }}
                                    </div>
                                </td>

                                {{-- Klas / Sub Klas --}}
                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-800 font-mono">
                                        {{ $asset->subKlasifikasi->klasifikasi->klasifikasiaset ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">
                                        {{ $asset->subKlasifikasi->subklasifikasiaset ?? '-' }}</div>
                                </td>

                                {{-- C --}}
                                <td class="px-4 py-3 text-center">
                                    @if ($c && isset($map[$c->confidentiality]))
                                        <span
                                            class="inline-flex h-6 w-6 items-center justify-center rounded-md text-xs font-bold {{ $map[$c->confidentiality]['class'] }}"
                                            title="{{ ['1' => 'Rendah', '2' => 'Sedang', '3' => 'Tinggi'][$c->confidentiality] ?? '' }}">
                                            {{ $map[$c->confidentiality]['label'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">–</span>
                                    @endif
                                </td>

                                {{-- I --}}
                                <td class="px-4 py-3 text-center">
                                    @if ($c && isset($map[$c->integrity]))
                                        <span
                                            class="inline-flex h-6 w-6 items-center justify-center rounded-md text-xs font-bold {{ $map[$c->integrity]['class'] }}"
                                            title="{{ ['1' => 'Rendah', '2' => 'Sedang', '3' => 'Tinggi'][$c->integrity] ?? '' }}">
                                            {{ $map[$c->integrity]['label'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">–</span>
                                    @endif
                                </td>

                                {{-- A --}}
                                <td class="px-4 py-3 text-center">
                                    @if ($c && isset($map[$c->availability]))
                                        <span
                                            class="inline-flex h-6 w-6 items-center justify-center rounded-md text-xs font-bold {{ $map[$c->availability]['class'] }}"
                                            title="{{ ['1' => 'Rendah', '2' => 'Sedang', '3' => 'Tinggi'][$c->availability] ?? '' }}">
                                            {{ $map[$c->availability]['label'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">–</span>
                                    @endif
                                </td>

                                {{-- Kritikalitas --}}
                                <td class="px-4 py-3 text-center">
                                    @if ($c)
                                        @php $lvl = $c->kritikalitas; @endphp
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                            {{ $lvl === 3 ? 'bg-red-100 text-red-700' : ($lvl === 2 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $levelLabels[$lvl] ?? '-' }}
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
                                <td colspan="10" class="px-6 py-12 text-center text-sm text-gray-400">
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
                        <h3 class="text-sm font-semibold text-gray-800">Export PDF — Kritikalitas Aset</h3>
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

                <form action="{{ route('admin.asset-criticality.export-pdf') }}" method="GET" target="_blank"
                    class="px-6 py-5 space-y-4">

                    {{-- Tahun --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tahun <span
                                class="text-red-500">*</span></label>
                        <select name="tahun" required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @foreach ($allTahun ?? [] as $t)
                                <option value="{{ $t->id }}"
                                    {{ $tahunContext?->id === $t->id ? 'selected' : '' }}>
                                    {{ $t->tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- OPD --}}
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

                    {{-- Klasifikasi --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Klasifikasi</label>
                        <select name="klasifikasi"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua</option>
                            @foreach (['PL' => 'Perangkat Lunak', 'PK' => 'Perangkat Keras', 'DI' => 'Data & Informasi', 'SDM' => 'Sumber Daya Manusia', 'SP' => 'Layanan/Proses'] as $kode => $nama)
                                <option value="{{ $kode }}">{{ $kode }} – {{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kritikalitas --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kritikalitas</label>
                        <select name="kritikalitas"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua</option>
                            <option value="3">Tinggi</option>
                            <option value="2">Sedang</option>
                            <option value="1">Rendah</option>
                            <option value="unassessed">Belum Dinilai</option>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 13h4M10 17h4M10 9h1" />
                            </svg>
                            Export PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════════
     CIA MODAL — individual (klik kode aset)
══════════════════════════════════════════════════════════ --}}
        <div id="modalCIA" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
            onclick="if(event.target===this) closeCIAModal()">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col" style="max-height:90vh;">

                {{-- Header --}}
                <div
                    class="flex-shrink-0 flex items-start justify-between rounded-t-2xl bg-gradient-to-r from-blue-700 to-blue-600 px-6 py-4">
                    <div>
                        <p id="modalKode" class="text-xs font-mono font-semibold text-blue-200"></p>
                        <h2 id="modalNama" class="mt-0.5 text-base font-bold text-white"></h2>
                        <p class="mt-1 text-xs text-blue-100">Penilaian CIA — Confidentiality · Integrity · Availability
                        </p>
                    </div>
                    <button onclick="closeCIAModal()"
                        class="ml-4 flex-shrink-0 rounded-lg p-1.5 text-blue-200 hover:bg-blue-800 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form --}}
                <form id="formCIA" method="POST" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    @method('PUT')

                    {{-- 3 Kolom CIA --}}
                    <div class="grid grid-cols-3 divide-x divide-gray-100 overflow-y-auto flex-1">

                        {{-- C --}}
                        <div class="p-5 space-y-3">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-purple-100 text-sm font-bold text-purple-700">C</span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Confidentiality</p>
                                    <p class="text-xs text-gray-400">Kerahasiaan</p>
                                </div>
                            </div>
                            @foreach ($ciaOptions['C'] as $opt)
                                <label
                                    class="cia-option flex items-start gap-3 rounded-xl border-2 border-gray-200 p-3 cursor-pointer transition-all hover:border-purple-300 hover:bg-gray-50"
                                    data-group="confidentiality" data-value="{{ $opt['value'] }}" data-color="purple">
                                    <input type="radio" name="confidentiality" value="{{ $opt['value'] }}"
                                        class="mt-0.5 h-4 w-4 flex-shrink-0 text-purple-600 border-gray-300"
                                        onchange="updatePreview('CIA')">
                                    <div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold
                                {{ $opt['value'] == 3 ? 'bg-red-100 text-red-700' : ($opt['value'] == 2 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $opt['value'] == 1 ? 'Rendah' : ($opt['value'] == 2 ? 'Sedang' : 'Tinggi') }}
                                        </span>
                                        <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">
                                            {{ Str::after($opt['label'], ': ') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- I --}}
                        <div class="p-5 space-y-3">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-sm font-bold text-blue-700">I</span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Integrity</p>
                                    <p class="text-xs text-gray-400">Integritas</p>
                                </div>
                            </div>
                            @foreach ($ciaOptions['I'] as $opt)
                                <label
                                    class="cia-option flex items-start gap-3 rounded-xl border-2 border-gray-200 p-3 cursor-pointer transition-all hover:border-blue-300 hover:bg-gray-50"
                                    data-group="integrity" data-value="{{ $opt['value'] }}" data-color="blue">
                                    <input type="radio" name="integrity" value="{{ $opt['value'] }}"
                                        class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-600 border-gray-300"
                                        onchange="updatePreview('CIA')">
                                    <div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold
                                {{ $opt['value'] == 3 ? 'bg-red-100 text-red-700' : ($opt['value'] == 2 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $opt['value'] == 1 ? 'Rendah' : ($opt['value'] == 2 ? 'Sedang' : 'Tinggi') }}
                                        </span>
                                        <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">
                                            {{ Str::after($opt['label'], ': ') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- A --}}
                        <div class="p-5 space-y-3">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-green-100 text-sm font-bold text-green-700">A</span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Availability</p>
                                    <p class="text-xs text-gray-400">Ketersediaan</p>
                                </div>
                            </div>
                            @foreach ($ciaOptions['A'] as $opt)
                                <label
                                    class="cia-option flex items-start gap-3 rounded-xl border-2 border-gray-200 p-3 cursor-pointer transition-all hover:border-green-300 hover:bg-gray-50"
                                    data-group="availability" data-value="{{ $opt['value'] }}" data-color="green">
                                    <input type="radio" name="availability" value="{{ $opt['value'] }}"
                                        class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600 border-gray-300"
                                        onchange="updatePreview('CIA')">
                                    <div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold
                                {{ $opt['value'] == 3 ? 'bg-red-100 text-red-700' : ($opt['value'] == 2 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $opt['value'] == 1 ? 'Rendah' : ($opt['value'] == 2 ? 'Sedang' : 'Tinggi') }}
                                        </span>
                                        <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">
                                            {{ Str::after($opt['label'], ': ') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div
                        class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4">
                        <div>
                            <div id="previewWrap" class="hidden flex items-center gap-2">
                                <span
                                    class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kritikalitas:</span>
                                <span id="previewBadge"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold"></span>
                                <span class="text-xs text-gray-400">= max(C, I, A)</span>
                            </div>
                            <p id="previewHint" class="text-xs text-gray-400 italic">Pilih nilai C, I, dan A untuk preview
                                kritikalitas</p>
                        </div>
                        <div class="flex gap-3 flex-shrink-0">
                            <button type="button" onclick="closeCIAModal()"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button id="btnSimpan" type="submit" disabled
                                class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                                Simpan Penilaian
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        {{-- ══════════════════════════════════════════════════════════
     BULK CIA MODAL — penilaian massal
══════════════════════════════════════════════════════════ --}}
        <div id="modalBulkCIA" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
            onclick="if(event.target===this) closeBulkCIAModal()">

            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl flex flex-col" style="max-height:90vh;">

                {{-- Header --}}
                <div
                    class="flex-shrink-0 flex items-start justify-between rounded-t-2xl bg-gradient-to-r from-indigo-700 to-indigo-600 px-6 py-4">
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
                            Nilai CIA untuk <span id="bulkModalCount" class="text-indigo-200">0</span> Aset
                        </h2>
                        <p class="mt-1 text-xs text-indigo-200">Nilai CIA yang dipilih akan diterapkan ke semua aset yang
                            dicentang</p>
                    </div>
                    <button onclick="closeBulkCIAModal()"
                        class="ml-4 flex-shrink-0 rounded-lg p-1.5 text-indigo-200 hover:bg-indigo-800 hover:text-white transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Daftar aset terpilih (collapsed list) --}}
                <div class="flex-shrink-0 border-b border-gray-100 bg-gray-50 px-6 py-3">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Aset yang akan dinilai</p>
                        <button onclick="toggleBulkList()" id="btnToggleBulkList"
                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                            Lihat daftar ▾
                        </button>
                    </div>
                    <div id="bulkAssetList" class="hidden flex flex-wrap gap-1.5 max-h-32 overflow-y-auto">
                        {{-- Diisi via JS --}}
                    </div>
                </div>

                {{-- Form --}}
                <form id="formBulkCIA" method="POST" action="{{ route('admin.asset-criticality.bulk-update') }}"
                    class="flex flex-col flex-1 min-h-0">
                    @csrf

                    {{-- Hidden fields asset_ids akan diisi JS --}}
                    <div id="bulkHiddenIds"></div>

                    {{-- 3 Kolom CIA — sama persis dengan modal individual --}}
                    <div class="grid grid-cols-3 divide-x divide-gray-100 overflow-y-auto flex-1">

                        {{-- C --}}
                        <div class="p-5 space-y-3">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-purple-100 text-sm font-bold text-purple-700">C</span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Confidentiality</p>
                                    <p class="text-xs text-gray-400">Kerahasiaan</p>
                                </div>
                            </div>
                            @foreach ($ciaOptions['C'] as $opt)
                                <label
                                    class="bulk-cia-option flex items-start gap-3 rounded-xl border-2 border-gray-200 p-3 cursor-pointer transition-all hover:border-purple-300 hover:bg-gray-50"
                                    data-group="bulk_confidentiality" data-value="{{ $opt['value'] }}"
                                    data-color="purple">
                                    <input type="radio" name="confidentiality" value="{{ $opt['value'] }}"
                                        class="mt-0.5 h-4 w-4 flex-shrink-0 text-purple-600 border-gray-300"
                                        onchange="updatePreview('BULK')">
                                    <div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold
                                {{ $opt['value'] == 3 ? 'bg-red-100 text-red-700' : ($opt['value'] == 2 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $opt['value'] == 1 ? 'Rendah' : ($opt['value'] == 2 ? 'Sedang' : 'Tinggi') }}
                                        </span>
                                        <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">
                                            {{ Str::after($opt['label'], ': ') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- I --}}
                        <div class="p-5 space-y-3">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-sm font-bold text-blue-700">I</span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Integrity</p>
                                    <p class="text-xs text-gray-400">Integritas</p>
                                </div>
                            </div>
                            @foreach ($ciaOptions['I'] as $opt)
                                <label
                                    class="bulk-cia-option flex items-start gap-3 rounded-xl border-2 border-gray-200 p-3 cursor-pointer transition-all hover:border-blue-300 hover:bg-gray-50"
                                    data-group="bulk_integrity" data-value="{{ $opt['value'] }}" data-color="blue">
                                    <input type="radio" name="integrity" value="{{ $opt['value'] }}"
                                        class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-600 border-gray-300"
                                        onchange="updatePreview('BULK')">
                                    <div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold
                                {{ $opt['value'] == 3 ? 'bg-red-100 text-red-700' : ($opt['value'] == 2 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $opt['value'] == 1 ? 'Rendah' : ($opt['value'] == 2 ? 'Sedang' : 'Tinggi') }}
                                        </span>
                                        <p class="mt-1.5 text-xs text-gray-600 leading-relaxed">
                                            {{ Str::after($opt['label'], ': ') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- A --}}
                        <div class="p-5 space-y-3">
                            <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-green-100 text-sm font-bold text-green-700">A</span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Availability</p>
                                    <p class="text-xs text-gray-400">Ketersediaan</p>
                                </div>
                            </div>
                            @foreach ($ciaOptions['A'] as $opt)
                                <label
                                    class="bulk-cia-option flex items-start gap-3 rounded-xl border-2 border-gray-200 p-3 cursor-pointer transition-all hover:border-green-300 hover:bg-gray-50"
                                    data-group="bulk_availability" data-value="{{ $opt['value'] }}" data-color="green">
                                    <input type="radio" name="availability" value="{{ $opt['value'] }}"
                                        class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600 border-gray-300"
                                        onchange="updatePreview('BULK')">
                                    <div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold
                                {{ $opt['value'] == 3 ? 'bg-red-100 text-red-700' : ($opt['value'] == 2 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                            {{ $opt['value'] == 1 ? 'Rendah' : ($opt['value'] == 2 ? 'Sedang' : 'Tinggi') }}
                                        </span>
                                        <p class="mt-1.5 text-xs text-gray-600 leading-relaxed font-mono">
                                            {{ Str::after($opt['label'], ': ') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div
                        class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4">
                        <div>
                            <div id="bulkPreviewWrap" class="hidden flex items-center gap-2">
                                <span
                                    class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kritikalitas:</span>
                                <span id="bulkPreviewBadge"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold"></span>
                                <span class="text-xs text-gray-400">= max(C, I, A) · diterapkan ke semua aset
                                    terpilih</span>
                            </div>
                            <p id="bulkPreviewHint" class="text-xs text-gray-400 italic">Pilih nilai C, I, dan A untuk
                                preview
                                kritikalitas</p>
                        </div>
                        <div class="flex gap-3 flex-shrink-0">
                            <button type="button" onclick="closeBulkCIAModal()"
                                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button id="btnBulkSimpan" type="submit" disabled
                                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Simpan ke <span id="btnBulkCount" class="ml-0.5">0</span> Aset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @endsection

    @push('scripts')
        <script>
            // ─── Constants ────────────────────────────────────────────────
            const BASE_URL = '{{ url('admin/asset-criticality') }}';
            const LEVEL_LABELS = {
                1: 'Rendah',
                2: 'Sedang',
                3: 'Tinggi'
            };
            const LEVEL_CLASSES = {
                1: 'bg-green-100 text-green-700',
                2: 'bg-amber-100 text-amber-700',
                3: 'bg-red-100 text-red-700',
            };

            // ─── Checkbox & Bulk Bar ──────────────────────────────────────

            function getCheckedIds() {
                return [...document.querySelectorAll('.row-checkbox:checked')].map(cb => cb.value);
            }

            function onRowCheckChange() {
                const checked = document.querySelectorAll('.row-checkbox:checked').length;
                const total = document.querySelectorAll('.row-checkbox').length;
                const checkAll = document.getElementById('checkAll');

                // Update indeterminate / checked state pada "check all"
                checkAll.indeterminate = checked > 0 && checked < total;
                checkAll.checked = checked === total && total > 0;

                // Highlight baris
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.closest('tr').classList.toggle('bg-blue-50', cb.checked);
                });

                updateBulkBar(checked);
            }

            function toggleAllOnPage(checked) {
                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.checked = checked;
                    cb.closest('tr').classList.toggle('bg-blue-50', checked);
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

            // Reset checkbox saat filter di-submit
            document.getElementById('filterForm')?.addEventListener('submit', clearAllChecks);

            // ─── Individual CIA Modal ─────────────────────────────────────

            function openCIAModal(assetId, kode, nama, c, i, a) {
                document.getElementById('modalKode').textContent = kode;
                document.getElementById('modalNama').textContent = nama;
                document.getElementById('formCIA').action = BASE_URL + '/' + assetId;

                // Reset
                document.querySelectorAll('#modalCIA input[type=radio]').forEach(r => r.checked = false);
                document.querySelectorAll('#modalCIA .cia-option').forEach(lbl => {
                    resetOptionStyle(lbl);
                });

                if (c) setRadio('#modalCIA', 'confidentiality', c);
                if (i) setRadio('#modalCIA', 'integrity', i);
                if (a) setRadio('#modalCIA', 'availability', a);

                updatePreview('CIA');
                document.getElementById('modalCIA').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeCIAModal() {
                document.getElementById('modalCIA').classList.add('hidden');
                document.body.style.overflow = '';
            }

            // ─── Bulk CIA Modal ───────────────────────────────────────────

            function openBulkCIAModal() {
                const ids = getCheckedIds();
                if (ids.length === 0) return;

                // Update count labels
                document.getElementById('bulkModalCount').textContent = ids.length;
                document.getElementById('btnBulkCount').textContent = ids.length;

                // Populate asset chips di daftar
                const listEl = document.getElementById('bulkAssetList');
                const hiddenEl = document.getElementById('bulkHiddenIds');
                listEl.innerHTML = '';
                hiddenEl.innerHTML = '';

                ids.forEach(id => {
                    const row = document.querySelector(`.asset-row[data-id="${id}"]`);
                    const kode = row?.dataset.kode ?? id;
                    const nama = row?.dataset.nama ?? '';

                    // Chip
                    const chip = document.createElement('span');
                    chip.className =
                        'inline-flex items-center gap-1 rounded-lg bg-white border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-700 shadow-sm';
                    chip.innerHTML = `<span class="font-mono text-indigo-600 font-semibold">${kode}</span>`;
                    if (nama) chip.title = nama;
                    listEl.appendChild(chip);

                    // Hidden input
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'asset_ids[]';
                    input.value = id;
                    hiddenEl.appendChild(input);
                });

                // Reset form CIA
                document.querySelectorAll('#modalBulkCIA input[type=radio]').forEach(r => r.checked = false);
                document.querySelectorAll('#modalBulkCIA .bulk-cia-option').forEach(lbl => resetOptionStyle(lbl));
                document.getElementById('bulkPreviewWrap').classList.add('hidden');
                document.getElementById('bulkPreviewHint').classList.remove('hidden');
                document.getElementById('btnBulkSimpan').disabled = true;

                // Tutup bulk bar, buka modal
                document.getElementById('modalBulkCIA').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeBulkCIAModal() {
                document.getElementById('modalBulkCIA').classList.add('hidden');
                document.body.style.overflow = '';
            }

            function toggleBulkList() {
                const list = document.getElementById('bulkAssetList');
                const btn = document.getElementById('btnToggleBulkList');
                const open = list.classList.toggle('hidden');
                btn.textContent = open ? 'Lihat daftar ▾' : 'Sembunyikan ▴';
            }

            // ─── Shared CIA helpers ───────────────────────────────────────

            function setRadio(scope, name, value) {
                const radio = document.querySelector(`${scope} input[name="${name}"][value="${value}"]`);
                if (radio) {
                    radio.checked = true;
                    highlightLabel(radio.closest('.cia-option') ?? radio.closest('.bulk-cia-option'));
                }
            }

            function resetOptionStyle(lbl) {
                lbl.classList.remove(
                    'border-purple-500', 'bg-purple-50',
                    'border-blue-500', 'bg-blue-50',
                    'border-green-500', 'bg-green-50'
                );
                lbl.classList.add('border-gray-200');
            }

            function highlightLabel(label) {
                if (!label) return;
                const color = label.dataset.color;
                const group = label.dataset.group;

                // Reset semua sibling
                document.querySelectorAll(`[data-group="${group}"]`).forEach(l => resetOptionStyle(l));

                // Highlight label yang dipilih
                label.classList.remove('border-gray-200');
                label.classList.add(`border-${color}-500`, `bg-${color}-50`);
            }

            // Attach highlight event untuk individual modal
            document.querySelectorAll('#modalCIA .cia-option input[type=radio]').forEach(radio => {
                radio.addEventListener('change', function() {
                    highlightLabel(this.closest('.cia-option'));
                });
            });

            // Attach highlight event untuk bulk modal
            document.querySelectorAll('#modalBulkCIA .bulk-cia-option input[type=radio]').forEach(radio => {
                radio.addEventListener('change', function() {
                    highlightLabel(this.closest('.bulk-cia-option'));
                });
            });

            function getModalVal(modalId, name) {
                const el = document.querySelector(`${modalId} input[name="${name}"]:checked`);
                return el ? parseInt(el.value) : null;
            }

            /**
             * @param {'CIA'|'BULK'} mode
             */
            function updatePreview(mode) {
                const isBulk = mode === 'BULK';
                const scope = isBulk ? '#modalBulkCIA' : '#modalCIA';

                const c = getModalVal(scope, 'confidentiality');
                const i = getModalVal(scope, 'integrity');
                const a = getModalVal(scope, 'availability');

                const wrap = document.getElementById(isBulk ? 'bulkPreviewWrap' : 'previewWrap');
                const hint = document.getElementById(isBulk ? 'bulkPreviewHint' : 'previewHint');
                const badge = document.getElementById(isBulk ? 'bulkPreviewBadge' : 'previewBadge');
                const btn = document.getElementById(isBulk ? 'btnBulkSimpan' : 'btnSimpan');

                if (c && i && a) {
                    const max = Math.max(c, i, a);
                    badge.textContent = LEVEL_LABELS[max];
                    badge.className = 'inline-flex items-center rounded-full px-3 py-1 text-sm font-bold ' + LEVEL_CLASSES[max];
                    wrap.classList.remove('hidden');
                    hint.classList.add('hidden');
                    btn.disabled = false;
                } else {
                    wrap.classList.add('hidden');
                    hint.classList.remove('hidden');
                    btn.disabled = true;
                }
            }

            // ─── Keyboard & close ─────────────────────────────────────────
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeCIAModal();
                    closeBulkCIAModal();
                }
            });
        </script>
    @endpush
