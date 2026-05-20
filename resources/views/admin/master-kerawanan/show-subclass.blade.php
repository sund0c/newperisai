{{-- resources/views/admin/master-kerawanan/show-subclass.blade.php --}}
@extends('layouts.admin')

@section('title', 'Master Kerawanan — ' . $subklas->subklasifikasiaset)
@section('page-title', 'Master Kerawanan')
@section('page-subtitle', $klas->klasifikasiaset . ' › ' . $subklas->subklasifikasiaset)

@section('content')

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Breadcrumb --}}
    <nav class="flex mb-4 text-sm text-gray-500">
        <a href="{{ route('admin.master-kerawanan.index') }}" class="hover:text-blue-600">Master Kerawanan</a>
        <span class="mx-2">/</span>
        <a href="{{ route('admin.master-kerawanan.class.show', $klas->id) }}" class="hover:text-blue-600">{{ $klas->klasifikasiaset }}</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-medium">{{ $subklas->subklasifikasiaset }}</span>
    </nav>

    {{-- Info banner --}}
    <div class="mb-4 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm flex flex-wrap items-center gap-3">
        <svg class="w-4 h-4 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Kerawanan yang berlaku untuk <strong>{{ $subklas->subklasifikasiaset }}</strong>:</span>
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Global {{ $klas->klasifikasiaset }}
            @if ($activeGlobalSet)
                (v{{ $activeGlobalSet->versi }} · {{ $activeGlobalSet->items->count() }} item)
            @else
                <span class="text-amber-600">(belum ada)</span>
            @endif
        </span>
        <span class="text-blue-300">+</span>
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Spesifik {{ $subklas->subklasifikasiaset }}
            @if ($activeSet)
                (v{{ $activeSet->versi }} · {{ $activeSet->items->count() }} item tambahan)
            @else
                <span class="text-amber-600">(belum ada)</span>
            @endif
        </span>
    </div>

    <div class="space-y-4">

        {{-- PANEL GLOBAL (collapsible, read-only) --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <button onclick="togglePanel('panelGlobal','iconGlobal')"
                    class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition-colors">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Kerawanan Global — {{ $klas->klasifikasiaset }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Dikelola di halaman kelas aset · Read-only di sini
                        @if ($activeGlobalSet)
                            · v{{ $activeGlobalSet->versi }} · {{ $activeGlobalSet->items->count() }} item
                        @endif
                    </p>
                </div>
                <svg id="iconGlobal" class="w-4 h-4 text-gray-400 transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div id="panelGlobal" class="border-t border-gray-100">
                @if ($activeGlobalSet && $activeGlobalSet->items->count() > 0)
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($activeGlobalSet->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-2.5 w-8 text-xs text-gray-400 font-mono align-top">{{ $item->nomor_urut }}</td>
                                    <td class="px-6 py-2.5 text-sm text-gray-700">{{ $item->deskripsi }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-6 py-2.5 border-t border-gray-100 bg-gray-50">
                        <a href="{{ route('admin.master-kerawanan.class.show', $klas->id) }}"
                           class="text-xs font-medium text-blue-600 hover:underline">
                            Kelola kerawanan global di halaman {{ $klas->klasifikasiaset }} →
                        </a>
                    </div>
                @else
                    <div class="px-6 py-6 text-center text-sm text-gray-400">
                        Belum ada kerawanan global aktif untuk {{ $klas->klasifikasiaset }}.
                    </div>
                @endif
            </div>
        </div>

        {{-- PANEL SPESIFIK --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h2 class="text-sm font-semibold text-gray-800">Kerawanan Spesifik — {{ $subklas->subklasifikasiaset }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Kerawanan tambahan khusus sub-kelas ini
                        @if ($activeSet)
                            · <span class="text-green-600 font-medium">v{{ $activeSet->versi }} aktif</span>
                            · {{ $activeSet->items->count() }} item
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if ($allVersions->count() > 0)
                        <button onclick="document.getElementById('modalRiwayat').classList.remove('hidden')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-600
                                       bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Riwayat ({{ $allVersions->count() }})
                        </button>
                    @endif
                    @if (!$draftSet)
                        <form method="POST" action="{{ route('admin.master-kerawanan.set.create-version') }}">
                            @csrf
                            <input type="hidden" name="scope_type" value="subclass">
                            <input type="hidden" name="scope_id" value="{{ $subklas->id }}">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white
                                           bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Buat Versi Baru
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Draft banner --}}
            @if ($draftSet)
                <div class="px-6 py-3 bg-amber-50 border-b border-amber-200 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span class="text-sm font-medium text-amber-800">
                            Draft v{{ $draftSet->versi }} sedang dalam penyuntingan ({{ $draftSet->items->count() }} item)
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="document.getElementById('modalPublish').classList.remove('hidden');
                                        document.getElementById('formPublish').action='/admin/master-kerawanan/set/{{ $draftSet->id }}/publish'"
                                class="px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                            Publish
                        </button>
                        <form method="POST" action="{{ route('admin.master-kerawanan.set.delete-draft', $draftSet) }}"
                              onsubmit="return confirm('Hapus draft v{{ $draftSet->versi }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                Hapus Draft
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Tabel item spesifik --}}
            @php $displaySet = $draftSet ?? $activeSet; @endphp
            @if ($displaySet && $displaySet->items->count() > 0)
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:22%">Kerawanan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:18%">Ancaman Tipikal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:18%">Dampak Tipikal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:17%">Kontrol Tipikal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:17%">Mitigasi Tipikal</th>
                            @if ($draftSet && $displaySet->id === $draftSet->id)
                                <th class="px-4 py-3 w-20"></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($displaySet->items as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-xs text-gray-400 font-mono align-top">{{ $item->nomor_urut }}</td>
                                <td class="px-4 py-3 align-top">
                                    <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->deskripsi }}</p>
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if ($item->ancaman_tipikal)
                                        <p class="text-xs text-gray-600 leading-relaxed">{{ $item->ancaman_tipikal }}</p>
                                    @else <span class="text-xs text-gray-300">—</span> @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if ($item->dampak_tipikal)
                                        <p class="text-xs text-orange-600 leading-relaxed">{{ $item->dampak_tipikal }}</p>
                                    @else <span class="text-xs text-gray-300">—</span> @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if ($item->kontrol_tipikal)
                                        <p class="text-xs text-gray-600 leading-relaxed">{{ $item->kontrol_tipikal }}</p>
                                    @else <span class="text-xs text-gray-300">—</span> @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if ($item->mitigasi_tipikal)
                                        <p class="text-xs text-green-700 leading-relaxed">{{ $item->mitigasi_tipikal }}</p>
                                    @else <span class="text-xs text-gray-300">—</span> @endif
                                </td>
                                @if ($draftSet && $displaySet->id === $draftSet->id)
                                    <td class="px-4 py-3 text-right whitespace-nowrap align-top">
                                        <div class="flex items-center justify-end gap-1">
                                            <button onclick="openEditModal('{{ $item->id }}', {{ json_encode($item->deskripsi) }}, {{ json_encode($item->ancaman_tipikal) }}, {{ json_encode($item->dampak_tipikal) }}, {{ json_encode($item->kontrol_tipikal) }}, {{ json_encode($item->mitigasi_tipikal) }})"
                                                    class="px-2.5 py-1.5 text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                                                Edit
                                            </button>
                                            <form method="POST" action="{{ route('admin.master-kerawanan.item.destroy', $item) }}"
                                                  onsubmit="return confirm('Hapus item ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 text-xs font-semibold bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($draftSet)
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
                        <button onclick="document.getElementById('modalTambahItem').classList.remove('hidden')"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Item Kerawanan Spesifik
                        </button>
                    </div>
                @endif
            @elseif ($displaySet)
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-400">Draft v{{ $displaySet->versi }} belum memiliki item.</p>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-sm text-gray-400">Belum ada kerawanan spesifik untuk {{ $subklas->subklasifikasiaset }}.</p>
                    <p class="text-xs text-gray-400 mt-1">Klik "Buat Versi Baru" untuk memulai.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modals: Publish, Tambah Item, Edit Item, Riwayat --}}
    {{-- PUBLISH --}}
    <div id="modalPublish" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
         onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-lg">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Publish Versi Baru</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Setelah dipublish, versi ini tidak dapat diedit lagi</p>
                </div>
                <button onclick="document.getElementById('modalPublish').classList.add('hidden')"
                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="formPublish" method="POST" action="" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Catatan Perubahan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_perubahan" rows="3" required placeholder="Jelaskan perubahan yang dilakukan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalPublish').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">Publish Sekarang</button>
                </div>
            </form>
        </div>
    </div>

    {{-- TAMBAH ITEM --}}
    @if ($draftSet)
    <div id="modalTambahItem" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
         onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-lg">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800">Tambah Item Kerawanan Spesifik</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Draft v{{ $draftSet->versi }} — {{ $subklas->subklasifikasiaset }}</p>
                </div>
                <button onclick="document.getElementById('modalTambahItem').classList.add('hidden')"
                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.master-kerawanan.item.store', $draftSet) }}" method="POST" class="px-6 py-5 space-y-3">
                @csrf
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Kerawanan <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Ancaman Tipikal</label>
                    <textarea name="ancaman_tipikal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Dampak Tipikal</label>
                    <textarea name="dampak_tipikal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Kontrol Tipikal</label>
                    <textarea name="kontrol_tipikal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Mitigasi Tipikal</label>
                    <textarea name="mitigasi_tipikal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalTambahItem').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT ITEM --}}
    <div id="modalEditItem" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
         onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-lg">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Edit Item Kerawanan</h3>
                <button onclick="document.getElementById('modalEditItem').classList.add('hidden')"
                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="formEditItem" method="POST" action="" class="px-6 py-5 space-y-3">
                @csrf @method('PUT')
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Kerawanan <span class="text-red-500">*</span></label>
                    <textarea id="editDeskripsi" name="deskripsi" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Ancaman Tipikal</label>
                    <textarea id="editAncaman" name="ancaman_tipikal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Dampak Tipikal</label>
                    <textarea id="editDampak" name="dampak_tipikal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Kontrol Tipikal</label>
                    <textarea id="editKontrol" name="kontrol_tipikal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div><label class="block text-xs font-medium text-gray-600 mb-1">Mitigasi Tipikal</label>
                    <textarea id="editMitigasi" name="mitigasi_tipikal" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea></div>
                <div class="flex items-center justify-end gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modalEditItem').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- RIWAYAT --}}
    @if ($allVersions->count() > 0)
    <div id="modalRiwayat" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-4"
         onclick="if(event.target===this) this.classList.add('hidden')">
        <div class="bg-white rounded-2xl border border-gray-300 shadow-2xl w-full max-w-lg">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 rounded-t-2xl flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-800">Riwayat Versi — {{ $subklas->subklasifikasiaset }}</h3>
                <button onclick="document.getElementById('modalRiwayat').classList.add('hidden')"
                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4 space-y-2 max-h-80 overflow-y-auto">
                @foreach ($allVersions as $ver)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-sm font-bold {{ $ver->is_active ? 'text-green-700' : 'text-gray-500' }}">v{{ $ver->versi }}</span>
                            @if ($ver->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>Aktif
                                </span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $ver->published_at?->format('d M Y') }}</span>
                        </div>
                        <a href="{{ route('admin.master-kerawanan.set.history', $ver) }}"
                           class="px-3 py-1 text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">Lihat</a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <script>
        function togglePanel(panelId, iconId) {
            document.getElementById(panelId).classList.toggle('hidden');
            document.getElementById(iconId).classList.toggle('rotate-180');
        }
        function openEditModal(itemId, deskripsi, ancaman, dampak, kontrol, mitigasi) {
            document.getElementById('formEditItem').action = `/admin/master-kerawanan/items/${itemId}`;
            document.getElementById('editDeskripsi').value = deskripsi || '';
            document.getElementById('editAncaman').value   = ancaman || '';
            document.getElementById('editDampak').value    = dampak || '';
            document.getElementById('editKontrol').value   = kontrol || '';
            document.getElementById('editMitigasi').value  = mitigasi || '';
            document.getElementById('modalEditItem').classList.remove('hidden');
        }
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                ['modalPublish','modalTambahItem','modalEditItem','modalRiwayat']
                    .forEach(id => document.getElementById(id)?.classList.add('hidden'));
            }
        });
    </script>

@endsection
