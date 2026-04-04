@extends('layouts.admin')

@section('title', $report->ticket_number)
@section('page-title', $report->ticket_number)
@section('page-subtitle', $report->title)

@section('content')

    <div class="max-w-3xl space-y-5">

        {{-- Hasil Validasi (tampil jika sudah ada) --}}
        @if ($report->validation_result)
            @php
                $vr = $report->validation_result;
                $vrc = \App\Models\Report::validationResultColor()[$vr] ?? 'gray';
                $vrl = \App\Models\Report::validationResultLabel()[$vr] ?? $vr;
            @endphp
            <div class="bg-white rounded-xl border border-{{ $vrc }}-200 shadow-sm p-5">
                <div class="flex items-center gap-4">

                    {{-- Icon per hasil --}}
                    @if ($vr === 'valid')
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                            @if ($report->hasCertificate())
                                {{-- Icon lencana untuk yang punya sertifikat --}}
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            @else
                                {{-- Centang hijau untuk valid tanpa sertifikat --}}
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                </svg>
                            @endif
                        </div>
                    @elseif($vr === 'invalid')
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />
                            </svg>
                        </div>
                    @elseif($vr === 'duplicate')
                        <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span
                                class="inline-flex px-3 py-1 rounded-full text-sm font-semibold
                                 bg-{{ $vrc }}-100 text-{{ $vrc }}-700">
                                {{ $vrl }}
                            </span>
                            @if ($report->hasCertificate())
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs
                                 font-semibold bg-indigo-100 text-indigo-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    Tersertifikasi
                                </span>
                            @endif
                        </div>

                        @if ($vr === 'valid' && !$report->hasCertificate())
                            <p class="text-xs text-gray-500 mt-1">Laporan valid. e-Sertifikat sedang diproses.</p>
                        @elseif($vr === 'invalid')
                            <p class="text-xs text-gray-500 mt-1">
                                Laporan tidak dapat diproses lebih lanjut.
                                @if ($report->closed_reason)
                                    {{ $report->closed_reason }}
                                @endif
                            </p>
                        @elseif($vr === 'duplicate')
                            <p class="text-xs text-gray-500 mt-1">
                                Laporan tercatat sebagai duplikat.
                                @if ($report->closed_reason)
                                    {{ $report->closed_reason }}
                                @endif
                            </p>
                        @endif
                    </div>

                    {{-- Tombol download sertifikat --}}
                    @if ($report->hasCertificate())
                        <a href="{{ route('public.certificate.download', $report) }}" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700
                      text-white rounded-lg text-xs font-semibold transition-colors shadow-sm shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Unduh e-Sertifikat
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Status Timeline --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-6">Status Laporan</h2>
            @php
                $flow = \App\Models\Report::statusFlow();
                $labels = \App\Models\Report::statusLabel();
                //$currentStep = array_search($report->status, $flow);
                // Jika tidak valid/duplikat, hapus step certificate dari flow
                if ($report->validation_result && $report->validation_result !== 'valid') {
                    $flow = array_values(array_filter($flow, fn($s) => $s !== 'certificate'));
                    $labels = array_filter($labels, fn($k) => $k !== 'certificate', ARRAY_FILTER_USE_KEY);
                }

                $currentStep = array_search($report->status, $flow);
                // Jika status closed dan certificate di-skip, pastikan currentStep benar
                if ($currentStep === false) {
                    $currentStep = count($flow) - 1;
                }
            @endphp

            <div class="relative">
                <div class="absolute top-4 left-4 right-4 h-0.5 bg-gray-200 z-0"></div>
                <div class="absolute top-4 left-4 h-0.5 bg-blue-500 z-0 transition-all duration-500"
                    style="width: calc({{ $currentStep }} / {{ count($flow) - 1 }} * (100% - 2rem))"></div>

                <div class="relative z-10 flex justify-between">
                    @foreach ($flow as $i => $step)
                        @php
                            $done = $i <= $currentStep;
                            $log = $report->statusLogs->firstWhere('status', $step);
                        @endphp
                        <div class="flex flex-col items-center gap-1.5" style="width: {{ 100 / count($flow) }}%">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all
                         {{ $done ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                                @if ($done && ($i < $currentStep || $report->status === 'closed'))
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                    </svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <p
                                class="text-xs text-center leading-tight {{ $done ? 'text-gray-700 font-medium' : 'text-gray-400' }}">
                                {{ $labels[$step] }}
                            </p>
                            <p class="text-xs text-center {{ $log ? 'text-blue-500' : 'text-gray-300' }}">
                                {{ $log ? $log->created_at->format('d M Y') : '—' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($report->admin_notes && $report->status !== 'closed')
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

                @if ($report->affected_system)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Sistem Terdampak</p>
                        <p class="text-sm text-blue-600 break-all">{{ $report->affected_system }}</p>
                    </div>
                @endif

                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deskripsi</p>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $report->description }}</p>
                </div>

                {{-- Dual Jenis Insiden --}}
                <div class="grid grid-cols-2 gap-3 pt-1">
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
                    @endphp
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jenis Insiden (Pelapor)
                        </p>
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            {{ $incidentLabel[$report->incident_type_reporter] ?? $report->incident_type_reporter }}
                        </span>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jenis Insiden
                            (Terverifikasi)</p>

                        @if ($report->incident_type_verified && $report->validation_result === 'valid')
                            <span
                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
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

                {{-- Dual Severity --}}
                <div class="grid grid-cols-2 gap-3 pt-1">
                    @php
                        $scR = \App\Models\Report::severityColor()[$report->severity_reporter] ?? 'gray';
                        $slR =
                            \App\Models\Report::severityLabel()[$report->severity_reporter] ??
                            $report->severity_reporter;
                    @endphp
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dampak (Pelapor)</p>
                        <span
                            class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $scR }}-100 text-{{ $scR }}-700">
                            {{ $slR }}
                        </span>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dampak (Terverifikasi)
                        </p>

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

                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Laporan</p>
                    <p class="text-sm text-gray-900">
                        {{ $report->created_at->format('d M Y, H:i') }} WITA
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
                            <path
                                d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
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

                {{-- Gambar --}}
                @foreach ($report->images as $img)
                    <a href="{{ route('public.attachments.show', $img) }}" target="_blank"
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
                    <a href="{{ route('public.attachments.show', $doc) }}" target="_blank"
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

        <div class="text-center pb-4">
            <a href="{{ route('public.reports.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Kembali ke Daftar Laporan
            </a>
        </div>

    </div>
@endsection
