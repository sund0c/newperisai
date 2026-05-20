{{-- resources/views/admin/risk-register/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Risk Register')
@section('page-title', 'Risk Register')
@section('page-subtitle', 'Daftar Risk Register Aset Informasi per OPD')

@section('content')

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('info'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('info') }}
    </div>
    @endif

    {{-- Toolbar --}}
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode RR atau nama aset..."
                   class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <select name="status" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="draft" @selected(request('status')==='draft')>Draft</option>
                <option value="final" @selected(request('status')==='final')>Final</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-gray-700 text-white text-sm rounded-lg hover:bg-gray-800 transition">Cari</button>
            @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.risk-register.index') }}" class="px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50 transition">Reset</a>
            @endif
        </form>

        {{-- role check handled by middleware --}}
    @if(true)
        <a href="{{ route('admin.risk-register.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Risk Register
        </a>
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Kode RR</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Aset</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">OPD</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">Versi</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">Item</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Dibuat</th>
                        <th class="px-4 py-3 text-center font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($registers as $rr)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-semibold text-blue-700">{{ $rr->kode_rr }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $rr->asset->nama_aset ?? '-' }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $rr->asset->kode_aset ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-[180px]">
                            <div class="truncate">{{ $rr->opd->namaopd ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 text-gray-700 text-xs font-bold rounded-full">
                                v{{ $rr->versi }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($rr->status === 'final')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Final
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-7 h-7 bg-blue-50 text-blue-700 text-xs font-bold rounded-full">
                                {{ $rr->items()->count() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $rr->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.risk-register.show', $rr) }}"
                                   class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition" title="Lihat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if($rr->isDraft())
                                <a href="{{ route('admin.risk-register.edit', $rr) }}"
                                   class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm font-medium">Belum ada Risk Register</p>
                            <p class="text-xs mt-1">Klik "Buat Risk Register" untuk memulai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($registers->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $registers->links() }}
        </div>
        @endif
    </div>

@endsection
