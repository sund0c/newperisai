{{-- resources/views/admin/master-kerawanan/show-version.blade.php --}}
@extends('layouts.admin')

@section('title', 'Riwayat Versi — v' . $set->versi)
@section('page-title', 'Master Kerawanan')
@section('page-subtitle', 'Riwayat Versi — ' . $set->scopeName)

@section('content')

    {{-- Breadcrumb --}}
    <nav class="flex mb-4 text-sm text-gray-500">
        <a href="{{ route('admin.master-kerawanan.index') }}" class="hover:text-blue-600">Master Kerawanan</a>
        <span class="mx-2">/</span>
        @if ($set->scope_type === 'global_class')
            <a href="{{ route('admin.master-kerawanan.class.show', $scope->id) }}" class="hover:text-blue-600">{{ $scope->klasifikasiaset }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">Global v{{ $set->versi }}</span>
        @else
            <a href="{{ route('admin.master-kerawanan.class.show', $scopeParent->id) }}" class="hover:text-blue-600">{{ $scopeParent->klasifikasiaset }}</a>
            <span class="mx-2">/</span>
            <a href="{{ route('admin.master-kerawanan.subclass.show', [$scopeParent->id, $scope->id]) }}" class="hover:text-blue-600">{{ $scope->subklasifikasiaset }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">v{{ $set->versi }}</span>
        @endif
    </nav>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
            <div class="flex items-start justify-between flex-wrap gap-3">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <h2 class="text-lg font-bold text-gray-900 font-mono">v{{ $set->versi }}</h2>
                        @if ($set->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Tidak Aktif
                            </span>
                        @endif
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">READ-ONLY</span>
                    </div>
                    <p class="text-sm text-gray-500">{{ $set->scopeName }}</p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Dibuat oleh</p>
                    <p class="text-sm text-gray-700">{{ $set->createdBy?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Dipublish oleh</p>
                    <p class="text-sm text-gray-700">{{ $set->publishedBy?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Tanggal Publish</p>
                    <p class="text-sm text-gray-700">{{ $set->published_at?->translatedFormat('d M Y, H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5">Jumlah Item</p>
                    <p class="text-sm text-gray-700">{{ $set->items->count() }} kerawanan</p>
                </div>
            </div>

            @if ($set->catatan_perubahan)
                <div class="mt-4 px-4 py-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700 flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div><span class="font-semibold">Catatan Perubahan:</span> {{ $set->catatan_perubahan }}</div>
                </div>
            @endif
        </div>

        {{-- Tabel --}}
        @if ($set->items->count() > 0)
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider w-8">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:22%">Kerawanan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:18%">Ancaman Tipikal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:18%">Dampak Tipikal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:17%">Kontrol Tipikal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider" style="width:17%">Mitigasi Tipikal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($set->items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4 text-xs text-gray-400 font-mono align-top">{{ $item->nomor_urut }}</td>
                            <td class="px-4 py-4 align-top">
                                <p class="text-sm font-medium text-gray-800 leading-snug">{{ $item->deskripsi }}</p>
                            </td>
                            <td class="px-4 py-4 align-top">
                                @if ($item->ancaman_tipikal)
                                    <p class="text-xs text-gray-600 leading-relaxed">{{ $item->ancaman_tipikal }}</p>
                                @else <span class="text-xs text-gray-300">—</span> @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                @if ($item->dampak_tipikal)
                                    <p class="text-xs text-orange-600 leading-relaxed">{{ $item->dampak_tipikal }}</p>
                                @else <span class="text-xs text-gray-300">—</span> @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                @if ($item->kontrol_tipikal)
                                    <p class="text-xs text-gray-600 leading-relaxed">{{ $item->kontrol_tipikal }}</p>
                                @else <span class="text-xs text-gray-300">—</span> @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                @if ($item->mitigasi_tipikal)
                                    <p class="text-xs text-green-700 leading-relaxed">{{ $item->mitigasi_tipikal }}</p>
                                @else <span class="text-xs text-gray-300">—</span> @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-gray-400">Versi ini tidak memiliki item kerawanan.</p>
            </div>
        @endif

    </div>

@endsection
