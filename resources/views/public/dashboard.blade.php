@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('page-subtitle')
    Selamat datang, {{ auth()->user()->name }}
@endsection

@section('content')
@php
    $user        = auth()->user();
    $submitted   = $user->reports()->where('status', 'submitted')->count();
    $validated   = $user->reports()->where('status', 'validated')->count();
    $certificate = $user->reports()->where('status', 'certificate')->count();
    $closed      = $user->reports()->where('status', 'closed')->count();
    // $recent      = $user->reports()
    //                     ->whereDate('created_at', today())
    //                     ->latest()
    //                     ->get();
    $recent = $user->reports()
    ->with('latestStatusLog')
    ->latest()
    ->limit(10)
    ->get();

@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <a href="{{ route('public.reports.index', ['status' => 'submitted']) }}"
       class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Diterima sistem</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $submitted }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('public.reports.index', ['status' => 'validated']) }}"
       class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-purple-200 hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Sedang divalidasi</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $validated }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('public.reports.index', ['status' => 'certificate']) }}"
       class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-indigo-200 hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Menunggu e-Certifikate</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $certificate }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('public.reports.index', ['status' => 'closed']) }}"
       class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-green-200 hover:shadow-md transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Selesai</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $closed }}</p>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </a>

</div>

{{-- Laporan Masuk Hari Ini --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-900">10 Laporan Terakhir</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ today()->format('d M Y') }}</p>
        </div>
<div class="flex items-center gap-3">
    <a href="{{ route('public.reports.create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700
              text-white text-sm font-semibold rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Laporan
    </a>
    <a href="{{ route('public.reports.index') }}"
       class="text-sm text-blue-600 hover:underline">Lihat semua</a>
</div>
    </div>

    @if($recent->isEmpty())
    <div class="px-6 py-10 text-center">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-sm text-gray-500 mb-3">Belum ada laporan hari ini.</p>
        <a href="{{ route('public.reports.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                  text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Laporan Baru
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Nomor Tiket
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Judul
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Hasil
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                        Tanggal Laporan
                    </th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($recent as $report)
                @php
                    $stc      = \App\Models\Report::statusColor()[$report->status] ?? 'gray';
                    $isClosed = $report->status === 'closed';
                @endphp
                <tr class="hover:bg-gray-50 transition-colors {{ $isClosed ? 'opacity-60' : '' }}">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 whitespace-nowrap">
                        {{ $report->ticket_number }}
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900 truncate max-w-[200px]">
                            {{ $report->title }}
                        </p>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($report->validation_result)
                        @php
                            $vrc = \App\Models\Report::validationResultColor()[$report->validation_result] ?? 'gray';
                            $vrl = \App\Models\Report::validationResultLabel()[$report->validation_result] ?? '';
                        @endphp
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                         bg-{{ $vrc }}-100 text-{{ $vrc }}-700">
                                {{ $vrl }}
                            </span>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                     bg-{{ $stc }}-100 text-{{ $stc }}-700">
                            {{ $report->status_label }}
                        </span>
                         @if($report->status === 'closed' && $report->latestStatusLog)
 <span class="text-xs">
        {{ (int) $report->created_at->diffInDays($report->latestStatusLog->created_at) }} hari

@endif </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $report->created_at->format('d M Y') }} WITA
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('public.reports.show', $report) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs
                                  font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100
                                  transition-colors border border-blue-200">
                            Detail
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection
