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

    {{-- Filter & Toolbar --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-4">
        <form method="GET" action="{{ route('admin.assets.index') }}" class="px-6 py-4 flex flex-wrap items-end gap-3">

            {{-- Search --}}
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama atau kode aset..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm
                   focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>

            {{-- Filter OPD --}}
            <div class="min-w-[180px]">
                <select name="opd_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
               focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua OPD</option>
                    @foreach ($opds as $opd)
                        <option value="{{ $opd->id }}" {{ request('opd_id') === $opd->id ? 'selected' : '' }}>
                            {{ $opd->namaopd }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Klasifikasi --}}
            <div class="min-w-[180px]">
                <select name="klasifikasi"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
               focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Klasifikasi</option>
                    @foreach ($subKlasifikasis->keys() as $namaKlas)
                        <option value="{{ $namaKlas }}" {{ request('klasifikasi') === $namaKlas ? 'selected' : '' }}>
                            {{ $namaKlas }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="min-w-[140px]">
                <select name="status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
               focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Dihapus</option>
                </select>
            </div>

            {{-- Buttons --}}
            <div class="flex items-end gap-2">
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

            {{-- Spacer --}}
            <div class="flex-1"></div>

            {{-- Tambah Aset --}}
            <button type="button" onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Aset
            </button>

        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        @if ($assets->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z
                                       M16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2z" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada aset ditemukan.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">#
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">OPD
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Klasifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Sub
                            Klasifikasi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 w-44"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($assets as $asset)
                        <tr
                            class="hover:bg-gray-50 transition-colors {{ $asset->trashed() ? 'opacity-60 bg-red-50/30' : '' }}">

                            {{-- No --}}
                            <td class="px-6 py-3 text-xs text-gray-400">
                                {{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}
                            </td>

                            {{-- Kode --}}
                            <td class="px-6 py-3">
                                <span
                                    class="font-mono text-xs font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                    {{ $asset->kode_aset }}
                                </span>
                            </td>

                            {{-- Nama --}}
                            <td class="px-6 py-3 font-medium text-gray-900 max-w-xs">
                                <span class="line-clamp-2">{{ $asset->nama_aset }}</span>
                            </td>

                            {{-- OPD --}}
                            <td class="px-6 py-3 text-xs text-gray-600 max-w-[160px]">
                                <span class="line-clamp-2">{{ $asset->opd->namaopd ?? '-' }}</span>
                            </td>

                            {{-- Klasifikasi --}}
                            <td class="px-6 py-3 text-xs text-gray-600">
                                {{ $asset->subKlasifikasi->klasifikasi ?? '-' }}
                            </td>

                            {{-- Sub Klasifikasi --}}
                            <td class="px-6 py-3 text-xs text-gray-600">
                                {{ $asset->subKlasifikasi->nama ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-3">
                                @if ($asset->trashed())
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                 text-xs font-semibold bg-red-100 text-red-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Dihapus
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                 text-xs font-semibold bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">

                                    @if ($asset->trashed())
                                        {{-- Restore --}}
                                        <form action="{{ route('admin.assets.restore', $asset->id) }}" method="POST"
                                            onsubmit="return confirm('Pulihkan aset {{ addslashes($asset->nama_aset) }}?')">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-emerald-50 text-emerald-600 hover:bg-emerald-100
                                                       border border-emerald-200 transition-colors">
                                                Pulihkan
                                            </button>
                                        </form>
                                    @else
                                        {{-- Edit --}}
                                        <button type="button"
                                            onclick="openEdit(
                                                '{{ $asset->id }}',
                                                '{{ addslashes($asset->opd_id) }}',
                                                '{{ addslashes($asset->sub_klasifikasi_id) }}',
                                                '{{ addslashes($asset->kode_aset) }}',
                                                '{{ addslashes($asset->nama_aset) }}'
                                            )"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                   bg-blue-50 text-blue-600 hover:bg-blue-100
                                                   border border-blue-200 transition-colors">
                                            Edit
                                        </button>

                                        {{-- Hapus --}}
                                        <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST"
                                            onsubmit="return confirm('Hapus aset {{ addslashes($asset->nama_aset) }}? Data ini akan diarsipkan.')">
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

                {{-- OPD --}}
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

                {{-- Sub Klasifikasi --}}
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
                                        {{ $sub->nama }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('sub_klasifikasi_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kode & Nama --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Kode Aset <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="kode_aset" value="{{ old('kode_aset') }}" placeholder="cth: PL-001"
                            maxlength="30"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                            oninput="this.value=this.value.toUpperCase()" required />
                        @error('kode_aset')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-1">
                        {{-- placeholder supaya grid 2 col terasa seimbang --}}
                    </div>
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

                {{-- OPD --}}
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

                {{-- Sub Klasifikasi --}}
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
                                    <option value="{{ $sub->id }}">{{ $sub->nama }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                {{-- Kode Aset --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Kode Aset <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editKodeAset" name="kode_aset" maxlength="30"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                            oninput="this.value=this.value.toUpperCase()" required />
                    </div>
                </div>

                {{-- Nama Aset --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Aset <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editNamaAset" name="nama_aset" maxlength="200"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
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

    {{-- Buka modal tambah otomatis jika ada validation error dari store --}}
    @if ($errors->any())
        <script>
            document.getElementById('modalTambah').classList.remove('hidden');
        </script>
    @endif

    <script>
        function openEdit(id, opdId, subKlasId, kode, nama) {
            document.getElementById('formEdit').action =
                '{{ route('admin.assets.update', '__ID__') }}'.replace('__ID__', id);

            document.getElementById('editOpdId').value = opdId;
            document.getElementById('editSubKlasifikasiId').value = subKlasId;
            document.getElementById('editKodeAset').value = kode;
            document.getElementById('editNamaAset').value = nama;

            document.getElementById('modalEdit').classList.remove('hidden');
        }

        // Tutup modal dengan Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('modalTambah').classList.add('hidden');
                document.getElementById('modalEdit').classList.add('hidden');
            }
        });
    </script>

@endsection
