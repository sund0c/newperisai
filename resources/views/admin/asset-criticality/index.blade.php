{{-- resources/views/admin/asset-criticality/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Kritikalitas Aset')

@section('content')

    {{-- ══════════════════════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════════════════════ --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kritikalitas Aset</h1>
            <p class="mt-1 text-sm text-gray-500">
                Penilaian Confidentiality, Integrity &amp; Availability (CIA) per aset
                <span class="font-medium text-gray-700">— Tahun {{ $tahunContext?->tahun ?? '-' }}</span>
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
     STAT CARDS
══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Aset</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalAset) }}</p>
            <p class="mt-1 text-xs text-gray-400">pada tahun {{ $tahunContext?->tahun ?? '-' }}</p>
        </div>

        <div class="rounded-xl border border-blue-100 bg-blue-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Sudah Dinilai</p>
            <p class="mt-2 text-3xl font-bold text-blue-700">{{ number_format($totalDinilai) }}</p>
            <p class="mt-1 text-xs text-blue-400">
                @if ($totalAset > 0)
                    {{ round(($totalDinilai / $totalAset) * 100) }}% dari total
                @else
                    -
                @endif
            </p>
        </div>

        <div class="rounded-xl border border-amber-100 bg-amber-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Belum Dinilai</p>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ number_format($totalBelumNilai) }}</p>
            <p class="mt-1 text-xs text-amber-400">perlu segera dinilai</p>
        </div>

        <div class="rounded-xl border border-red-100 bg-red-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Tinggi</p>
            <p class="mt-2 text-3xl font-bold text-red-700">{{ number_format($totalTinggi) }}</p>
            <p class="mt-1 text-xs text-red-400">aset kritikalitas tinggi</p>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════
     FILTER BAR
══════════════════════════════════════════════════════════ --}}
    <form method="GET" action="{{ route('admin.asset-criticality.index') }}"
        class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm mb-6">

        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Cari Aset</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode atau nama aset..."
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>

        <div class="min-w-[180px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">OPD</label>
            <select name="opd_id"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">Semua OPD</option>
                @foreach ($opds as $opd)
                    <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                        {{ $opd->nama_opd }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Klasifikasi</label>
            <select name="klasifikasi"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">Semua</option>
                @foreach (['PL' => 'Perangkat Lunak', 'PK' => 'Perangkat Keras', 'DI' => 'Data & Informasi', 'SDM' => 'Sumber Daya Manusia', 'SP' => 'Layanan/Proses'] as $kode => $nama)
                    <option value="{{ $kode }}" {{ request('klasifikasi') === $kode ? 'selected' : '' }}>
                        {{ $kode }} – {{ $nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Kritikalitas</label>
            <select name="kritikalitas"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">Semua</option>
                <option value="3" {{ request('kritikalitas') == '3' ? 'selected' : '' }}>Tinggi</option>
                <option value="2" {{ request('kritikalitas') == '2' ? 'selected' : '' }}>Sedang</option>
                <option value="1" {{ request('kritikalitas') == '1' ? 'selected' : '' }}>Rendah</option>
            </select>
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-gray-600 mb-1">Status Penilaian</label>
            <select name="status"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">Semua</option>
                <option value="unassessed" {{ request('status') === 'unassessed' ? 'selected' : '' }}>Belum Dinilai
                </option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.asset-criticality.index') }}"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Reset
            </a>
        </div>
    </form>

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

    {{-- ══════════════════════════════════════════════════════════
     TABLE
══════════════════════════════════════════════════════════ --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-12 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">#
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'kode_aset', 'direction' => $sortBy === 'kode_aset' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                            class="inline-flex items-center gap-1 hover:text-gray-700">
                            Kode Aset @if ($sortBy === 'kode_aset')
                                <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'nama_aset', 'direction' => $sortBy === 'nama_aset' && $direction === 'asc' ? 'desc' : 'asc']) }}"
                            class="inline-flex items-center gap-1 hover:text-gray-700">
                            Nama Aset @if ($sortBy === 'nama_aset')
                                <span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">OPD</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Klas / Sub
                        Klas</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">C</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">I</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">A</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Kritikal
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($assets as $i => $asset)
                    @php
                        $c = $asset->criticality;
                        $no = $assets->firstItem() + $i;
                        $map = [
                            1 => ['label' => 'R', 'class' => 'bg-green-100 text-green-700'],
                            2 => ['label' => 'S', 'class' => 'bg-amber-100 text-amber-700'],
                            3 => ['label' => 'T', 'class' => 'bg-red-100 text-red-700'],
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                        onclick="openCIAModal('{{ $asset->id }}', '{{ addslashes($asset->kode_aset) }}', '{{ addslashes($asset->nama_aset) }}', {{ $c?->confidentiality ?? 'null' }}, {{ $c?->integrity ?? 'null' }}, {{ $c?->availability ?? 'null' }})">
                        <td class="px-4 py-3 text-center">
                            <span
                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-blue-600 text-xs font-bold text-white shadow">
                                {{ $no }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-700">{{ $asset->kode_aset }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $asset->nama_aset }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            <span class="max-w-[160px] block truncate" title="{{ $asset->opd?->nama_opd }}">
                                {{ $asset->opd?->nama_opd ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            @if ($asset->subKlasifikasi)
                                <span
                                    class="inline-block rounded bg-gray-100 px-1.5 py-0.5 text-xs font-mono font-semibold text-gray-700">
                                    {{ $asset->subKlasifikasi->klasifikasi?->klasifikasiaset ?? '?' }}
                                </span>
                                <span
                                    class="ml-1 text-xs text-gray-500">{{ $asset->subKlasifikasi->subklasifikasiaset }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
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
                        <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-400">
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


    {{-- ══════════════════════════════════════════════════════════
     CIA MODAL — vanilla JS, no Alpine
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
                    <p class="mt-1 text-xs text-blue-100">Penilaian CIA — Confidentiality · Integrity · Availability</p>
                </div>
                <button onclick="closeCIAModal()"
                    class="ml-4 flex-shrink-0 rounded-lg p-1.5 text-blue-200 hover:bg-blue-800 hover:text-white transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                                    onchange="updatePreview()">
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
                                    onchange="updatePreview()">
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
                                    onchange="updatePreview()">
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

                </div>{{-- end grid --}}

                {{-- Footer --}}
                <div
                    class="flex-shrink-0 border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4">

                    {{-- Preview --}}
                    <div>
                        <div id="previewWrap" class="hidden flex items-center gap-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kritikalitas:</span>
                            <span id="previewBadge"
                                class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold"></span>
                            <span class="text-xs text-gray-400">= max(C, I, A)</span>
                        </div>
                        <p id="previewHint" class="text-xs text-gray-400 italic">Pilih nilai C, I, dan A untuk preview
                            kritikalitas</p>
                    </div>

                    {{-- Buttons --}}
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

@endsection

@push('scripts')
    <script>
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
        const BORDER_SELECTED = {
            purple: 'border-purple-500 bg-purple-50',
            blue: 'border-blue-500 bg-blue-50',
            green: 'border-green-500 bg-green-50',
        };

        function openCIAModal(assetId, kode, nama, c, i, a) {
            // Set header
            document.getElementById('modalKode').textContent = kode;
            document.getElementById('modalNama').textContent = nama;

            // Set form action
            document.getElementById('formCIA').action = BASE_URL + '/' + assetId;

            // Reset semua radio & border
            document.querySelectorAll('#modalCIA input[type=radio]').forEach(r => r.checked = false);
            document.querySelectorAll('#modalCIA .cia-option').forEach(lbl => {
                const color = lbl.dataset.color;
                lbl.className = lbl.className
                    .replace(/border-(purple|blue|green)-500/g, 'border-gray-200')
                    .replace(/bg-(purple|blue|green)-50/g, '');
                lbl.classList.add('border-gray-200');
            });

            // Set nilai existing jika ada
            if (c) setRadio('confidentiality', c);
            if (i) setRadio('integrity', i);
            if (a) setRadio('availability', a);

            updatePreview();

            // Buka modal
            document.getElementById('modalCIA').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function setRadio(name, value) {
            const radio = document.querySelector(`#modalCIA input[name="${name}"][value="${value}"]`);
            if (radio) {
                radio.checked = true;
                const label = radio.closest('.cia-option');
                if (label) highlightLabel(label);
            }
        }

        function highlightLabel(label) {
            const color = label.dataset.color;
            const group = label.dataset.group;
            // Reset siblings
            document.querySelectorAll(`.cia-option[data-group="${group}"]`).forEach(l => {
                l.classList.remove(
                    'border-purple-500', 'bg-purple-50',
                    'border-blue-500', 'bg-blue-50',
                    'border-green-500', 'bg-green-50'
                );
                l.classList.add('border-gray-200');
            });
            // Highlight selected
            label.classList.remove('border-gray-200');
            label.classList.add(`border-${color}-500`, `bg-${color}-50`);
        }

        // Highlight on click
        document.querySelectorAll('.cia-option input[type=radio]').forEach(radio => {
            radio.addEventListener('change', function() {
                highlightLabel(this.closest('.cia-option'));
            });
        });

        function updatePreview() {
            const c = getVal('confidentiality');
            const i = getVal('integrity');
            const a = getVal('availability');

            const previewWrap = document.getElementById('previewWrap');
            const previewHint = document.getElementById('previewHint');
            const previewBadge = document.getElementById('previewBadge');
            const btnSimpan = document.getElementById('btnSimpan');

            if (c && i && a) {
                const max = Math.max(c, i, a);
                previewBadge.textContent = LEVEL_LABELS[max];
                previewBadge.className = 'inline-flex items-center rounded-full px-3 py-1 text-sm font-bold ' +
                    LEVEL_CLASSES[max];
                previewWrap.classList.remove('hidden');
                previewHint.classList.add('hidden');
                btnSimpan.disabled = false;
            } else {
                previewWrap.classList.add('hidden');
                previewHint.classList.remove('hidden');
                btnSimpan.disabled = true;
            }
        }

        function getVal(name) {
            const el = document.querySelector(`#modalCIA input[name="${name}"]:checked`);
            return el ? parseInt(el.value) : null;
        }

        function closeCIAModal() {
            document.getElementById('modalCIA').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Tutup dengan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCIAModal();
        });
    </script>
@endpush
