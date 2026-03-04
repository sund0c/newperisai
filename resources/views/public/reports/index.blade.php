@extends('layouts.admin')

@section('title', 'Laporan Saya')
@section('page-title', 'Laporan Saya')
@section('page-subtitle', 'Daftar laporan keamanan yang telah Anda kirimkan')

@section('content')

{{-- Toolbar --}}
<div class="mb-4 flex items-center justify-between gap-3">
    <form method="GET" action="{{ route('public.reports.index') }}" class="flex items-center gap-2 flex-1 max-w-md">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nomor tiket atau judul..."
                   class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors">
            Cari
        </button>
        @if(request('search'))
        <a href="{{ route('public.reports.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-500 rounded-lg text-sm font-medium transition-colors">
            Reset
        </a>
        @endif
    </form>

    <a href="{{ route('public.reports.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm whitespace-nowrap">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Laporan Baru
    </a>
</div>

@if($reports->isEmpty())
<div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
    </div>
    @if(request('search'))
    <p class="text-gray-500 text-sm">Tidak ada laporan yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>".</p>
    <a href="{{ route('public.reports.index') }}" class="text-blue-600 text-sm hover:underline mt-2 inline-block">Lihat semua laporan</a>
    @else
    <p class="text-gray-500 text-sm">Belum ada laporan. Klik tombol di atas untuk membuat laporan baru.</p>
    @endif
</div>
@else
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Nomor Tiket</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Dampak</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal Laporan</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($reports as $report)
                @php
                    $severityLabels = \App\Models\Report::severityLabel();
                    $severityColors = \App\Models\Report::severityColor();
                    $statusColors   = \App\Models\Report::statusColor();
                    $sev       = $report->effective_severity;
                    $sc        = $severityColors[$sev] ?? 'gray';
                    $stc       = $statusColors[$report->status] ?? 'gray';
                    $latestLog = $report->latestStatusLog;
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-gray-600 whitespace-nowrap">
                        {{ $report->ticket_number }}
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900 truncate max-w-[180px]">{{ $report->title }}</p>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                            bg-{{ $sc }}-100 text-{{ $sc }}-700">
                            {{ $severityLabels[$sev] ?? strtoupper($sev) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                            bg-{{ $stc }}-100 text-{{ $stc }}-700">
                            {{ $report->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $latestLog ? $latestLog->created_at->format('d M Y, H:i') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $report->created_at->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('public.reports.show', $report) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors border border-blue-200">
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

    @if($reports->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">
            Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }} laporan
        </p>
        {{ $reports->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endif

@endsection
