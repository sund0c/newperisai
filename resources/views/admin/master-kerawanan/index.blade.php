{{-- resources/views/admin/master-kerawanan/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Master Kerawanan')
@section('page-title', 'Master Kerawanan')
@section('page-subtitle', 'Kelola daftar kerawanan per kelas aset dan sub-kelas')

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
        Klik kelas aset untuk mengelola kerawanan global dan sub-kelasnya.
        Setiap scope memiliki versi independen — perubahan pada satu scope tidak mempengaruhi scope lain.
    </div>

    {{-- Grid Kelas Aset --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($assetClasses as $class)
            @php
                $activeSet = $class->activeVulnerabilitySet;
                $itemCount = $activeSet?->items->count() ?? 0;
                $subCount = $class->subclasses->count();
            @endphp
            <a href="{{ route('admin.master-kerawanan.class.show', $class) }}"
                class="group block bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 font-mono">
                            {{ $class->kode }}
                        </span>
                        @if ($activeSet)
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                v{{ $activeSet->versi }} aktif
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                Belum ada versi
                            </span>
                        @endif
                    </div>

                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 mb-3">
                        {{ $class->nama }}
                    </h3>

                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            {{ $itemCount }} kerawanan global
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            {{ $subCount }} sub-kelas
                        </span>
                    </div>
                </div>
                <div class="px-5 py-2.5 bg-gray-50 rounded-b-xl border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-400">Klik untuk kelola</span>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-500 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </a>
        @endforeach
    </div>

@endsection
