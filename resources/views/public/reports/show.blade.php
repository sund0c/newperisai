@extends('layouts.admin')

@section('title', $report->ticket_number)
@section('page-title', $report->ticket_number)
@section('page-subtitle', $report->title)

@section('content')

<div class="max-w-3xl space-y-5">

    {{-- Status Timeline --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-6">Status Laporan</h2>
        @php
            $flow        = \App\Models\Report::statusFlow();
            $labels      = \App\Models\Report::statusLabel();
            $currentStep = array_search($report->status, $flow);
        @endphp

        <div class="relative">
            <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-200 z-0"></div>
            <div class="absolute top-4 left-4 h-0.5 bg-blue-500 z-0 transition-all duration-500"
                 style="width: calc({{ $currentStep }} / {{ count($flow) - 1 }} * (100% - 2rem))"></div>

            <div class="relative z-10 flex justify-between">
                @foreach($flow as $i => $step)
                @php
                    $done = $i <= $currentStep;
                    $log  = $report->statusLogs->firstWhere('status', $step);
                @endphp
                <div class="flex flex-col items-center gap-1.5" style="width: {{ 100 / count($flow) }}%">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all
                         {{ $done ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                        @if($done && $i < $currentStep)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                            </svg>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>
                    <p class="text-xs text-center leading-tight {{ $done ? 'text-gray-700 font-medium' : 'text-gray-400' }}">
                        {{ $labels[$step] }}
                    </p>
                    <p class="text-xs text-center {{ $log ? 'text-blue-500' : 'text-gray-300' }}">
                        {{ $log ? $log->created_at->format('d M Y') : '—' }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>

        @if($report->admin_notes)
        <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-xs font-semibold text-blue-700 mb-1">Catatan dari Tim CSIRT:</p>
            <p class="text-sm text-blue-800">{{ $report->admin_notes }}</p>
        </div>
        @endif
    </div>

    {{-- Detail Laporan --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Detail Laporan</h2>
        </div>

        <div class="px-6 py-5 space-y-4">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Judul</p>
                <p class="text-sm text-gray-900">{{ $report->title }}</p>
            </div>

            @if($report->affected_system)
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Sistem Terdampak</p>
                <p class="text-sm text-blue-600 break-all">{{ $report->affected_system }}</p>
            </div>
            @endif

            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deskripsi</p>
                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $report->description }}</p>
            </div>

            {{-- Dual Severity --}}
            <div class="grid grid-cols-2 gap-3 pt-1">
                @php
                    $scR = \App\Models\Report::severityColor()[$report->severity_reporter] ?? 'gray';
                    $slR = \App\Models\Report::severityLabel()[$report->severity_reporter] ?? $report->severity_reporter;
                @endphp
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dampak (Pelapor)</p>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $scR }}-100 text-{{ $scR }}-700">
                        {{ $slR }}
                    </span>
                </div>

                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dampak (Terverifikasi)</p>
                    @if($report->severity_verified)
                    @php
                        $scV = \App\Models\Report::severityColor()[$report->severity_verified] ?? 'gray';
                        $slV = \App\Models\Report::severityLabel()[$report->severity_verified] ?? $report->severity_verified;
                    @endphp
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $scV }}-100 text-{{ $scV }}-700">
                        {{ $slV }}
                    </span>
                    @else
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                        Belum diverifikasi
                    </span>
                    @endif
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Laporan</p>
                <p class="text-sm text-gray-900">
    {{ $report->created_at->format('d M Y, H:i') }} WITA
    <span class="text-gray-400 mx-1">|</span>
    {{ $report->created_at->utc()->format('d M Y, H:i') }} UTC (UTC+8)
</p>
            </div>
        </div>
    </div>

    {{-- PoC Attachments --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">Proof of Concept (PoC)</h2>
        </div>
        <div class="px-6 py-5 space-y-3">

            {{-- Video --}}
            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Link Video PoC</p>
                    <a href="{{ $report->poc_video_url }}" target="_blank" rel="noopener noreferrer"
                       class="text-sm text-blue-600 hover:underline break-all">
                        {{ $report->poc_video_url }}
                    </a>
                </div>
            </div>

            {{-- Gambar -- tampil nama file, klik buka di tab baru --}}
            @if($report->images->isNotEmpty())
            @foreach($report->images as $img)
            <a href="{{ route('public.attachments.show', $img) }}" target="_blank"
               class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 font-medium group-hover:text-blue-600 transition-colors">{{ $img->original_name }}</p>
                        <p class="text-xs text-gray-400">{{ $img->formatted_size }}</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
            @endforeach
            @endif

            {{-- PDF --}}
            @if($report->documents->isNotEmpty())
            @foreach($report->documents as $doc)
            <a href="{{ route('public.attachments.show', $doc) }}" target="_blank"
               class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50 border border-transparent hover:border-blue-200 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 font-medium group-hover:text-blue-600 transition-colors">{{ $doc->original_name }}</p>
                        <p class="text-xs text-gray-400">{{ $doc->formatted_size }}</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
            @endforeach
            @endif

        </div>
    </div>

    <div class="text-center pb-4">
        <a href="{{ route('public.reports.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Kembali ke Daftar Laporan
        </a>
    </div>

</div>
@endsection
