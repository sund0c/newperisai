{{-- resources/views/admin/master-se/create.blade.php --}}
{{-- Untuk edit, ganti @php $isEdit = false; dengan true dan isi $seVersion --}}
@extends('layouts.admin')

@php $isEdit = isset($seVersion); @endphp
@section('title', $isEdit ? 'Edit Versi SE' : 'Buat Versi SE Baru')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.master-se.index') }}"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $isEdit ? 'Edit Versi SE' : 'Buat Versi SE Baru' }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $isEdit ? "Edit indikator {$seVersion->kode}" : 'Versi baru dengan 10 indikator' }}
                </p>
            </div>
        </div>

        <form action="{{ $isEdit ? route('admin.master-se.update', $seVersion) : route('admin.master-se.store') }}"
            method="POST" x-data="seForm()" x-init="init()" id="seForm">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            {{-- Info Versi --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Informasi Versi</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Kode Versi
                        </label>
                        <input type="text" name="kode" value="{{ old('kode', $isEdit ? $seVersion->kode : $kode) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono bg-gray-50 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            {{ $isEdit ? 'readonly' : '' }} required>
                        @error('kode')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Versi
                        </label>
                        <input type="text" name="nama" value="{{ old('nama', $isEdit ? $seVersion->nama : '') }}"
                            placeholder="Contoh: Versi 1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                        @error('nama')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi <span class="text-gray-400">(opsional)</span>
                        </label>
                        <textarea name="deskripsi" rows="2" placeholder="Penjelasan singkat tentang versi ini..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('deskripsi', $isEdit ? $seVersion->deskripsi : '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Daftar Indikator --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Indikator Penilaian</h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Harus tepat 10 indikator. Setiap indikator memiliki 3 pilihan jawaban dengan bobot nilai.
                        </p>
                    </div>
                    <span class="text-sm font-medium"
                        :class="indikators.length === 10 ? 'text-green-600' : 'text-amber-600'">
                        <span x-text="indikators.length"></span>/10 indikator
                    </span>
                </div>

                <div class="space-y-4">
                    <template x-for="(ind, idx) in indikators" :key="idx">
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                            {{-- Header indikator --}}
                            <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 dark:bg-gray-700 cursor-pointer"
                                @click="ind.open = !ind.open">
                                <span
                                    class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-100 text-blue-800 text-xs font-bold flex-shrink-0"
                                    x-text="idx + 1"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate"
                                        x-text="ind.pertanyaan || `Indikator ${idx + 1} — belum diisi`"></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    {{-- Validasi cepat --}}
                                    <svg x-show="isIndikatorValid(ind)" class="w-4 h-4 text-green-500 flex-shrink-0"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg x-show="!isIndikatorValid(ind)" class="w-4 h-4 text-amber-500 flex-shrink-0"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform"
                                        :class="ind.open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Body indikator --}}
                            <div x-show="ind.open" x-collapse class="px-4 py-4 space-y-4">
                                {{-- Hidden fields --}}
                                <input type="hidden" :name="`indikators[${idx}][id]`" :value="ind.id">

                                {{-- Pertanyaan --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        Pertanyaan / Indikator <span class="text-red-500">*</span>
                                    </label>
                                    <textarea :name="`indikators[${idx}][pertanyaan]`" x-model="ind.pertanyaan" rows="2"
                                        placeholder="Tulis pertanyaan indikator..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                        required></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Keterangan / Hint
                                        (opsional)</label>
                                    <input type="text" :name="`indikators[${idx}][keterangan]`" x-model="ind.keterangan"
                                        placeholder="Penjelasan tambahan untuk penilai..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{-- Pilihan jawaban --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-2">
                                        Pilihan Jawaban <span class="text-red-500">*</span>
                                        <span class="text-gray-400 font-normal">(3 pilihan dengan nilai bobot)</span>
                                    </label>
                                    <div class="space-y-2">
                                        <template x-for="p in [1, 2, 3]" :key="p">
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="w-6 h-6 flex items-center justify-center rounded-full text-xs font-medium flex-shrink-0"
                                                    :class="p === 1 ? 'bg-green-100 text-green-700' : p === 2 ?
                                                        'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'"
                                                    x-text="p"></span>
                                                <input type="text" :name="`indikators[${idx}][pilihan_${p}]`"
                                                    x-model="ind[`pilihan_${p}`]" :placeholder="`Label pilihan ${p}`"
                                                    class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    required>
                                                <div class="flex items-center gap-1 flex-shrink-0">
                                                    <span class="text-xs text-gray-500">Nilai:</span>
                                                    <input type="number" :name="`indikators[${idx}][nilai_${p}]`"
                                                        x-model="ind[`nilai_${p}`]" min="1" max="10"
                                                        class="w-16 px-2 py-1.5 border border-gray-300 rounded-lg text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                        required>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Validasi global --}}
                <div x-show="!allValid()"
                    class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                    ⚠ Lengkapi semua 10 indikator sebelum menyimpan.
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.master-se.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" :disabled="!allValid()"
                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Versi' }}
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function seForm() {
                return {
                    indikators: [],

                    init() {
                        // Prefill data lama (edit) dari PHP atau buat 10 slot kosong
                        const existing = @json($isEdit ? $seVersion->indikators : []);

                        if (existing.length > 0) {
                            this.indikators = existing.map((ind, i) => ({
                                id: ind.id,
                                pertanyaan: ind.pertanyaan,
                                keterangan: ind.keterangan ?? '',
                                pilihan_1: ind.pilihan_1,
                                nilai_1: ind.nilai_1,
                                pilihan_2: ind.pilihan_2,
                                nilai_2: ind.nilai_2,
                                pilihan_3: ind.pilihan_3,
                                nilai_3: ind.nilai_3,
                                open: false,
                            }));
                        } else {
                            // Buat 10 slot kosong, buka slot pertama
                            for (let i = 0; i < 10; i++) {
                                this.indikators.push({
                                    id: null,
                                    pertanyaan: '',
                                    keterangan: '',
                                    pilihan_1: '',
                                    nilai_1: 1,
                                    pilihan_2: '',
                                    nilai_2: 2,
                                    pilihan_3: '',
                                    nilai_3: 3,
                                    open: i === 0,
                                });
                            }
                        }

                        // Buka indikator yang belum valid (saat edit)
                        this.$nextTick(() => {
                            const firstInvalid = this.indikators.findIndex(ind => !this.isIndikatorValid(ind));
                            if (firstInvalid >= 0) this.indikators[firstInvalid].open = true;
                        });
                    },

                    isIndikatorValid(ind) {
                        return ind.pertanyaan?.trim() &&
                            ind.pilihan_1?.trim() && ind.pilihan_2?.trim() && ind.pilihan_3?.trim() &&
                            ind.nilai_1 && ind.nilai_2 && ind.nilai_3;
                    },

                    allValid() {
                        return this.indikators.length === 10 && this.indikators.every(ind => this.isIndikatorValid(ind));
                    },
                };
            }
        </script>
    @endpush
@endsection
