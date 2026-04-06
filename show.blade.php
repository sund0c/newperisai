@extends('layouts.admin')

@section('title', $report->ticket_number)
@section('page-title', $report->ticket_number)
@section('page-subtitle', $report->title)

@section('content')

    {{-- Flash --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KOLOM KIRI: Detail + Attachments + CSIRT --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Detail Laporan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">Detail Laporan</h2>
                    {{-- Badge hasil validasi --}}
                    @if ($report->validation_result)
                        @php
                            $vrc = \App\Models\Report::validationResultColor()[$report->validation_result] ?? 'gray';
                        @endphp
                        <span
                            class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-{{ $vrc }}-100 text-{{ $vrc }}-700">
                            {{ $report->validation_result_label }}
                        </span>
                    @endif
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nomor Tiket</p>
                            <p class="text-sm font-mono text-gray-900">{{ $report->ticket_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pelapor</p>
                            <p class="text-sm text-gray-900">{{ $report->reporter?->name }}</p>
                            <p class="text-xs text-gray-400">{{ $report->reporter?->email }}</p>
                            <p class="text-xs text-gray-400">{{ $report->reporter?->organization }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Judul</p>
                        <p class="text-sm text-gray-900">{{ $report->title }}</p>
                    </div>

                    @if ($report->affected_system)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Sistem Terdampak
                            </p>
                            <p class="text-sm text-blue-600 break-all">{{ $report->affected_system }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deskripsi</p>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $report->description }}</p>
                    </div>

                    {{-- Dual Jenis Insiden --}}
                    <div class="grid grid-cols-2 gap-3">
                        @php
                            $incidentLabel = [
                                'data_breach' => 'Data Breach',
                                'web_defacement' => 'Web Defacement',
                                'ransomware' => 'Ransomware',
                                'phishing' => 'Phishing',
                                'malicious_software' => 'Malicious Software',
                                'exploit' => 'Exploit',
                                'account_hijacking' => 'Account Hijacking',
                                'advanced_persistence_threat' => 'Advanced Persistence Threat',
                                'peringatan_keamanan' => 'Peringatan Keamanan',
                                'lainnya' => $report->incident_type_other ?? 'Lain-lain',
                            ];
                            $incidentColors = [
                                'data_breach_pdp' => 'red',
                                'data_breach' => 'red',
                                'web_defacement' => 'orange',
                                'ransomware' => 'red',
                                'phishing' => 'yellow',
                                'malicious_software' => 'purple',
                                'exploit' => 'blue',
                                'account_hijacking' => 'orange',
                                'advanced_persistence_threat' => 'red',
                                'peringatan_keamanan' => 'blue',
                                'lainnya' => 'gray',
                            ];
                        @endphp
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jenis Insiden
                                (Pelapor)</p>
                            @php $icR = $incidentColors[$report->incident_type_reporter] ?? 'gray'; @endphp
                            <span
                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $icR }}-100 text-{{ $icR }}-700">
                                {{ $incidentLabel[$report->incident_type_reporter] ?? $report->incident_type_reporter }}
                            </span>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jenis Insiden
                                (Terverifikasi)</p>

                            @if ($report->incident_type_verified && $report->validation_result === 'valid')
                                @php $icV = $incidentColors[$report->incident_type_verified] ?? 'gray'; @endphp
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $icV }}-100 text-{{ $icV }}-700">
                                    {{ $incidentLabel[$report->incident_type_verified] ?? $report->incident_type_verified }}
                                </span>
                            @elseif($report->validation_result && $report->validation_result !== 'valid')
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                    N/A
                                </span>
                            @else
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                    Belum diverifikasi
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Dual Dampak/Severity --}}
                    <div class="grid grid-cols-2 gap-3">
                        @php
                            $scR = \App\Models\Report::severityColor()[$report->severity_reporter] ?? 'gray';
                            $slR =
                                \App\Models\Report::severityLabel()[$report->severity_reporter] ??
                                $report->severity_reporter;
                        @endphp
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dampak (Pelapor)
                            </p>
                            <span
                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $scR }}-100 text-{{ $scR }}-700">
                                {{ $slR }}
                            </span>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dampak
                                (Terverifikasi)</p>

                            @if ($report->severity_verified && $report->validation_result === 'valid')
                                @php
                                    $scV = \App\Models\Report::severityColor()[$report->severity_verified] ?? 'gray';
                                    $slV =
                                        \App\Models\Report::severityLabel()[$report->severity_verified] ??
                                        $report->severity_verified;
                                @endphp
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $scV }}-100 text-{{ $scV }}-700">
                                    {{ $slV }}
                                </span>
                            @elseif($report->validation_result && $report->validation_result !== 'valid')
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                    N/A
                                </span>
                            @else
                                <span
                                    class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                    Belum diverifikasi
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs text-gray-500">
                        <div>
                            <p class="font-semibold uppercase tracking-wider mb-1">Tanggal Laporan</p>
                            <p>{{ $report->created_at->format('d M Y') }} WITA</p>
                        </div>
                        @if ($report->validated_at)
                            <div>
                                <p class="font-semibold uppercase tracking-wider mb-1">Tanggal Validasi</p>
                                <p>{{ $report->validated_at->format('d M Y, H:i') }} WITA</p>
                            </div>
                        @endif
                        @if ($report->certificated_at)
                            <div>
                                <p class="font-semibold uppercase tracking-wider mb-1">Tanggal e-Sertifikat</p>
                                <p>{{ $report->certificated_at->format('d M Y, H:i') }} WITA</p>
                            </div>
                        @endif
                        @if ($report->closed_at)
                            <div>
                                <p class="font-semibold uppercase tracking-wider mb-1">Tanggal Selesai</p>
                                <p>{{ $report->closed_at->format('d M Y, H:i') }} WITA</p>
                            </div>
                        @endif
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
                                <path
                                    d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Link Video PoC
                            </p>
                            <a href="{{ $report->poc_video_url }}" target="_blank" rel="noopener noreferrer"
                                class="text-sm text-blue-600 hover:underline break-all">
                                {{ $report->poc_video_url }}
                            </a>
                        </div>
                    </div>

                    {{-- Gambar --}}
                    @foreach ($report->images as $img)
                        <a href="{{ route('support.attachments.show', $img) }}" target="_blank"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50
                          border border-transparent hover:border-blue-200 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-700 font-medium group-hover:text-blue-600">
                                        {{ $img->original_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $img->formatted_size }}</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endforeach

                    {{-- PDF --}}
                    @foreach ($report->documents as $doc)
                        <a href="{{ route('support.attachments.show', $doc) }}" target="_blank"
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50
                          border border-transparent hover:border-blue-200 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-700 font-medium group-hover:text-blue-600">
                                        {{ $doc->original_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $doc->formatted_size }}</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Riwayat Status --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-700">Riwayat Status</h2>
                </div>
                <div class="px-6 py-5">
                    <ol class="space-y-0">
                        @foreach ($report->statusLogs as $log)
                            <li class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <span class="w-3 h-3 rounded-full bg-blue-500 shrink-0 mt-1"></span>
                                    @if (!$loop->last)
                                        <span class="w-0.5 bg-gray-200 flex-1 my-1"></span>
                                    @endif
                                </div>
                                <div class="pb-5 flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ \App\Models\Report::statusLabel()[$log->status] ?? $log->status }}
                                    </p>
                                    @if ($log->notes)
                                        <p class="text-xs text-gray-600 mt-0.5 break-words">{{ trim($log->notes) }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $log->changer?->name ?? 'Sistem' }} ·
                                        {{ $log->created_at->format('d M Y, H:i') }} WITA
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            {{-- Proses Mitigasi CSIRT --}}
            @if ($report->csirtProcess)
                @php $cp = $report->csirtProcess; @endphp
                <div class="bg-white rounded-xl border border-indigo-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-indigo-100 bg-indigo-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <h2 class="text-sm font-semibold text-indigo-700">Proses Mitigasi CSIRT</h2>
                        </div>
                        <span
                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                bg-{{ $cp->status_color }}-100 text-{{ $cp->status_color }}-700">
                            {{ $cp->status_label }}
                        </span>
                    </div>
                    <div class="px-6 py-5">

                        {{-- ── MILESTONE BAR ── --}}
                        <div class="grid divide-x divide-gray-200 border border-gray-200 rounded-lg overflow-hidden mb-5"
                            style="grid-template-columns: repeat(4, minmax(0, 1fr))">
                            {{-- Dinotifikasi --}}
                            <div class="p-3 bg-gray-50">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Dinotifikasi
                                </p>
                                <p class="text-xs font-medium text-gray-700">
                                    {{ $cp->notified_at?->format('d M Y, H:i') ?? '—' }} WITA</p>
                            </div>

                            {{-- Mulai Proses --}}
                            <div class="p-3 {{ $cp->started_at ? 'bg-indigo-50' : 'bg-gray-50' }}">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mulai Proses
                                </p>
                                <p class="text-xs font-medium text-gray-700">
                                    {{ $cp->started_at?->format('d M Y, H:i') ?? '—' }}
                                    {{ $cp->started_at ? 'WITA' : '' }}</p>
                            </div>

                            {{-- Selesai --}}
                            <div class="p-3 {{ $cp->closed_at ? 'bg-green-50' : 'bg-gray-50' }}">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Selesai</p>
                                <p class="text-xs font-medium text-gray-700">
                                    {{ $cp->closed_at?->format('d M Y, H:i') ?? '—' }} {{ $cp->closed_at ? 'WITA' : '' }}
                                </p>
                            </div>

                            {{-- Laporan --}}
                            <div class="p-3 {{ $cp->mitigation_file ? 'bg-indigo-50' : 'bg-gray-50' }}">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Laporan</p>
                                @if ($cp->mitigation_file)
                                    <a href="{{ route('support.csirt.download', $cp) }}" target="_blank"
                                        class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:underline">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Unduh PDF
                                    </a>
                                @else
                                    <p class="text-xs text-gray-300">—</p>
                                @endif
                            </div>

                        </div>

                        {{-- ── DIVIDER ── --}}
                        @if ($cp->activityLogs && $cp->activityLogs->isNotEmpty())
                            <div class="border-t border-dashed border-gray-200 mb-5"></div>

                            {{-- ── ACTIVITY LOG (terbaru di atas) ── --}}
                            <ol class="space-y-0">
                                @foreach ($cp->activityLogs->sortByDesc('created_at') as $log)
                                    @php
                                        $typeMap = [
                                            'update' => [
                                                'label' => 'Update',
                                                'color' => '#3b82f6',
                                                'badge' => 'bg-blue-100 text-blue-700',
                                            ],
                                            'notification' => [
                                                'label' => 'Notifikasi',
                                                'color' => '#eab308',
                                                'badge' => 'bg-yellow-100 text-yellow-700',
                                            ],
                                            'coordination' => [
                                                'label' => 'Koordinasi',
                                                'color' => '#a855f7',
                                                'badge' => 'bg-purple-100 text-purple-700',
                                            ],
                                            'technical' => [
                                                'label' => 'Teknis',
                                                'color' => '#ef4444',
                                                'badge' => 'bg-red-100 text-red-700',
                                            ],
                                            'other' => [
                                                'label' => 'Lainnya',
                                                'color' => '#9ca3af',
                                                'badge' => 'bg-gray-100 text-gray-600',
                                            ],
                                        ];
                                        $cfg = $typeMap[$log->type] ?? $typeMap['other'];
                                    @endphp
                                    <li class="flex gap-4">
                                        <div class="flex flex-col items-center">
                                            <span class="w-3 h-3 rounded-full shrink-0 mt-1"
                                                style="background-color: {{ $cfg['color'] }}"></span>
                                            @if (!$loop->last)
                                                <span class="w-0.5 bg-gray-200 flex-1 my-1"></span>
                                            @endif
                                        </div>
                                        <div class="pb-5 flex-1 min-w-0">
                                            <div class="flex items-start gap-2">
                                                <p class="text-sm font-semibold text-gray-800 leading-snug flex-1 min-w-0">
                                                    {{ $log->title }}</p>
                                                <span
                                                    class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $cfg['badge'] }}">
                                                    {{ $cfg['label'] }}
                                                </span>
                                            </div>
                                            @if ($log->body)
                                                <p class="text-xs text-gray-600 mt-1 leading-relaxed break-words">
                                                    {{ trim($log->body) }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ $log->logger->name }} · {{ $log->created_at->format('d M Y, H:i') }}
                                                WITA
                                            </p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif

                    </div>
                </div>
            @endif

        </div>

        {{-- KOLOM KANAN: Panel Aksi --}}
        <div class="space-y-4">

            {{-- Tombol kembali --}}
            <a href="{{ route('support.reports.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar
            </a>

            {{-- ════ AKSI: VALIDASI (status = submitted) ════ --}}
            @if ($report->status === 'submitted')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Tindakan</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Tekan tombol di bawah untuk memulai proses validasi tiket ini.
                    </p>
                    <form method="POST" action="{{ route('support.reports.validate', $report) }}">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Mulai validasi tiket {{ $report->ticket_number }}?')"
                            class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold
                               rounded-lg text-sm transition-colors">
                            Mulai Validasi
                        </button>
                    </form>
                </div>
            @endif

            {{-- ════ AKSI: SET RESULT (status = validated) ════ --}}
            @if ($report->status === 'validated')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Hasil Validasi</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Periksa seluruh bukti PoC sebelum menentukan hasil validasi.
                        Catatan akan dikirim ke pelapor melalui email.
                    </p>

                    <form method="POST" action="{{ route('support.reports.result', $report) }}" id="resultForm">
                        @csrf
                        <input type="hidden" name="result" id="resultInput" value="">

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Catatan <span class="text-gray-400">(opsional — akan dikirim ke pelapor)</span>
                            </label>
                            <textarea name="notes" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                     focus:outline-none focus:ring-2 focus:ring-gray-400"
                                placeholder="Tambahkan catatan atau keterangan untuk pelapor..."></textarea>
                        </div>

                        {{-- Verifikasi Jenis Insiden --}}
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Verifikasi Jenis Insiden <span class="text-red-500">*</span>
                            </label>
                            <select name="incident_type_verified" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">-- Pilih Jenis Insiden --</option>
                                @php
                                    $incidentOptions = [
                                        'data_breach_pdp' => 'Data Pribadi Bocor (UU PDP)',
                                        'data_breach' => 'Data Breach (Non PDP)',
                                        'web_defacement' => 'Web Defacement',
                                        'ransomware' => 'Ransomware',
                                        'phishing' => 'Phishing',
                                        'malicious_software' => 'Malicious Software',
                                        'exploit' => 'Exploit',
                                        'account_hijacking' => 'Account Hijacking',
                                        'advanced_persistence_threat' => 'Advanced Persistence Threat',
                                        'peringatan_keamanan' => 'Peringatan Keamanan',
                                        'lainnya' => 'Lain-lain',
                                    ];
                                @endphp
                                @foreach ($incidentOptions as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ $value === $report->incident_type_reporter ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">
                                Pelapor menilai:
                                <strong>{{ $incidentOptions[$report->incident_type_reporter] ?? $report->incident_type_reporter }}</strong>
                                @if ($report->incident_type_reporter === 'lainnya' && $report->incident_type_other)
                                    ({{ $report->incident_type_other }})
                                @endif
                            </p>
                        </div>

                        {{-- Verifikasi Dampak --}}
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Verifikasi Dampak <span class="text-red-500">*</span>
                            </label>
                            <select name="severity_verified" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                                <option value="">-- Pilih Dampak --</option>
                                @foreach (\App\Models\Report::severityLabel() as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ $value === $report->severity_reporter ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">
                                Pelapor menilai:
                                <strong>{{ \App\Models\Report::severityLabel()[$report->severity_reporter] }}</strong>
                            </p>
                        </div>

                        <div class="space-y-2 pt-1">
                            {{-- VALID --}}
                            <button type="button"
                                onclick="submitResult('valid', 'Tandai sebagai VALID? Tim CSIRT akan dinotifikasi untuk mitigasi.')"
                                class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold
                       rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Valid
                            </button>

                            {{-- TIDAK VALID --}}
                            <button type="button"
                                onclick="submitResult('invalid', 'Tandai sebagai TIDAK VALID? Pelapor akan dikirim email pemberitahuan.')"
                                class="w-full py-2.5 bg-white hover:bg-red-50 text-red-600 font-semibold
                       rounded-lg text-sm transition-colors border border-red-300
                       flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tidak Valid
                            </button>

                            {{-- DUPLIKAT --}}
                            <button type="button"
                                onclick="submitResult('duplicate', 'Tandai sebagai DUPLIKAT? Pelapor akan dikirim email pemberitahuan.')"
                                class="w-full py-2.5 bg-white hover:bg-yellow-50 text-yellow-700 font-semibold
                       rounded-lg text-sm transition-colors border border-yellow-300
                       flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Duplikat
                            </button>
                        </div>
                    </form>
                </div>

                <script>
                    function submitResult(result, confirmMessage) {
                        if (!confirm(confirmMessage)) return;
                        document.getElementById('resultInput').value = result;
                        document.getElementById('resultForm').submit();
                    }
                </script>
            @endif

            {{-- ════ AKSI: UPLOAD E-CERTIFICATE (status = certificate) ════ --}}
            @if ($report->status === 'certificate')
                <div class="bg-white rounded-xl border border-green-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Upload e-Sertifikat</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Upload file PDF e-sertifikat untuk menutup tiket ini.
                        Pelapor akan dikirim email dengan link download sertifikat.
                    </p>
                    <form method="POST" action="{{ route('support.reports.certificate', $report) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                File e-Sertifikat (PDF, maks 5MB)
                            </label>
                            <input type="file" name="certificate" accept=".pdf" required
                                class="w-full text-xs text-gray-600 border border-gray-300 rounded-lg
                                  file:mr-3 file:py-2 file:px-3 file:border-0 file:text-xs
                                  file:font-medium file:bg-green-50 file:text-green-700
                                  hover:file:bg-green-100 cursor-pointer">
                        </div>
                        <button type="submit"
                            onclick="return confirm('Upload e-sertifikat dan tutup tiket ini? Pelapor akan dikirim email.')"
                            class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold
                               rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                            Upload & Tutup Tiket
                        </button>
                    </form>
                </div>
            @endif

            {{-- ════ STATUS CLOSED ════ --}}
            @if ($report->status === 'closed')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-700">Tiket Selesai
                            dalam waktu {{ (int) $report->created_at->diffInDays($report->closed_at) }} hari

                        </h3>
                    </div>
                    <p class="text-xs text-gray-500">
                        Tiket ini telah ditutup pada
                        {{ $report->closed_at?->format('d M Y, H:i') }} WITA.
                    </p>
                    @if ($report->certificate_file)
                        <a href="{{ route('support.certificate.download', $report) }}" target="_blank"
                            class="mt-3 w-full inline-flex items-center justify-center gap-2 py-2 px-3
                      bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs
                      font-medium border border-indigo-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Lihat e-Sertifikat
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>

@endsection
