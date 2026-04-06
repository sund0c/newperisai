@extends('layouts.admin')

@section('title', 'Tiket Penanganan Data Pribadi')
@section('page-title', 'Tiket Penanganan Data Pribadi')
@section('page-subtitle', 'Daftar laporan data breach yang memerlukan penanganan UU PDP')

@section('content')

    {{-- Filter & Search --}}
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('dpo.reports.index') }}" class="flex flex-wrap items-center gap-2 flex-1">

            <div class="relative min-w-[200px] flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nomor tiket atau judul..."
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <select name="status"
                class="px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none
                       focus:ring-2 focus:ring-indigo-500 bg-white">
                <option value="">Semua Status</option>
                @foreach (\App\Models\DpoProcess::statusLabel() as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg
                       text-sm font-medium transition-colors">
                Filter
            </button>

            @if (request()->hasAny(['search', 'status']))
                <a href="{{ route('dpo.reports.index') }}"
                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg
                  text-sm font-medium transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($processes->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <p class="text-gray-500 text-sm">Tidak ada tiket penanganan data pribadi yang ditemukan.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[760px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Nomor Tiket</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Judul</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Dampak</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Status Penanganan</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Dinotifikasi</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Ditangani oleh</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($processes as $process)
                            @php
                                $report = $process->report;
                                $sev = $report->effective_severity;
                                $sc = \App\Models\Report::severityColor()[$sev] ?? 'gray';
                                $sl = \App\Models\Report::severityLabel()[$sev] ?? $sev;
                                $stc = $process->status_color;
                                $isClosed = $process->status === 'closed';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isClosed ? 'opacity-60' : '' }}">
                                <td class="px-4 py-3 font-mono text-xs text-gray-600 whitespace-nowrap">
                                    {{ $report->ticket_number }}
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900 truncate max-w-[200px]">{{ $report->title }}</p>
                                    @if ($report->affected_system)
                                        <p class="text-xs text-gray-400 truncate max-w-[200px]">
                                            {{ $report->affected_system }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                         bg-{{ $sc }}-100 text-{{ $sc }}-700">
                                        {{ $sl }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                                         bg-{{ $stc }}-100 text-{{ $stc }}-700">
                                        {{ $process->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $process->notified_at?->format('d M Y, H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600 whitespace-nowrap">
                                    {{ $process->handler?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('dpo.reports.show', $process) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs
                                          font-semibold bg-indigo-50 text-indigo-600 hover:bg-indigo-100
                                          transition-colors border border-indigo-200">
                                        Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($processes->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
                    <p class="text-xs text-gray-500">
                        Menampilkan {{ $processes->firstItem() }}–{{ $processes->lastItem() }}
                        dari {{ $processes->total() }} tiket
                    </p>
                    {{ $processes->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    @endif

@endsection
