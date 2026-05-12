{{-- resources/views/admin/assets/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Aset')
@section('page-title', 'Manajemen Aset')
@section('page-subtitle', 'Kelola daftar aset informasi per OPD')

@section('content')

    {{-- Flash Messages --}}
    @if (session('success'))
        <div
            class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif


    <div class="mb-4">
        <div class="flex gap-3">

            {{-- Total Aktif --}}
            <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Aktif</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalAktif) }}</p>
                <p class="text-xs text-gray-400 mt-1">Tahun {{ $tahunContext?->tahun ?? '-' }}</p>
            </div>

            {{-- Per Klasifikasi --}}
            @php
                $colorMap = [
                    'PL' => ['border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                    'PK' => ['border' => 'border-violet-200', 'dot' => 'bg-violet-500'],
                    'SP' => ['border' => 'border-amber-200', 'dot' => 'bg-amber-500'],
                    'SK' => ['border' => 'border-green-200', 'dot' => 'bg-green-500'],
                    'DI' => ['border' => 'border-rose-200', 'dot' => 'bg-rose-500'],
                ];
            @endphp

            @foreach ($statsKlas as $klas)
                @php
                    $kode = strtoupper($klas->kodeklas ?? 'XX');
                    $colors = $colorMap[$kode] ?? ['border' => 'border-gray-200', 'dot' => 'bg-gray-400'];
                    $pct = $totalAktif > 0 ? round(($klas->total_aset / $totalAktif) * 100) : 0;
                @endphp
                <div class="flex-1 min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-4">
                    <div class="flex items-center justify-end mb-2">
                        <span class="text-xs text-gray-400">{{ $pct }}%</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($klas->total_aset) }}</p>
                    <p class="text-xs text-gray-400 mt-1 truncate">{{ $klas->klasifikasiaset }}</p>
                </div>
            @endforeach

        </div>
    </div>

    {{-- Filter & Toolbar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <form method="GET" action="{{ route('admin.assets.index') }}">
            <div class="px-6 py-4 flex items-center gap-3">

                {{-- Search --}}
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau kode aset..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                {{-- Filter OPD --}}
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

                {{-- Filter Klasifikasi --}}
                <div class="flex-1">
                    <select name="klasifikasi"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Klasifikasi</option>
                        @foreach ($subKlasifikasis->keys() as $namaKlas)
                            <option value="{{ $namaKlas }}"
                                {{ request('klasifikasi') === $namaKlas ? 'selected' : '' }}>
                                {{ $namaKlas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div class="w-36 shrink-0">
                    <select name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Dihapus</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                        Terapkan
                    </button>
                    @if (request()->hasAny(['search', 'opd_id', 'klasifikasi', 'status']))
                        <a href="{{ route('admin.assets.index') }}"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                            Reset
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>


    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header tabel: judul kiri, tombol kanan --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold text-gray-700">Daftar Aset</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    Menampilkan <span class="font-medium text-gray-600">{{ $assets->total() }}</span> aset
                    dari total <span class="font-medium text-gray-600">{{ $totalAset }}</span> aset
                </p>
            </div>

            {{-- Action buttons --}}
            <div class="flex items-center gap-2">

                {{-- ── Tombol Export PDF ── --}}
                <button type="button" onclick="document.getElementById('modalExportPdf').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-red-200
                           bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 13h4M10 17h4M10 9h1" />
                    </svg>
                    Export PDF
                </button>

                {{-- ── Tombol Tambah Aset ── --}}
                <button type="button" onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Aset
                </button>

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
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50">

                        <tr>
<th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10">#</th>
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
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
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-44">
                                OPD</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-44">
                                Klas / Sub Klas</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                                Status</th>
                            <th class="px-6 py-3 w-36"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($assets as $asset)
                            @php $isDeleted = $asset->trashed(); @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isDeleted ? 'opacity-50' : '' }}">

                                <td class="px-3 py-3 text-xs text-gray-400">
                                    {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}
                                </td>

                                <td class="px-3 py-3 whitespace-nowrap">
                                    <a href="{{ route('admin.assets.detail', $asset) }}"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
           bg-indigo-50 text-indigo-600 hover:bg-indigo-100
           border border-indigo-200 transition-colors">


                                        <span
                                            class="font-mono text-xs font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                            {{ $asset->kode_aset }}
                                        </span>
                                    </a>
                                </td>

                                <td class="px-6 py-3 max-w-[200px]">
                                    <div class="text-xs font-medium text-gray-700">{{ $asset->nama_aset ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $asset->keterangan ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-3 text-xs text-gray-600 max-w-[140px]">
                                    <span class="line-clamp-2">{{ $asset->opd->namaopd ?? '-' }}</span>
                                </td>

                                <td class="px-6 py-3">
                                    <div class="text-xs font-medium text-gray-700">
                                        {{ $asset->subKlasifikasi->klasifikasi->klasifikasiaset ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $asset->subKlasifikasi->subklasifikasiaset ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-6 py-3">
                                    @if ($isDeleted)
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Dihapus</span>
                                    @else
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                    @endif
                                </td>

                                <td class="px-6 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($isDeleted)
                                            <form action="{{ route('admin.assets.restore', $asset->id) }}" method="POST"
                                                onsubmit="return confirm('Pulihkan aset {{ addslashes($asset->kode_aset) }}?')">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-green-50 text-green-600 hover:bg-green-100
                                                           border border-green-200 transition-colors">
                                                    Pulihkan
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" data-id="{{ $asset->id }}"
                                                data-opd="{{ $asset->opd_id }}"
                                                data-subklas="{{ $asset->sub_klasifikasi_id }}"
                                                data-kode="{{ $asset->kode_aset }}" data-nama="{{ $asset->nama_aset }}"
                                                data-keterangan="{{ $asset->keterangan }}" onclick="openEdit(this)"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-blue-50 text-blue-600 hover:bg-blue-100
                                                       border border-blue-200 transition-colors">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST"
                                                onsubmit="return confirm('Arsipkan aset {{ addslashes($asset->kode_aset) }}? Data tidak akan hilang permanen.')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-red-50 text-red-600 hover:bg-red-100
                                                           border border-red-200 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if ($assets->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                        <p class="text-xs text-gray-400">
                            Menampilkan {{ $assets->firstItem() }}–{{ $assets->lastItem() }}
                            dari {{ $assets->total() }} aset
                        </p>
                        {{ $assets->links() }}
                    </div>
                @else
                    <div class="px-6 py-3 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Total: {{ $assets->total() }} aset</p>
                    </div>
                @endif
            @endif
        </div>
    </div>


    {{-- ============================================================
         MODAL TAMBAH
    ============================================================ --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-lg">

            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Aset</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Isi detail aset baru</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.assets.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        OPD <span class="text-red-500">*</span>
                    </label>
                    <select name="opd_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih OPD --</option>
                        @foreach ($opds as $opd)
                            <option value="{{ $opd->id }}" {{ old('opd_id') === $opd->id ? 'selected' : '' }}>
                                {{ $opd->namaopd }}
                            </option>
                        @endforeach
                    </select>
                    @error('opd_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Sub Klasifikasi <span class="text-red-500">*</span>
                    </label>
                    <select name="sub_klasifikasi_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Sub Klasifikasi --</option>
                        @foreach ($subKlasifikasis as $klasifikasi => $items)
                            <optgroup label="{{ $klasifikasi }}">
                                @foreach ($items as $sub)
                                    <option value="{{ $sub->id }}"
                                        {{ old('sub_klasifikasi_id') === $sub->id ? 'selected' : '' }}>
                                        {{ $sub->subklasifikasiaset }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('sub_klasifikasi_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Aset <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_aset" value="{{ old('nama_aset') }}"
                        placeholder="cth: Server Aplikasi SIPD" maxlength="200"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                    @error('nama_aset')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                        placeholder="Deskripsi singkat aset, fungsi, lokasi, atau informasi tambahan..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ============================================================
         MODAL EDIT
    ============================================================ --}}
    <div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-lg">

            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Edit Aset</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui detail aset</p>
                </div>
                <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="formEdit" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PUT')

                <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                    <span class="text-xs text-gray-400">Kode Aset</span>
                    <span id="editKodeAsetDisplay"
                        class="font-mono text-xs font-semibold text-gray-700 bg-white border border-gray-200 px-2 py-0.5 rounded">
                    </span>
                    <span class="ml-auto text-xs text-gray-400 italic">Tidak dapat diubah</span>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        OPD <span class="text-red-500">*</span>
                    </label>
                    <select id="editOpdId" name="opd_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih OPD --</option>
                        @foreach ($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->namaopd }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Sub Klasifikasi <span class="text-red-500">*</span>
                    </label>
                    <select id="editSubKlasifikasiId" name="sub_klasifikasi_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Sub Klasifikasi --</option>
                        @foreach ($subKlasifikasis as $klasifikasi => $items)
                            <optgroup label="{{ $klasifikasi }}">
                                @foreach ($items as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->subklasifikasiaset }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Aset <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editNamaAset" name="nama_aset" maxlength="200"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                    <textarea id="editKeterangan" name="keterangan" rows="3"
                        placeholder="Deskripsi singkat aset, fungsi, lokasi, atau informasi tambahan..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                    @error('keterangan')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ============================================================
         MODAL EXPORT PDF
    ============================================================ --}}
    <div id="modalExportPdf" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">

            {{-- Header --}}
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">Export Laporan PDF</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Pilih filter sebelum generate</p>
                    </div>
                </div>
                <button onclick="document.getElementById('modalExportPdf').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form method="GET" action="{{ route('admin.assets.export-pdf') }}" target="_blank"
                class="px-6 py-5 space-y-4">

                {{-- 1. Tahun --}}
                <div>
                    <label for="ep_tahun" class="block text-xs font-medium text-gray-600 mb-1">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <select id="ep_tahun" name="tahun" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-red-400">
                        <option value="" disabled selected>— Pilih Tahun —</option>
                        @foreach ($allTahun as $tahun)
                            <option value="{{ $tahun->id }}"
                                {{ isset($tahunContext) && $tahunContext->id === $tahun->id ? 'selected' : '' }}>
                                {{ $tahun->tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. OPD --}}
                <div>
                    <label for="ep_opd" class="block text-xs font-medium text-gray-600 mb-1">OPD</label>
                    <select id="ep_opd" name="opd_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-red-400">
                        <option value="">Semua OPD</option>
                        @foreach ($opds as $opd)
                            <option value="{{ $opd->id }}">{{ $opd->namaopd }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Klasifikasi --}}
                <div>
                    <label for="ep_klas" class="block text-xs font-medium text-gray-600 mb-1">Klasifikasi</label>
                    <select id="ep_klas" name="klasifikasi_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-red-400">
                        <option value="">Semua Klasifikasi</option>
                        @foreach ($klasifikasis as $klas)
                            <option value="{{ $klas->id }}">{{ $klas->klasifikasiaset }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Status --}}
                <div>
                    <label for="ep_status" class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select id="ep_status" name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-red-400">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="hapus">Dihapus</option>
                    </select>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalExportPdf').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-red-600 hover:bg-red-700
                               text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Generate PDF
                    </button>
                </div>
            </form>

        </div>
    </div>


    {{-- Buka modal tambah otomatis jika ada validation error --}}
    @if ($errors->any())
        <script>
            document.getElementById('modalTambah').classList.remove('hidden');
        </script>
    @endif

    <script>
        function openEdit(btn) {
            const id = btn.dataset.id;
            const opdId = btn.dataset.opd;
            const subKlas = btn.dataset.subklas;
            const kode = btn.dataset.kode;
            const nama = btn.dataset.nama;
            const keterangan = btn.dataset.keterangan;

            document.getElementById('formEdit').action =
                '{{ route('admin.assets.update', '__ID__') }}'.replace('__ID__', id);

            document.getElementById('editKodeAsetDisplay').textContent = kode;
            document.getElementById('editOpdId').value = opdId;
            document.getElementById('editSubKlasifikasiId').value = subKlas;
            document.getElementById('editNamaAset').value = nama;
            document.getElementById('editKeterangan').value = keterangan;

            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>

@endsection
