{{-- resources/views/admin/periods/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Periode')
@section('page-title', 'Manajemen Periode')
@section('page-subtitle', 'Kelola periode pemutakhiran berdasarkan jenis')

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
    @if ($errors->has('period'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $errors->first('period') }}
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
                <span>Setiap jenis periode hanya boleh memiliki <strong class="text-gray-700">satu periode aktif</strong>.
                    Periode berbeda jenis boleh berjalan bersamaan.</span>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Periode
            </button>
        </div>

        {{-- Tabel --}}
        @if ($periods->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada periode.</p>
            </div>
        @else
            {{-- Group per jenis periode --}}
            @foreach ($periods as $jenis => $items)
                @php $jenisEnum = \App\Enums\JenisPeriode::from($jenis); @endphp

                <div class="border-b border-gray-100 last:border-0">
                    {{-- Group Header --}}
                    <div class="px-6 py-2.5 bg-gray-50 flex items-center gap-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ $jenisEnum->label() }}
                        </span>
                        <span class="text-xs text-gray-400">({{ $items->count() }} periode)</span>
                    </div>

                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-100">
                            <tr>
                                <th
                                    class="px-6 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">
                                    #</th>
                                <th
                                    class="px-6 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Nama Periode</th>
                                <th
                                    class="px-6 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Tanggal Mulai</th>
                                <th
                                    class="px-6 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Tanggal Selesai</th>
                                <th
                                    class="px-6 py-2.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-2.5 w-48"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($items as $period)
                                <tr class="hover:bg-gray-50 transition-colors">

                                    {{-- No --}}
                                    <td class="px-6 py-3 text-xs text-gray-400">{{ $loop->iteration }}</td>

                                    {{-- Nama --}}
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $period->nama_periode }}</td>

                                    {{-- Tanggal Mulai --}}
                                    <td class="px-6 py-3 text-gray-600 text-xs">
                                        {{ $period->tanggal_mulai->translatedFormat('d M Y') }}
                                    </td>

                                    {{-- Tanggal Selesai --}}
                                    <td class="px-6 py-3 text-gray-600 text-xs">
                                        {{ $period->tanggal_selesai->translatedFormat('d M Y') }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-3">
                                        <div class="flex flex-col gap-1">
                                            @if ($period->is_active)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 w-fit">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 w-fit">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                    Tidak Aktif
                                                </span>
                                            @endif

                                            @if ($period->berjalan)
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 w-fit">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                    Berjalan
                                                </span>
                                            @elseif (now()->isAfter($period->tanggal_selesai))
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-600 w-fit">
                                                    Selesai
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 w-fit">
                                                    Terjadwal
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-6 py-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">

                                            {{-- Edit --}}
                                            <button type="button"
                                                onclick="openEdit('{{ $period->id }}', '{{ addslashes($period->nama_periode) }}', '{{ $period->jenis_periode->value }}', '{{ $period->tanggal_mulai->format('Y-m-d') }}', '{{ $period->tanggal_selesai->format('Y-m-d') }}')"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                       bg-blue-50 text-blue-600 hover:bg-blue-100
                                                       border border-blue-200 transition-colors">
                                                Edit
                                            </button>

                                            {{-- Aktifkan / Nonaktifkan --}}
                                            @if ($period->is_active)
                                                <form action="{{ route('admin.periods.deactivate', $period) }}"
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
                                                <form action="{{ route('admin.periods.activate', $period) }}"
                                                    method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                               bg-green-50 text-green-600 hover:bg-green-100
                                                               border border-green-200 transition-colors">
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Hapus --}}
                                            @if (!$period->is_active)
                                                <form action="{{ route('admin.periods.destroy', $period) }}" method="POST"
                                                    onsubmit="return confirm('Hapus periode {{ addslashes($period->nama_periode) }}?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                               bg-red-50 text-red-600 hover:bg-red-100
                                                               border border-red-200 transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span title="Nonaktifkan terlebih dahulu sebelum menghapus"
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                           bg-gray-50 text-gray-300 border border-gray-200
                                                           cursor-not-allowed select-none">
                                                    Hapus
                                                </span>
                                            @endif

                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Modal Tambah --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Periode</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Isi detail periode baru</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200
                           text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.periods.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Periode <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nama_periode" value="{{ old('nama_periode') }}"
                        placeholder="cth: Pemutakhiran Aset Q1 2026"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required autofocus />
                    @error('nama_periode')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Periode <span
                            class="text-red-500">*</span></label>
                    <select name="jenis_periode"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach (\App\Enums\JenisPeriode::cases() as $jenis)
                            <option value="{{ $jenis->value }}"
                                {{ old('jenis_periode') === $jenis->value ? 'selected' : '' }}>
                                {{ $jenis->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_periode')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Mulai <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('tanggal_mulai')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Selesai <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                        @error('tanggal_selesai')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
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

    {{-- Modal Edit --}}
    <div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Edit Periode</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui detail periode</p>
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

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nama Periode <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="editNama" name="nama_periode"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Periode <span
                            class="text-red-500">*</span></label>
                    <select id="editJenis" name="jenis_periode"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        @foreach (\App\Enums\JenisPeriode::cases() as $jenis)
                            <option value="{{ $jenis->value }}">{{ $jenis->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Mulai <span
                                class="text-red-500">*</span></label>
                        <input type="date" id="editTanggalMulai" name="tanggal_mulai"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Selesai <span
                                class="text-red-500">*</span></label>
                        <input type="date" id="editTanggalSelesai" name="tanggal_selesai"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required />
                    </div>
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
    @if ($errors->any() && !$errors->has('period'))
        <script>
            document.getElementById('modalTambah').classList.remove('hidden');
        </script>
    @endif

    <script>
        function openEdit(id, nama, jenis, mulai, selesai) {
            document.getElementById('formEdit').action =
                '{{ route('admin.periods.update', '__ID__') }}'.replace('__ID__', id);
            document.getElementById('editNama').value = nama;
            document.getElementById('editJenis').value = jenis;
            document.getElementById('editTanggalMulai').value = mulai;
            document.getElementById('editTanggalSelesai').value = selesai;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>

@endsection
