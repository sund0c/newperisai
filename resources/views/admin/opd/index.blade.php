{{-- resources/views/admin/opd/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Perangkat Daerah')
@section('page-title', 'Perangkat Daerah')
@section('page-subtitle', 'Kelola daftar Perangkat Daerah Pemerintah Provinsi Bali')

@section('content')

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Total OPD</p>
            <p class="text-2xl font-bold text-blue-600">{{ $opds->whereNull('deleted_at')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Dihapus</p>
            <p class="text-2xl font-bold text-red-500">{{ $opds->whereNotNull('deleted_at')->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                {{-- Pertahankan sort state saat filter --}}
                @if (request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari perangkat daerah..."
                    class="w-56 px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500">

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
                @if (request('search'))
                    <a href="{{ route('admin.opd.index', array_filter(['sort' => request('sort'), 'direction' => request('direction')])) }}"
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 text-sm transition-colors">
                        Reset
                    </a>
                @endif
                @if (request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
            </form>

            <button onclick="document.getElementById('modalTambahOpd').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Perangkat Daerah
            </button>
        </div>

        {{-- Tabel --}}
        @php
            // Helper untuk generate URL sort
            function sortUrl(string $column, string $currentSort, string $currentDir): string
            {
                $dir = $currentSort === $column && $currentDir === 'asc' ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
            }
        @endphp

        @if ($opds->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada data Perangkat Daerah.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[500px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">
                                <a href="{{ sortUrl('id', $sortBy, $direction) }}"
                                    class="inline-flex items-center gap-1 hover:text-blue-600 transition-colors group">
                                    ID
                                    <span class="flex flex-col leading-none">
                                        <svg class="w-2.5 h-2.5 {{ $sortBy === 'id' && $direction === 'asc' ? 'text-blue-500' : 'text-gray-300 group-hover:text-gray-400' }}"
                                            fill="currentColor" viewBox="0 0 10 6">
                                            <path d="M5 0L10 6H0z" />
                                        </svg>
                                        <svg class="w-2.5 h-2.5 {{ $sortBy === 'id' && $direction === 'desc' ? 'text-blue-500' : 'text-gray-300 group-hover:text-gray-400' }}"
                                            fill="currentColor" viewBox="0 0 10 6">
                                            <path d="M5 6L0 0H10z" />
                                        </svg>
                                    </span>
                                </a>
                            </th>

                            {{-- Kolom sortable: Nama --}}
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <a href="{{ sortUrl('namaopd', $sortBy, $direction) }}"
                                    class="inline-flex items-center gap-1 hover:text-blue-600 transition-colors group">
                                    Nama Perangkat Daerah
                                    <span class="flex flex-col leading-none">
                                        <svg class="w-2.5 h-2.5 {{ $sortBy === 'namaopd' && $direction === 'asc' ? 'text-blue-500' : 'text-gray-300 group-hover:text-gray-400' }}"
                                            fill="currentColor" viewBox="0 0 10 6">
                                            <path d="M5 0L10 6H0z" />
                                        </svg>
                                        <svg class="w-2.5 h-2.5 {{ $sortBy === 'namaopd' && $direction === 'desc' ? 'text-blue-500' : 'text-gray-300 group-hover:text-gray-400' }}"
                                            fill="currentColor" viewBox="0 0 10 6">
                                            <path d="M5 6L0 0H10z" />
                                        </svg>
                                    </span>
                                </a>
                            </th>


                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                                Status</th>
                            <th class="px-4 py-3 w-40"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($opds as $index => $opd)
                            @php $isDeleted = $opd->trashed(); @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isDeleted ? 'opacity-50' : '' }}">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $opd->id }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $opd->namaopd }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($isDeleted)
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Dihapus</span>
                                    @else
                                        <span
                                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($isDeleted)
                                            <form action="{{ route('admin.opd.restore', $opd->id) }}" method="POST"
                                                onsubmit="return confirm('Pulihkan {{ addslashes($opd->namaopd) }}?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-green-50 text-green-600 hover:bg-green-100
                                                           border border-green-200 transition-colors">
                                                    Pulihkan
                                                </button>
                                            </form>
                                        @else
                                            <button type="button"
                                                onclick="openEditOpd({{ $opd->id }}, '{{ addslashes($opd->namaopd) }}')"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-blue-50 text-blue-600 hover:bg-blue-100
                                                       border border-blue-200 transition-colors">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.opd.destroy', $opd) }}" method="POST"
                                                onsubmit="return confirm('Hapus {{ addslashes($opd->namaopd) }}? Data tidak akan hilang permanen.')">
                                                @csrf
                                                @method('DELETE')
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
        @endif

    </div>


    {{-- ════ MODAL TAMBAH ════ --}}
    <div id="modalTambahOpd" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Perangkat Daerah</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Pemerintah Provinsi Bali</p>
                </div>
                <button onclick="document.getElementById('modalTambahOpd').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.opd.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Perangkat Daerah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="namaopd" value="{{ old('namaopd') }}" required autofocus
                        placeholder="Nama perangkat daerah"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalTambahOpd').classList.add('hidden')"
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
    <div id="modalEditOpd" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Edit Perangkat Daerah</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Pemerintah Provinsi Bali</p>
                </div>
                <button onclick="document.getElementById('modalEditOpd').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formEditOpd" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Perangkat Daerah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editNamaOpd" name="namaopd" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalEditOpd').classList.add('hidden')"
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
        function openEditOpd(id, nama) {
            document.getElementById('formEditOpd').action = `/admin/opd/${id}`;
            document.getElementById('editNamaOpd').value = nama;
            document.getElementById('modalEditOpd').classList.remove('hidden');
        }
    </script>

    @if ($errors->any())
        <script>
            document.getElementById('modalTambahOpd').classList.remove('hidden');
        </script>
    @endif

@endsection
