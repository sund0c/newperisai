{{-- resources/views/admin/risk-register/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Risk Register — ' . $riskRegister->kode_rr)
@section('page-title', $riskRegister->kode_rr)
@section('page-subtitle', ($riskRegister->asset->nama_aset ?? '-') . ' — ' . ($riskRegister->opd->namaopd ?? '-'))

@section('content')

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Header bar --}}
    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> Draft
            </span>
            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">v{{ $riskRegister->versi }}</span>
        </div>
        @if(true)
        <form method="POST" action="{{ route('admin.risk-register.finalize', $riskRegister) }}"
              onsubmit="return confirm('Finalisasi Risk Register ini? Setelah final tidak dapat diubah lagi.')">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Finalisasi
            </button>
        </form>
        @endif
    </div>

    <div x-data="rrEditor()" x-init="init()">

        {{-- Statistik level risiko --}}
        @php
            $items       = $riskRegister->items;
            $totalItems  = $items->count();
            $levelCounts = $items->groupBy('inherent_level')->map->count();
        @endphp
        @if($totalItems > 0)
        <div class="grid grid-cols-5 gap-3 mb-4">
            @foreach([
                ['Sangat Tinggi', 'bg-red-50 border-red-200 text-red-700'],
                ['Tinggi',        'bg-orange-50 border-orange-200 text-orange-700'],
                ['Sedang',        'bg-yellow-50 border-yellow-200 text-yellow-700'],
                ['Rendah',        'bg-blue-50 border-blue-200 text-blue-700'],
                ['Sangat Rendah', 'bg-green-50 border-green-200 text-green-700'],
            ] as [$lvl, $cls])
            <div class="border rounded-lg p-3 {{ $cls }} text-center">
                <div class="text-2xl font-bold">{{ $levelCounts[$lvl] ?? 0 }}</div>
                <div class="text-xs font-medium mt-0.5">{{ $lvl }}</div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Tabel item risiko --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800 text-sm">
                    Daftar Item Risiko
                    <span class="ml-1 text-xs font-normal text-gray-400">({{ $totalItems }} item)</span>
                </h3>
                <button @click="openAddModal()"
                        class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Item
                </button>
            </div>

            @if($totalItems > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-2.5 text-left font-medium text-gray-600 w-8">No</th>
                            <th class="px-3 py-2.5 text-left font-medium text-gray-600">Ancaman / Kerawanan</th>
                            <th class="px-3 py-2.5 text-left font-medium text-gray-600">Kategori</th>
                            <th class="px-3 py-2.5 text-center font-medium text-gray-600">D</th>
                            <th class="px-3 py-2.5 text-center font-medium text-gray-600">K</th>
                            <th class="px-3 py-2.5 text-center font-medium text-gray-600">Skor</th>
                            <th class="px-3 py-2.5 text-center font-medium text-gray-600">Level</th>
                            <th class="px-3 py-2.5 text-center font-medium text-gray-600">Keputusan</th>
                            <th class="px-3 py-2.5 text-center font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $item)
                        @php
                            $badge = match($item->inherent_level) {
                                'Sangat Tinggi' => 'bg-red-100 text-red-800',
                                'Tinggi'        => 'bg-orange-100 text-orange-800',
                                'Sedang'        => 'bg-yellow-100 text-yellow-800',
                                'Rendah'        => 'bg-blue-100 text-blue-800',
                                default         => 'bg-green-100 text-green-800',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2.5 text-gray-400 font-mono">{{ $item->risk_no }}</td>
                            <td class="px-3 py-2.5 max-w-[220px]">
                                <div class="font-medium text-gray-800 line-clamp-1">{{ $item->ancaman }}</div>
                                <div class="text-gray-400 line-clamp-1 mt-0.5">{{ $item->kerawanan }}</div>
                            </td>
                            <td class="px-3 py-2.5 text-gray-500">{{ $item->kategori ?? '-' }}</td>
                            <td class="px-3 py-2.5 text-center font-semibold text-gray-700">{{ $item->inherent_dampak }}</td>
                            <td class="px-3 py-2.5 text-center font-semibold text-gray-700">{{ $item->inherent_kemungkinan }}</td>
                            <td class="px-3 py-2.5 text-center font-bold text-gray-800">{{ $item->inherent_skor }}</td>
                            <td class="px-3 py-2.5 text-center">
                                <span class="px-1.5 py-0.5 rounded text-xs font-medium {{ $badge }}">{{ $item->inherent_level ?? '-' }}</span>
                            </td>
                            <td class="px-3 py-2.5 text-center text-gray-500">{{ $item->keputusan_penanganan ?? '-' }}</td>
                            <td class="px-3 py-2.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button"
                                            data-item="{{ $item->toJson() }}"
                                            @click="openEditModalWithData(JSON.parse($el.dataset.item))"
                                            class="p-1 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded transition" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form method="POST"
                                          action="{{ route('admin.risk-register.items.destroy', [$riskRegister, $item]) }}"
                                          onsubmit="return confirm('Hapus item risiko #{{ $item->risk_no }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-12 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">Belum ada item risiko. Klik "Tambah Item" untuk mulai.</p>
            </div>
            @endif
        </div>

        {{-- ═══════════════════════════════════ --}}
        {{-- MODAL TAMBAH / EDIT ITEM           --}}
        {{-- ═══════════════════════════════════ --}}
        <div x-show="showModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             @keydown.escape.window="showModal = false">
            <div class="flex items-start justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-black/40" @click="showModal = false"></div>

                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl my-8 z-10">
                    {{-- Modal header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div>
                            <h3 class="font-semibold text-gray-800" x-text="editMode ? 'Edit Item Risiko' : 'Tambah Item Risiko'"></h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $riskRegister->kode_rr }} — {{ $riskRegister->asset->nama_aset ?? '-' }}</p>
                        </div>
                        <button @click="showModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form :action="editMode
                            ? '{{ url('admin/risk-register/' . $riskRegister->id . '/items') }}/' + editItemId
                            : '{{ route('admin.risk-register.items.store', $riskRegister) }}'"
                          method="POST">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="editMode ? 'PATCH' : 'POST'">

                        <div class="px-6 py-5 space-y-5 max-h-[70vh] overflow-y-auto">

                            {{-- Auto-isi dari master kerawanan --}}
                            <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl">
                                <label class="block text-xs font-semibold text-blue-700 mb-2 uppercase tracking-wide">Auto-isi dari Master Kerawanan</label>
                                <select @change="fillFromMaster($event.target)"
                                        class="w-full text-sm border border-blue-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="">-- Pilih kerawanan dari master (opsional) --</option>
                                    @foreach($masterKerawanan as $mk)
                                    <option value="{{ $mk->id }}"
                                            data-ancaman="{{ $mk->ancaman_tipikal }}"
                                            data-kerawanan="{{ $mk->deskripsi }}"
                                            data-dampak="{{ $mk->dampak_tipikal }}"
                                            data-kontrol="{{ $mk->kontrol_tipikal }}"
                                            data-mitigasi="{{ $mk->mitigasi_tipikal }}"
                                            data-kategori="{{ $mk->set->versi ?? "" }}">
                                        {{ $mk->ancaman_tipikal }} — {{ Str::limit($mk->kerawanan, 60) }}
                                    </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-blue-500 mt-1.5">Pilih dari master untuk mengisi field secara otomatis. Semua field tetap dapat diedit.</p>
                            </div>

                            <input type="hidden" name="vulnerability_item_id" x-model="form.vulnerability_item_id">

                            {{-- Jenis Risiko + Kategori --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Jenis Risiko</label>
                                    <input type="text" name="jenis_risiko" x-model="form.jenis_risiko"
                                           placeholder="cth: Keamanan Informasi, Operasional"
                                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Kategori</label>
                                    <input type="text" name="kategori" x-model="form.kategori"
                                           placeholder="cth: Teknis, Non-teknis"
                                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            {{-- Ancaman & Kerawanan --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Ancaman <span class="text-red-500">*</span></label>
                                    <textarea name="ancaman" x-model="form.ancaman" required rows="3"
                                              placeholder="Deskripsikan ancaman..."
                                              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Kerawanan / Kelemahan <span class="text-red-500">*</span></label>
                                    <textarea name="kerawanan" x-model="form.kerawanan" required rows="3"
                                              placeholder="Deskripsikan kerawanan..."
                                              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>

                            {{-- Dampak Detail + Area Dampak --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Dampak Detail</label>
                                    <textarea name="dampak_detail" x-model="form.dampak_detail" rows="3"
                                              placeholder="Uraikan dampak yang mungkin terjadi..."
                                              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Area Dampak</label>
                                    <div class="space-y-1.5">
                                        @foreach($areaDampakOptions as $opt)
                                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                                            <input type="checkbox" name="area_dampak[]" value="{{ $opt }}"
                                                   :checked="form.area_dampak?.includes('{{ $opt }}')"
                                                   @change="toggleAreaDampak('{{ $opt }}')"
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            {{ $opt }}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Kontrol & Mitigasi --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Kontrol Saat Ini</label>
                                    <textarea name="kontrol_saat_ini" x-model="form.kontrol_saat_ini" rows="3"
                                              placeholder="Kontrol keamanan yang sudah diterapkan..."
                                              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Rencana Aksi Penanganan</label>
                                    <textarea name="rencana_aksi" x-model="form.rencana_aksi" rows="3"
                                              placeholder="Langkah mitigasi yang direncanakan..."
                                              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>

                            {{-- Inherent Risk --}}
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Nilai Risiko Bawaan (Inherent Risk)</h4>
                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Dampak <span class="text-red-500">*</span></label>
                                        <select name="inherent_dampak" x-model="form.inherent_dampak" required @change="hitungSkor()"
                                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Pilih --</option>
                                            <option value="1">1 — Tidak Signifikan</option>
                                            <option value="2">2 — Kurang Signifikan</option>
                                            <option value="3">3 — Cukup Signifikan</option>
                                            <option value="4">4 — Signifikan</option>
                                            <option value="5">5 — Sangat Signifikan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Kemungkinan <span class="text-red-500">*</span></label>
                                        <select name="inherent_kemungkinan" x-model="form.inherent_kemungkinan" required @change="hitungSkor()"
                                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Pilih --</option>
                                            <option value="1">1 — Hampir Tidak Terjadi</option>
                                            <option value="2">2 — Jarang Terjadi</option>
                                            <option value="3">3 — Kadang-Kadang</option>
                                            <option value="4">4 — Sering Terjadi</option>
                                            <option value="5">5 — Hampir Pasti</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Hasil</label>
                                        <div class="p-2 rounded-lg text-center border transition"
                                             :class="{
                                                 'bg-red-50 border-red-300 text-red-800': inherentLevel === 'Sangat Tinggi',
                                                 'bg-orange-50 border-orange-300 text-orange-800': inherentLevel === 'Tinggi',
                                                 'bg-yellow-50 border-yellow-300 text-yellow-800': inherentLevel === 'Sedang',
                                                 'bg-blue-50 border-blue-300 text-blue-800': inherentLevel === 'Rendah',
                                                 'bg-green-50 border-green-300 text-green-800': inherentLevel === 'Sangat Rendah',
                                                 'bg-gray-50 border-gray-200 text-gray-400': !inherentLevel,
                                             }">
                                            <div class="text-xl font-bold" x-text="inherentSkor || '-'"></div>
                                            <div class="text-xs font-medium" x-text="inherentLevel || 'pilih D & K'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Evaluasi --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Keputusan Penanganan</label>
                                    <select name="keputusan_penanganan" x-model="form.keputusan_penanganan"
                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Pilih --</option>
                                        @foreach($keputusanOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Prioritas Risiko</label>
                                    <select name="prioritas_risiko" x-model="form.prioritas_risiko"
                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Pilih --</option>
                                        @foreach($prioritasOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Opsi Penanganan</label>
                                    <input type="text" name="opsi_penanganan" x-model="form.opsi_penanganan"
                                           placeholder="cth: Implementasi WAF"
                                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Keluaran</label>
                                    <input type="text" name="keluaran" x-model="form.keluaran"
                                           placeholder="cth: Laporan penerapan kontrol"
                                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Target / Jadwal</label>
                                    <input type="text" name="target_jadwal" x-model="form.target_jadwal"
                                           placeholder="cth: Q2 2025"
                                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Penanggung Jawab</label>
                                    <input type="text" name="penanggung_jawab" x-model="form.penanggung_jawab"
                                           placeholder="Nama / Jabatan"
                                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Risk Owner</label>
                                    <input type="text" name="risk_owner" x-model="form.risk_owner"
                                           placeholder="Pemilik risiko"
                                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            {{-- Residual Risk --}}
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                <label class="flex items-center gap-3 px-4 py-3 bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="ada_residual_risk" value="1"
                                           x-model="form.ada_residual_risk"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Terdapat Residual Risk setelah penanganan</span>
                                </label>
                                <div x-show="form.ada_residual_risk" x-collapse class="p-4 bg-orange-50 space-y-4">
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Dampak Residual</label>
                                            <select name="residual_dampak" x-model="form.residual_dampak" @change="hitungResidualSkor()"
                                                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                                                <option value="">-- Pilih --</option>
                                                <option value="1">1 — Tidak Signifikan</option>
                                                <option value="2">2 — Kurang Signifikan</option>
                                                <option value="3">3 — Cukup Signifikan</option>
                                                <option value="4">4 — Signifikan</option>
                                                <option value="5">5 — Sangat Signifikan</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Kemungkinan Residual</label>
                                            <select name="residual_kemungkinan" x-model="form.residual_kemungkinan" @change="hitungResidualSkor()"
                                                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                                                <option value="">-- Pilih --</option>
                                                <option value="1">1 — Hampir Tidak Terjadi</option>
                                                <option value="2">2 — Jarang Terjadi</option>
                                                <option value="3">3 — Kadang-Kadang</option>
                                                <option value="4">4 — Sering Terjadi</option>
                                                <option value="5">5 — Hampir Pasti</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Hasil Residual</label>
                                            <div class="p-2 rounded-lg text-center border border-orange-200 bg-white">
                                                <div class="text-xl font-bold text-gray-800" x-text="residualSkor || '-'"></div>
                                                <div class="text-xs text-gray-500" x-text="residualLevel || '-'"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Status Residual</label>
                                        <select name="residual_status" x-model="form.residual_status"
                                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                                            <option value="">-- Pilih --</option>
                                            <option value="Acceptable">Acceptable</option>
                                            <option value="Not Acceptable">Not Acceptable</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Kontrol Tambahan --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1.5">Rencana Kontrol Tambahan</label>
                                <textarea name="rencana_kontrol_tambahan" x-model="form.rencana_kontrol_tambahan" rows="2"
                                          placeholder="Kontrol tambahan yang direncanakan..."
                                          class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>

                        </div>

                        {{-- Modal footer --}}
                        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false"
                                    class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition"
                                    x-text="editMode ? 'Simpan Perubahan' : 'Tambah Item'">
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>{{-- end x-data --}}

@endsection

@push('scripts')
<script>
const RISK_MATRIX = [
    [ 1,  3,  5,  8, 20],
    [ 2,  7, 11, 13, 21],
    [ 4, 10, 14, 17, 22],
    [ 6, 12, 16, 19, 24],
    [ 9, 15, 18, 23, 25],
];
function skorKeLevel(s) {
    if (s <= 5)  return 'Sangat Rendah';
    if (s <= 10) return 'Rendah';
    if (s <= 15) return 'Sedang';
    if (s <= 20) return 'Tinggi';
    return 'Sangat Tinggi';
}
function emptyForm() {
    return {
        vulnerability_item_id:'', jenis_risiko:'', ancaman:'', kerawanan:'',
        kategori:'', dampak_detail:'', area_dampak:[], kontrol_saat_ini:'',
        rencana_aksi:'', inherent_dampak:'', inherent_kemungkinan:'',
        keputusan_penanganan:'', prioritas_risiko:'', opsi_penanganan:'',
        keluaran:'', target_jadwal:'', penanggung_jawab:'', risk_owner:'',
        ada_residual_risk:false, residual_dampak:'', residual_kemungkinan:'',
        residual_status:'', rencana_kontrol_tambahan:'',
    };
}
function rrEditor() {
    return {
        showModal:false, editMode:false, editItemId:null,
        form: emptyForm(),
        inherentSkor:null, inherentLevel:null,
        residualSkor:null, residualLevel:null,
        init() {},
        openAddModal() {
            this.editMode=false; this.editItemId=null;
            this.form=emptyForm();
            this.inherentSkor=null; this.inherentLevel=null;
            this.residualSkor=null; this.residualLevel=null;
            this.showModal=true;
        },
        openEditModalWithData(item) {
            this.editMode=true; this.editItemId=item.id;
            this.form = {
                vulnerability_item_id:    item.vulnerability_item_id||'',
                jenis_risiko:             item.jenis_risiko||'',
                ancaman:                  item.ancaman||'',
                kerawanan:                item.kerawanan||'',
                kategori:                 item.kategori||'',
                dampak_detail:            item.dampak_detail||'',
                area_dampak:              item.area_dampak||[],
                kontrol_saat_ini:         item.kontrol_saat_ini||'',
                rencana_aksi:             item.rencana_aksi||'',
                inherent_dampak:          item.inherent_dampak?.toString()||'',
                inherent_kemungkinan:     item.inherent_kemungkinan?.toString()||'',
                keputusan_penanganan:     item.keputusan_penanganan||'',
                prioritas_risiko:         item.prioritas_risiko||'',
                opsi_penanganan:          item.opsi_penanganan||'',
                keluaran:                 item.keluaran||'',
                target_jadwal:            item.target_jadwal||'',
                penanggung_jawab:         item.penanggung_jawab||'',
                risk_owner:               item.risk_owner||'',
                ada_residual_risk:        !!item.ada_residual_risk,
                residual_dampak:          item.residual_dampak?.toString()||'',
                residual_kemungkinan:     item.residual_kemungkinan?.toString()||'',
                residual_status:          item.residual_status||'',
                rencana_kontrol_tambahan: item.rencana_kontrol_tambahan||'',
            };
            this.hitungSkor(); this.hitungResidualSkor();
            this.showModal=true;
        },
        fillFromMaster(sel) {
            const d = sel.selectedOptions[0]?.dataset;
            if (!d || !sel.value) return;
            this.form.vulnerability_item_id = sel.value;
            this.form.ancaman          = d.ancaman||'';
            this.form.kerawanan        = d.kerawanan||'';
            this.form.dampak_detail    = d.dampak||'';
            this.form.kontrol_saat_ini = d.kontrol||'';
            this.form.rencana_aksi     = d.mitigasi||'';
            this.form.kategori         = d.kategori||'';
        },
        toggleAreaDampak(val) {
            if (!this.form.area_dampak) this.form.area_dampak=[];
            const i = this.form.area_dampak.indexOf(val);
            i>=0 ? this.form.area_dampak.splice(i,1) : this.form.area_dampak.push(val);
        },
        hitungSkor() {
            const d=parseInt(this.form.inherent_dampak), k=parseInt(this.form.inherent_kemungkinan);
            if(d>=1&&d<=5&&k>=1&&k<=5){
                this.inherentSkor=RISK_MATRIX[k-1][d-1];
                this.inherentLevel=skorKeLevel(this.inherentSkor);
            } else { this.inherentSkor=null; this.inherentLevel=null; }
        },
        hitungResidualSkor() {
            const d=parseInt(this.form.residual_dampak), k=parseInt(this.form.residual_kemungkinan);
            if(d>=1&&d<=5&&k>=1&&k<=5){
                this.residualSkor=RISK_MATRIX[k-1][d-1];
                this.residualLevel=skorKeLevel(this.residualSkor);
            } else { this.residualSkor=null; this.residualLevel=null; }
        },
    };
}
</script>
@endpush
