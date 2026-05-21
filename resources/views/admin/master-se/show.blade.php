{{-- resources/views/admin/master-se/show.blade.php --}}
@extends('layouts.admin')

@section('title', $seVersion->kode . ' — ' . $seVersion->nama)
@section('page-title', $seVersion->nama)
@section('page-subtitle', 'Kelola indikator penilaian · Kode: ' . $seVersion->kode)

@section('content')

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

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-4">
        <a href="{{ route('admin.master-se.index') }}" class="hover:text-gray-600 transition-colors">Master Kategorisasi
            SE</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-600 font-medium">{{ $seVersion->kode }} — {{ $seVersion->nama }}</span>
    </div>

    @if ($seVersion->is_active)
        <div
            class="mb-4 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse shrink-0"></span>
            Versi ini sedang <strong class="mx-1">aktif</strong> — penilaian aset baru akan menggunakan indikator dari
            versi ini.
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4">
            <p class="text-sm text-gray-500">
                Daftar indikator untuk <strong class="text-gray-700">{{ $seVersion->kode }} —
                    {{ $seVersion->nama }}</strong>
            </p>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Indikator
            </button>
        </div>

        @if ($seVersion->indikators->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-gray-400">Belum ada indikator. Tambahkan indikator pertama.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">#
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Pertanyaan / Indikator</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Pilihan
                            Jawaban</th>
                        <th class="px-6 py-3 w-32"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($seVersion->indikators as $ind)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3 text-xs text-gray-800">{{ $ind->urutan }}</td>
                            <td class="px-6 py-3">
                                <div class="text-xs font-mono text-gray-800">{{ $ind->pertanyaan }}</div>
                                @if ($ind->keterangan)
                                    <div class="text-xs font-mono text-gray-800">{{ $ind->keterangan }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-5 h-5 flex items-center justify-center rounded-full bg-green-100 text-green-700 text-xs font-semibold shrink-0">1</span>
                                        <span class="text-xs font-mono text-gray-800">{{ $ind->pilihan_1 }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-5 h-5 flex items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-semibold shrink-0">2</span>
                                        <span class="text-xs font-mono text-gray-800">{{ $ind->pilihan_2 }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-5 h-5 flex items-center justify-center rounded-full bg-red-100 text-red-700 text-xs font-semibold shrink-0">3</span>
                                        <span class="text-xs font-mono text-gray-800">{{ $ind->pilihan_3 }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button data-id="{{ $ind->id }}" data-urutan="{{ $ind->urutan }}"
                                        data-pertanyaan="{{ $ind->pertanyaan }}" data-keterangan="{{ $ind->keterangan }}"
                                        data-pilihan1="{{ $ind->pilihan_1 }}" data-pilihan2="{{ $ind->pilihan_2 }}"
                                        data-pilihan3="{{ $ind->pilihan_3 }}" onclick="bukaModalEdit(this)"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                               bg-gray-50 text-gray-600 hover:bg-gray-100
                                               border border-gray-200 transition-colors">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.master-se.indikator.destroy', [$seVersion, $ind]) }}"
                                        method="POST" onsubmit="return confirm('Hapus indikator #{{ $ind->urutan }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold
                                                   bg-red-50 text-red-600 hover:bg-red-100
                                                   border border-red-200 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>


    {{-- ════ MODAL TAMBAH ════ --}}
    <div id="modalTambah"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/60 px-4 py-8 overflow-y-auto"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-xl">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Indikator</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $seVersion->kode }} — {{ $seVersion->nama }}</p>
                </div>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-400 transition-colors text-sm">✕</button>
            </div>
            <form action="{{ route('admin.master-se.indikator.store', $seVersion) }}" method="POST"
                class="px-6 py-5 space-y-5">
                @csrf
                {{-- Nomor urut --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">
                        Nomor Urut <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="urutan"
                        value="{{ old('urutan', $seVersion->indikators->count() + 1) }}" min="1" max="99"
                        class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>
                {{-- Pertanyaan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">
                        Pertanyaan / Indikator <span class="text-red-500">*</span>
                    </label>
                    <textarea name="pertanyaan" rows="3" placeholder="Tulis pertanyaan indikator..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none
                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>{{ old('pertanyaan') }}</textarea>
                </div>
                {{-- Keterangan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">
                        Keterangan / Hint <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                        placeholder="Penjelasan tambahan untuk penilai..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                {{-- Pilihan jawaban — card style --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-2">
                        Pilihan Jawaban <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal">(3 pilihan)</span>
                    </label>
                    <div class="space-y-2">
                        @foreach ([['n' => 1, 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'ring' => 'focus-within:border-green-400', 'ph' => 'Tulis label pilihan pertama...'], ['n' => 2, 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'ring' => 'focus-within:border-blue-400', 'ph' => 'Tulis label pilihan kedua...'], ['n' => 3, 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'ring' => 'focus-within:border-red-400', 'ph' => 'Tulis label pilihan ketiga...']] as $p)
                            <div
                                class="flex items-center gap-3 px-4 py-3 border-2 border-gray-200 rounded-xl transition-colors {{ $p['ring'] }}">
                                <span
                                    class="w-7 h-7 flex items-center justify-center rounded-full {{ $p['bg'] }} {{ $p['text'] }} text-xs font-bold shrink-0">{{ $p['n'] }}</span>
                                <input type="text" name="pilihan_{{ $p['n'] }}"
                                    value="{{ old('pilihan_' . $p['n']) }}" placeholder="{{ $p['ph'] }}"
                                    class="flex-1 text-sm bg-transparent border-none outline-none text-gray-800 placeholder-gray-400"
                                    required />
                            </div>
                        @endforeach
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


    {{-- ════ MODAL EDIT ════ --}}
    <div id="modalEdit"
        class="hidden fixed inset-0 z-50 flex items-start justify-center bg-black/60 px-4 py-8 overflow-y-auto"
        onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-xl">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Edit Indikator</h3>
                    <p class="text-xs text-gray-500 mt-0.5" id="editSubtitle">—</p>
                </div>
                <button onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-400 transition-colors text-sm">✕</button>
            </div>
            <form id="formEdit" method="POST" class="px-6 py-5 space-y-5">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Nomor Urut <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="urutan" id="edit_urutan" min="1" max="99"
                        class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Pertanyaan / Indikator <span
                            class="text-red-500">*</span></label>
                    <textarea name="pertanyaan" id="edit_pertanyaan" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Keterangan / Hint <span
                            class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" name="keterangan" id="edit_keterangan"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-2">
                        Pilihan Jawaban <span class="text-red-500">*</span>
                        <span class="text-gray-400 font-normal">(3 pilihan)</span>
                    </label>
                    <div class="space-y-2">
                        @foreach ([['n' => 1, 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'ring' => 'focus-within:border-green-400'], ['n' => 2, 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'ring' => 'focus-within:border-blue-400'], ['n' => 3, 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'ring' => 'focus-within:border-red-400']] as $p)
                            <div
                                class="flex items-center gap-3 px-4 py-3 border-2 border-gray-200 rounded-xl transition-colors {{ $p['ring'] }}">
                                <span
                                    class="w-7 h-7 flex items-center justify-center rounded-full {{ $p['bg'] }} {{ $p['text'] }} text-xs font-bold shrink-0">{{ $p['n'] }}</span>
                                <input type="text" name="pilihan_{{ $p['n'] }}"
                                    id="edit_pilihan{{ $p['n'] }}"
                                    class="flex-1 text-sm bg-transparent border-none outline-none text-gray-800 placeholder-gray-400"
                                    placeholder="Pilihan {{ $p['n'] }}" required />
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-1">
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

    @if ($errors->any())
        <script>
            document.getElementById('modalTambah').classList.remove('hidden');
        </script>
    @endif

    <script>
        const baseEditUrl = '{{ route('admin.master-se.indikator.update', [$seVersion, ':indikator']) }}';

        function bukaModalEdit(btn) {
            document.getElementById('formEdit').action = baseEditUrl.replace(':indikator', btn.dataset.id);
            document.getElementById('editSubtitle').textContent = '{{ $seVersion->kode }} — Indikator #' + btn.dataset
                .urutan;
            document.getElementById('edit_urutan').value = btn.dataset.urutan;
            document.getElementById('edit_pertanyaan').value = btn.dataset.pertanyaan;
            document.getElementById('edit_keterangan').value = btn.dataset.keterangan ?? '';
            document.getElementById('edit_pilihan1').value = btn.dataset.pilihan1;
            document.getElementById('edit_pilihan2').value = btn.dataset.pilihan2;
            document.getElementById('edit_pilihan3').value = btn.dataset.pilihan3;
            document.getElementById('modalEdit').classList.remove('hidden');
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.getElementById('modalTambah').classList.add('hidden');
                document.getElementById('modalEdit').classList.add('hidden');
            }
        });
    </script>

@endsection
