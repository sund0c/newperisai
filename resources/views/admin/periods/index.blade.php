{{-- resources/views/admin/periods/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Periode Waktu')
@section('page-title', 'Periode Waktu')
@section('page-subtitle', 'Tahun sebagai periode pemutakhiran data aset')

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
        <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 text-sm text-gray-500 flex-1">
                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Tahun digunakan sebagai periode pemutakhiran data aset. Periode aktif hanya boleh satu.</span>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Tahun
            </button>
        </div>

        {{-- Tabel --}}
        @if ($periods->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="17" rx="2" stroke-width="1.5" />
                    <line x1="3" y1="9" x2="21" y2="9" stroke-width="1.5" />
                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="1.5"
                        stroke-linecap="round" />
                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="1.5"
                        stroke-linecap="round" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada periode waktu.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 w-40"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($periods as $period)
                            @php $inUse = $period->asset_instances_count > 0; @endphp
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- Tahun --}}
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <rect x="3" y="4" width="18" height="17" rx="2"
                                                stroke-width="2" />
                                            <line x1="3" y1="9" x2="21" y2="9"
                                                stroke-width="2" />
                                            <line x1="8" y1="2" x2="8" y2="6"
                                                stroke-width="2" stroke-linecap="round" />
                                            <line x1="16" y1="2" x2="16" y2="6"
                                                stroke-width="2" stroke-linecap="round" />
                                            <text x="12" y="19" text-anchor="middle" font-size="7" font-weight="700"
                                                fill="currentColor" stroke="none">{{ substr($period->tahun, -2) }}</text>
                                        </svg>
                                        <span class="font-semibold text-gray-900">{{ $period->tahun }}</span>
                                    </div>
                                </td>

                                {{-- Status Aktif --}}
                                <td class="px-6 py-3">
                                    @if ($period->is_active)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                {{-- Aksi --}}
                                <td class="px-6 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">

                                        {{-- Set Aktif --}}
                                        @unless ($period->is_active)
                                            <form action="{{ route('admin.periods.activate', $period) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-green-50 text-green-600 hover:bg-green-100
                                                           border border-green-200 transition-colors">
                                                    Aktifkan
                                                </button>
                                            </form>
                                        @endunless

                                        {{-- Hapus — disabled jika sedang digunakan --}}
                                        @if ($inUse)
                                            <span
                                                title="Tidak dapat dihapus — periode sedang digunakan oleh {{ $period->asset_instances_count }} aset"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-gray-50 text-gray-300 border border-gray-200 cursor-not-allowed select-none">
                                                Hapus
                                            </span>
                                        @else
                                            <form action="{{ route('admin.periods.destroy', $period) }}" method="POST"
                                                onsubmit="return confirm('Hapus periode {{ $period->tahun }}?')">
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

    {{-- Modal Tambah Tahun --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            {{-- Header --}}
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Periode Tahun</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Periode pemutahiran data aset</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                       text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <form action="{{ route('admin.periods.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tahun" min="2025" max="2099"
                        value="{{ old('tahun', now()->year) }}" placeholder="2025"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required autofocus />
                    @error('tahun')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            class="rounded border-gray-300 text-blue-600">
                        Jadikan periode aktif
                    </label>
                    <p class="mt-1 text-xs text-gray-400 ml-5">
                        Periode aktif sebelumnya akan dinonaktifkan otomatis.
                    </p>
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

@endsection
