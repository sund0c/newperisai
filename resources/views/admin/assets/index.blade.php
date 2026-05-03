@extends('layouts.admin')

@section('title', 'Daftar Aset')
@section('page-title', 'Daftar Aset')
@section('page-subtitle', 'Kelola data aset Pemerintah Provinsi Bali')

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

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / nama aset..."
                    class="w-52 px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">

                <select name="opd_id"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua OPD</option>
                    @foreach ($opds as $opd)
                        <option value="{{ $opd->id }}" {{ request('opd_id') == $opd->id ? 'selected' : '' }}>
                            {{ $opd->namaopd }}
                        </option>
                    @endforeach
                </select>

                <select name="klasifikasi"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Klasifikasi</option>
                    @foreach (['PL', 'PK', 'DI', 'SDM', 'SP'] as $k)
                        <option value="{{ $k }}" {{ request('klasifikasi') == $k ? 'selected' : '' }}>
                            {{ $k }}</option>
                    @endforeach
                </select>

                <select name="status"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Dihapus</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'opd_id', 'klasifikasi', 'status']))
                    <a href="{{ route('admin.assets.index') }}"
                        class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm transition-colors">Reset</a>
                @endif
            </form>

            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Aset
            </button>
        </div>

        {{-- Tabel --}}
        @if ($assets->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada data aset.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[700px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Aset</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Klasifikasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">OPD
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                                Status</th>
                            <th class="px-4 py-3 w-36"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($assets as $asset)
                            @php $isDeleted = $asset->trashed(); @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isDeleted ? 'opacity-50' : '' }}">

                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                        {{ $asset->kode_aset }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $asset->nama_aset }}
                                </td>

                                <td class="px-4 py-3">
                                    @php
                                        $k = $asset->subKlasifikasi->klasifikasi;
                                        $badge = match ($k) {
                                            'PL' => 'bg-blue-100 text-blue-700',
                                            'PK' => 'bg-purple-100 text-purple-700',
                                            'DI' => 'bg-cyan-100 text-cyan-700',
                                            'SDM' => 'bg-amber-100 text-amber-700',
                                            'SP' => 'bg-green-100 text-green-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $badge }}">
                                        {{ $k }}
                                    </span>
                                    <span class="text-xs text-gray-500 ml-1">{{ $asset->subKlasifikasi->nama }}</span>
                                </td>

                                <td class="px-4 py-3 text-xs text-gray-600">{{ $asset->opd->namaopd }}</td>

                                <td class="px-4 py-3 text-center">
                                    @if ($isDeleted)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Dihapus</span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($isDeleted)
                                            <form action="{{ route('admin.assets.restore', $asset->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-green-50 text-green-600 hover:bg-green-100
                                                           border border-green-200 transition-colors">
                                                    Pulihkan
                                                </button>
                                            </form>
                                        @else
                                            <button type="button"
                                                onclick="openEdit(
                                                    '{{ $asset->id }}',
                                                    '{{ addslashes($asset->nama_aset) }}',
                                                    '{{ $asset->kode_aset }}',
                                                    '{{ $asset->opd_id }}',
                                                    '{{ $asset->sub_klasifikasi_id }}'
                                                )"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-blue-50 text-blue-600 hover:bg-blue-100
                                                       border border-blue-200 transition-colors">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST"
                                                onsubmit="return confirm('Hapus aset {{ addslashes($asset->nama_aset) }}?')">
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
            </div>

            @if ($assets->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $assets->links() }}
                </div>
            @endif
        @endif
    </div>


    {{-- ════ MODAL TAMBAH ════ --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Aset</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Pemerintah Provinsi Bali</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.assets.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                @include('admin.assets._fields')
                <div class="flex items-center justify-end gap-3 pt-2">
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

    {{-- ════ MODAL EDIT ════ --}}
    <div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Edit Aset</h3>
                    <p id="editSubtitle" class="text-xs text-gray-500 mt-0.5"></p>
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
                @csrf @method('PATCH')
                @include('admin.assets._fields', ['isEdit' => true])
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEdit(id, nama, kode, opdId, subKlasId) {
            document.getElementById('formEdit').action = `/admin/assets/${id}`;
            document.getElementById('editSubtitle').textContent = kode + ' — ' + nama;
            document.getElementById('edit_opd_id').value = opdId;
            document.getElementById('edit_sub_klasifikasi_id').value = subKlasId;
            document.getElementById('edit_kode_aset').value = kode;
            document.getElementById('edit_nama_aset').value = nama;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>

    @if ($errors->any())
        <script>
            document.getElementById('modalTambah').classList.remove('hidden');
        </script>
    @endif

@endsection
