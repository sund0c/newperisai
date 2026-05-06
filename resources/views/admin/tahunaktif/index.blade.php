{{-- resources/views/admin/tahunaktif/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tahun Aset')
@section('page-title', 'Tahun Aset')
@section('page-subtitle', 'Kelola tahun aktif pemutakhiran data aset')

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

    {{-- Info --}}
    <div class="mb-4 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Hanya <strong class="mx-1">satu tahun</strong> yang boleh aktif pada satu waktu.
        Data aset yang diinput akan otomatis terikat ke tahun yang sedang aktif.
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <p class="text-sm text-gray-500">
                Total <strong class="text-gray-700">{{ $tahuns->count() }}</strong> tahun terdaftar
            </p>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Tahun
            </button>
        </div>

        {{-- Tabel --}}
        @if ($tahuns->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada tahun terdaftar.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">#
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tahun
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Ditambahkan</th>
                        <th class="px-6 py-3 w-48"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($tahuns as $tahun)
                        <tr class="hover:bg-gray-50 transition-colors {{ $tahun->is_active ? 'bg-blue-50/30' : '' }}">

                            {{-- No --}}
                            <td class="px-6 py-3 text-xs text-gray-400">{{ $loop->iteration }}</td>

                            {{-- Tahun --}}
                            <td class="px-6 py-3">
                                <span
                                    class="text-2xl font-bold {{ $tahun->is_active ? 'text-blue-600' : 'text-gray-700' }}">
                                    {{ $tahun->tahun }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-3">
                                @if ($tahun->is_active)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold bg-blue-100 text-blue-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                 text-xs font-semibold bg-gray-100 text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Ditambahkan --}}
                            <td class="px-6 py-3 text-xs text-gray-400">
                                {{ $tahun->created_at->translatedFormat('d M Y, H:i') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">

                                    @if ($tahun->is_active)
                                        {{-- Nonaktifkan --}}
                                        <form action="{{ route('admin.tahunaktif.deactivate', $tahun) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-yellow-50 text-yellow-600 hover:bg-yellow-100
                                                       border border-yellow-200 transition-colors">
                                                Nonaktifkan
                                            </button>
                                        </form>

                                        {{-- Hapus disabled saat aktif --}}
                                        <span title="Nonaktifkan terlebih dahulu"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                   bg-gray-50 text-gray-300 border border-gray-200
                                                   cursor-not-allowed select-none">
                                            Hapus
                                        </span>
                                    @else
                                        {{-- Aktifkan --}}
                                        <form action="{{ route('admin.tahunaktif.activate', $tahun) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-green-50 text-green-600 hover:bg-green-100
                                                       border border-green-200 transition-colors">
                                                Aktifkan
                                            </button>
                                        </form>

                                        {{-- Hapus --}}
                                        <form action="{{ route('admin.tahunaktif.destroy', $tahun) }}" method="POST"
                                            onsubmit="return confirm('Hapus tahun {{ $tahun->tahun }}? Tindakan ini tidak dapat dibatalkan.')">
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
        @endif

    </div>


    {{-- ════ MODAL TAMBAH ════ --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Tahun</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Pemutakhiran data aset tahunan</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.tahunaktif.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tahun" value="{{ old('tahun', now()->year) }}" min="2020"
                        max="2099" placeholder="{{ now()->year }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required autofocus />
                    @error('tahun')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400">Tahun tidak dapat diubah setelah disimpan.</p>
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

    @if ($errors->any())
        <script>
            document.getElementById('modalTambah').classList.remove('hidden');
        </script>
    @endif

    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('modalTambah').classList.add('hidden');
            }
        });
    </script>

@endsection
