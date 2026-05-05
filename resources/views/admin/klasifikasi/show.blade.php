{{-- resources/views/admin/klasifikasi/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Sub Klasifikasi — ' . $klasifikasi->klasifikasiaset)
@section('page-title', $klasifikasi->klasifikasiaset)
@section('page-subtitle', 'Kelola sub klasifikasi aset · Kode: ' . $klasifikasi->kodeklas)

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

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-5">
        <a href="{{ route('admin.klasifikasi.index') }}" class="hover:text-blue-600 transition-colors">
            Klasifikasi Aset
        </a>
        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-700 font-medium">{{ $klasifikasi->klasifikasiaset }}</span>
    </div>


    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-gray-500">
                Daftar sub klasifikasi untuk
                <strong class="text-gray-700">{{ $klasifikasi->klasifikasiaset }}</strong>
            </p>
            <button onclick="document.getElementById('modalTambahSubklas').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Sub Klasifikasi
            </button>
        </div>

        {{-- Tabel --}}
        @if ($subklasifikasis->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada sub klasifikasi. Klik <strong>Tambah</strong> untuk menambahkan.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[500px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                #</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-56">
                                Nama Sub Klasifikasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Keterangan</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">
                                Status</th>
                            <th class="px-4 py-3 w-40"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($subklasifikasis as $sub)
                            @php $isDeleted = $sub->trashed(); @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isDeleted ? 'opacity-50' : '' }}">
                                <td class="px-4 py-3 text-xs text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $sub->subklasifikasiaset }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $sub->penjelasan ?? '—' }}</td>
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
                                            {{-- Pulihkan --}}
                                            <form
                                                action="{{ route('admin.klasifikasi.subklas.restore', [$klasifikasi->id, $sub->id]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Pulihkan sub klasifikasi {{ addslashes($sub->subklasifikasiaset) }}?')">
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
                                            {{-- Edit --}}
                                            <button type="button"
                                                onclick="openEditSubklas('{{ $sub->id }}', '{{ addslashes($sub->subklasifikasiaset) }}', '{{ addslashes($sub->penjelasan ?? '') }}')"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
           bg-blue-50 text-blue-600 hover:bg-blue-100
           border border-blue-200 transition-colors">
                                                Edit
                                            </button>
                                            {{-- Hapus --}}
                                            <form
                                                action="{{ route('admin.klasifikasi.subklas.destroy', [$klasifikasi, $sub]) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus sub klasifikasi {{ addslashes($sub->subklasifikasiaset) }}? Data tidak akan hilang permanen.')">
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
    <div id="modalTambahSubklas" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Sub Klasifikasi</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $klasifikasi->klasifikasiaset }}</p>
                </div>
                <button onclick="document.getElementById('modalTambahSubklas').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.klasifikasi.subklas.store', $klasifikasi) }}"
                class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Sub Klasifikasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subklasifikasiaset" value="{{ old('subklasifikasiaset') }}" required
                        autofocus placeholder="Nama sub klasifikasi"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                    <textarea name="penjelasan" rows="3" placeholder="Opsional"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('penjelasan') }}</textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalTambahSubklas').classList.add('hidden')"
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
    <div id="modalEditSubklas" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Edit Sub Klasifikasi</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $klasifikasi->klasifikasiaset }}</p>
                </div>
                <button onclick="document.getElementById('modalEditSubklas').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="formEditSubklas" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Sub Klasifikasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editNama" name="subklasifikasiaset" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan</label>
                    <textarea id="editPenjelasan" name="penjelasan" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalEditSubklas').classList.add('hidden')"
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
        function openEditSubklas(id, nama, penjelasan) {
            document.getElementById('formEditSubklas').action =
                '{{ route('admin.klasifikasi.subklas.update', [$klasifikasi, '__ID__']) }}'.replace('__ID__', id);
            document.getElementById('editNama').value = nama;
            document.getElementById('editPenjelasan').value = penjelasan;
            document.getElementById('modalEditSubklas').classList.remove('hidden');
        }
    </script>

    @if ($errors->any())
        <script>
            document.getElementById('modalTambahSubklas').classList.remove('hidden');
        </script>
    @endif

@endsection
