{{-- resources/views/admin/master-se/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Master Kategorisasi SE')
@section('page-title', 'Master Kategorisasi SE')
@section('page-subtitle', 'Kelola versi indikator penilaian Sekuriti Elektronik')

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
        Hanya <strong class="mx-1">satu versi</strong> yang boleh aktif pada satu waktu.
        Penilaian aset akan selalu menggunakan versi yang sedang aktif. Riwayat penilaian versi lama tetap tersimpan.
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <p class="text-sm text-gray-500">
                Total <strong class="text-gray-700">{{ $versions->total() }}</strong> versi terdaftar
            </p>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Versi
            </button>
        </div>

        {{-- Tabel --}}
        @if ($versions->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada versi SE terdaftar.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">#
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Kode
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Indikator</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Dibuat
                        </th>
                        <th class="px-6 py-3 w-48"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($versions as $version)
                        <tr class="hover:bg-gray-50 transition-colors {{ $version->is_active ? 'bg-blue-50/30' : '' }}">

                            {{-- No --}}
                            <td class="px-6 py-3 text-xs text-gray-400">{{ $versions->firstItem() + $loop->index }}</td>

                            {{-- Kode --}}
                            <td class="px-6 py-3">
                                <span
                                    class="font-mono text-sm font-bold {{ $version->is_active ? 'text-blue-600' : 'text-gray-700' }}">
                                    {{ $version->kode }}
                                </span>
                            </td>

                            {{-- Nama --}}
                            <td class="px-6 py-3">
                                <div class="text-sm text-gray-800">{{ $version->nama }}</div>
                                @if ($version->deskripsi)
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $version->deskripsi }}</div>
                                @endif
                            </td>

                            {{-- Indikator count --}}
                            <td class="px-6 py-3 text-center">
                                <span
                                    class="text-sm font-semibold {{ $version->indikators_count === 10 ? 'text-gray-700' : 'text-amber-500' }}">
                                    {{ $version->indikators_count }}
                                </span>
                                <span class="text-xs text-gray-400">/10</span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-3">
                                @if ($version->is_active)
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Dibuat --}}
                            <td class="px-6 py-3 text-xs text-gray-400">
                                {{ $version->created_at->translatedFormat('d M Y, H:i') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- Detail --}}
                                    <a href="{{ route('admin.master-se.show', $version) }}"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                               bg-gray-50 text-gray-600 hover:bg-gray-100
                                               border border-gray-200 transition-colors">
                                        Detail
                                    </a>

                                    @if ($version->is_active)
                                        {{-- Nonaktifkan: disabled jika dia satu-satunya yang aktif --}}
                                        @if ($totalAktif > 1)
                                            <form action="{{ route('admin.master-se.deactivate', $version) }}"
                                                method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-yellow-50 text-yellow-600 hover:bg-yellow-100
                                                           border border-yellow-200 transition-colors">
                                                    Nonaktifkan
                                                </button>
                                            </form>
                                        @else
                                            <span title="Harus ada minimal satu versi aktif"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-gray-50 text-gray-300 border border-gray-200
                                                       cursor-not-allowed select-none">
                                                Nonaktifkan
                                            </span>
                                        @endif

                                        {{-- Hapus disabled saat aktif --}}
                                        <span title="Nonaktifkan terlebih dahulu"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                   bg-gray-50 text-gray-300 border border-gray-200
                                                   cursor-not-allowed select-none">
                                            Hapus
                                        </span>
                                    @else
                                        {{-- Aktifkan — selalu bisa diklik, validasi di controller --}}
                                        <form action="{{ route('admin.master-se.activate', $version) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('Aktifkan {{ $version->kode }}? Versi aktif saat ini akan dinonaktifkan.')"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-green-50 text-green-600 hover:bg-green-100
                                                       border border-green-200 transition-colors">
                                                Aktifkan
                                            </button>
                                        </form>

                                        {{-- Hapus --}}
                                        @if ($version->penilaians_count === 0)
                                            <form action="{{ route('admin.master-se.destroy', $version) }}" method="POST"
                                                onsubmit="return confirm('Hapus versi {{ $version->kode }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-red-50 text-red-600 hover:bg-red-100
                                                           border border-red-200 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @else
                                            <span title="Versi sudah digunakan untuk penilaian"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-gray-50 text-gray-300 border border-gray-200
                                                       cursor-not-allowed select-none">
                                                Hapus
                                            </span>
                                        @endif
                                    @endif

                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($versions->hasPages())
                <div class="px-6 py-3 border-t border-gray-100">
                    {{ $versions->links() }}
                </div>
            @endif
        @endif

    </div>


    {{-- ════ MODAL TAMBAH ════ --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-lg">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Versi SE</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Kode versi dibuat otomatis</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.master-se.store-version') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Kode Versi</label>
                    <input type="text" name="kode" value="{{ $nextKode }}" readonly
                        class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 font-mono cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-400">Dibuat otomatis, tidak dapat diubah.</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Nama Versi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Versi 1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required autofocus />
                    @error('nama')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="deskripsi" rows="2" placeholder="Penjelasan singkat tentang versi ini..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none
                               focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi') }}</textarea>
                </div>
                <div
                    class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700 flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Setelah versi dibuat, lengkapi <strong class="mx-0.5">10 indikator</strong> melalui halaman Detail
                    sebelum dapat diaktifkan.
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
