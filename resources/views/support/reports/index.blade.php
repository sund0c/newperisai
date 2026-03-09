@extends('layouts.admin')

@section('title', 'Semua Tiket')
@section('page-title', 'Semua Tiket')
@section('page-subtitle', 'Daftar seluruh laporan kerentanan yang masuk')

@section('content')

    {{-- Filter & Search --}}
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <form method="GET" action="{{ route('support.reports.index') }}" class="flex flex-wrap items-center gap-2 flex-1">

            {{-- Search --}}
            <div class="relative min-w-[200px] flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nomor tiket atau judul..."
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                          focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Filter Status --}}
            <select name="status"
                class="px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none
                       focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">Semua Status</option>
                @foreach (\App\Models\Report::statusLabel() as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Severity --}}
            <select name="severity"
                class="px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none
                       focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="">Semua Dampak</option>
                @foreach (\App\Models\Report::severityLabel() as $value => $label)
                    <option value="{{ $value }}" {{ request('severity') === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm
                       font-medium transition-colors">
                Filter
            </button>

            @if (request()->hasAny(['search', 'status', 'severity']))
                <a href="{{ route('support.reports.index') }}"
                    class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-sm
                  font-medium transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel --}}
    @if ($reports->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                         M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <p class="text-gray-500 text-sm">Tidak ada tiket yang ditemukan.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[860px]">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Nomor Tiket</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Judul</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Pelapor</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Dampak</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Tanggal Masuk</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($reports as $report)
                            @php
                                $sev = $report->effective_severity;
                                $sc = \App\Models\Report::severityColor()[$sev] ?? 'gray';
                                $sl = \App\Models\Report::severityLabel()[$sev] ?? $sev;
                                $stc = \App\Models\Report::statusColor()[$report->status] ?? 'gray';
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
                                    {{-- Badge hasil validasi jika sudah ada --}}
                                    @if ($report->validation_result)
                                        @php
                                            $vrc =
                                                \App\Models\Report::validationResultColor()[
                                                    $report->validation_result
                                                ] ?? 'gray';
                                            $vrl =
                                                \App\Models\Report::validationResultLabel()[
                                                    $report->validation_result
                                                ] ?? '';
                                        @endphp
                                        <span
                                            class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded text-xs
                                     bg-{{ $vrc }}-100 text-{{ $vrc }}-700">
                                            {{ $vrl }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-sm text-gray-700">{{ $report->reporter?->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $report->reporter?->organization }}</p>
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
                                        {{ $report->status_label }}
                                    </span>
                                    {{-- Indikator CSIRT --}}
                                    @if ($report->csirtProcess)
                                        <span
                                            class="ml-1 inline-flex px-2 py-0.5 rounded text-xs
                                     bg-indigo-100 text-indigo-600">
                                            CSIRT: {{ $report->csirtProcess->status_label }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $report->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('support.reports.show', $report) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs
                                  font-semibold bg-blue-50 text-blue-600 hover:bg-blue-100
                                  transition-colors border border-blue-200">
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

            {{-- Pagination --}}
            @if ($reports->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between flex-wrap gap-2">
                    <p class="text-xs text-gray-500">
                        Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }}
                        dari {{ $reports->total() }} tiket
                    </p>
                    {{ $reports->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    @endif

@endsection
