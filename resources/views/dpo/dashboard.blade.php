@extends('layouts.admin')

@section('page-title', 'Dashboard DPO')
@section('page-subtitle', 'Panel Penanganan Data Pribadi (UU PDP)')

@section('content')
    @php
        $notified = \App\Models\DpoProcess::where('status', 'notified')->count();
        $inProgress = \App\Models\DpoProcess::where('status', 'in_progress')->count();
        $closed = \App\Models\DpoProcess::where('status', 'closed')->count();
        $total = \App\Models\DpoProcess::count();
        $recent = \App\Models\DpoProcess::with(['report', 'handler'])
            ->latest()
            ->limit(8)
            ->get();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Tiket</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $total }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <a href="{{ route('dpo.reports.index', ['status' => 'notified']) }}"
            class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-yellow-200 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Menunggu Proses</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $notified }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dpo.reports.index', ['status' => 'in_progress']) }}"
            class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sedang Diproses</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $inProgress }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ route('dpo.reports.index', ['status' => 'closed']) }}"
            class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:border-green-200 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Penanganan Selesai</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $closed }}</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </a>

    </div>

    {{-- Tiket DPO Terbaru --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Tiket Terbaru</h3>
            <a href="{{ route('dpo.reports.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua</a>
        </div>

        @if ($recent->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">Belum ada tiket penanganan data pribadi.</p>
        @else
            <div class="divide-y divide-gray-50">
                @foreach ($recent as $process)
                    @php
                        $sev = $process->report->effective_severity;
                        $sc = \App\Models\Report::severityColor()[$sev] ?? 'gray';
                        $stc = $process->status_color;
                    @endphp
                    <a href="{{ route('dpo.reports.show', $process) }}"
                        class="flex items-center justify-between px-6 py-3.5 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4 min-w-0">
                            <span class="font-mono text-xs text-gray-500 shrink-0">
                                {{ $process->report->ticket_number }}
                            </span>
                            <p class="text-sm font-medium text-gray-800 truncate">
                                {{ $process->report->title }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 ml-4">
                            <span
                                class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $sc }}-100 text-{{ $sc }}-700">
                                {{ \App\Models\Report::severityLabel()[$sev] ?? $sev }}
                            </span>
                            <span
                                class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $stc }}-100 text-{{ $stc }}-700">
                                {{ $process->status_label }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
