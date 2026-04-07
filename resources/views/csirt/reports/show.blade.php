@extends('layouts.admin')

@section('title', $csirtProcess->report->ticket_number)
@section('page-title', $csirtProcess->report->ticket_number)
@section('page-subtitle', $csirtProcess->report->title)

@section('content')

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

    @php $report = $csirtProcess->report; @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KOLOM KIRI: Detail laporan --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info Laporan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">Detail Laporan</h2>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        Valid
                    </span>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nomor Tiket</p>
                            <p class="text-sm font-mono text-gray-900">{{ $report->ticket_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Laporan</p>
                            <p class="text-sm text-gray-900">{{ $report->created_at->format('d M Y, H:i') }} WITA</p>
                        </div>
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

                    @php
                        $sev = $report->effective_severity;
                        $sc = \App\Models\Report::severityColor()[$sev] ?? 'gray';
                        $sl = \App\Models\Report::severityLabel()[$sev] ?? $sev;
                        $incidentLabel = [
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
                            'lainnya' => $report->incident_type_other ?? 'Lain-lain',
                        ];
                    @endphp
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jenis Insiden</p>
                            <span
                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                {{ $incidentLabel[$report->incident_type_verified] ?? $report->incident_type_verified }}
                            </span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dampak</p>
                            <span
                                class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $sc }}-100 text-{{ $sc }}-700">
                                {{ $sl }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Link Video PoC</p>
                        <a href="{{ $report->poc_video_url }}" target="_blank" rel="noopener noreferrer"
                            class="text-sm text-blue-600 hover:underline break-all">
                            {{ $report->poc_video_url }}
                        </a>
                    </div>

                    @if ($report->validation_file)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Laporan Validasi</p>
                            <a href="{{ route('csirt.reports.validation-file', $report) }}" target="_blank"
                                class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Lihat PDF
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Attachments PoC --}}
            @if ($report->images->isNotEmpty() || $report->documents->isNotEmpty())
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-700">Bukti PoC</h2>
                    </div>
                    <div class="px-6 py-5 space-y-2">
                        @foreach ($report->images as $img)
                            <a href="{{ route('csirt.attachments.show', $img) }}" target="_blank"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-indigo-50
                          border border-transparent hover:border-indigo-200 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-700 font-medium group-hover:text-indigo-600">
                                            {{ $img->original_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $img->formatted_size }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endforeach

                        @foreach ($report->documents as $doc)
                            <a href="{{ route('csirt.attachments.show', $doc) }}" target="_blank"
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-indigo-50
                          border border-transparent hover:border-indigo-200 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-700 font-medium group-hover:text-indigo-600">
                                            {{ $doc->original_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $doc->formatted_size }}</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ════ TIMELINE PENANGANAN ════ --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">Timeline Penanganan</h2>
                    <span class="text-xs text-gray-400">{{ $csirtProcess->activityLogs->count() }} aktivitas</span>
                </div>
                <div class="px-6 py-5">

                    {{-- ── MILESTONE BAR ── --}}
                    <div class="grid divide-x divide-gray-200 border border-gray-200 rounded-lg overflow-hidden mb-5"
                        style="grid-template-columns: repeat(3, minmax(0, 1fr))">
                        <div class="p-3 bg-gray-50">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Dinotifikasi</p>
                            <p class="text-xs font-medium text-gray-700">
                                {{ $csirtProcess->notified_at?->format('d M Y, H:i') ?? '—' }}
                                {{ $csirtProcess->notified_at ? 'WITA' : '' }}</p>
                        </div>
                        <div class="p-3 {{ $csirtProcess->started_at ? 'bg-indigo-50' : 'bg-gray-50' }}">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mulai Proses</p>
                            <p class="text-xs font-medium text-gray-700">
                                {{ $csirtProcess->started_at?->format('d M Y, H:i') ?? '—' }}
                                {{ $csirtProcess->started_at ? 'WITA' : '' }}</p>
                        </div>
                        <div class="p-3 {{ $csirtProcess->closed_at ? 'bg-green-50' : 'bg-gray-50' }}">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Mitigasi Selesai
                            </p>
                            <p class="text-xs font-medium text-gray-700">
                                {{ $csirtProcess->closed_at?->format('d M Y, H:i') ?? '—' }}
                                {{ $csirtProcess->closed_at ? 'WITA' : '' }}</p>
                        </div>
                    </div>

                    {{-- ── DIVIDER ── --}}
                    @if ($csirtProcess->activityLogs && $csirtProcess->activityLogs->isNotEmpty())
                        <div class="border-t border-dashed border-gray-200 mb-5"></div>

                        {{-- ── ACTIVITY LOG (terbaru di atas) ── --}}
                        <ol class="space-y-0">
                            @foreach ($csirtProcess->activityLogs->sortByDesc('created_at') as $log)
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
                                            {{ $log->logger->name }} · {{ $log->created_at->format('d M Y, H:i') }} WITA
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-xs text-gray-400 italic">Belum ada aktivitas yang dicatat.</p>
                    @endif

                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: Panel Aksi --}}
        <div class="space-y-4">

            <a href="{{ route('csirt.reports.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar
            </a>

            {{-- Status card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status Mitigasi</p>
                <span
                    class="inline-flex px-3 py-1.5 rounded-full text-sm font-semibold
                         bg-{{ $csirtProcess->status_color }}-100 text-{{ $csirtProcess->status_color }}-700">
                    {{ $csirtProcess->status_label }}
                </span>
                @if ($csirtProcess->handler)
                    <p class="text-xs text-gray-500 mt-2">Ditangani: {{ $csirtProcess->handler->name }}</p>
                @endif
            </div>

            {{-- ════ AKSI: MULAI PROSES (status = notified) ════ --}}
            @if ($csirtProcess->status === 'notified')
                <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Tindakan</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Tekan tombol di bawah untuk memulai proses mitigasi.
                        Tanggal mulai akan dicatat otomatis.
                    </p>
                    <form method="POST" action="{{ route('csirt.reports.start', $csirtProcess) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Mulai proses mitigasi untuk tiket ini?')"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                               rounded-lg text-sm transition-colors">
                            Mulai Proses
                        </button>
                    </form>
                </div>
            @endif

            {{-- ════ AKSI: UPLOAD LAPORAN + SELESAI (status = in_progress) ════ --}}
            @if ($csirtProcess->status === 'in_progress')
                <div class="bg-white rounded-xl border border-indigo-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Selesaikan Mitigasi</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Upload laporan mitigasi PDF dan tambahkan catatan hasil proses.
                    </p>
                    <form method="POST" action="{{ route('csirt.reports.close', $csirtProcess) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Laporan Mitigasi (PDF, maks 10MB) <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="mitigation_file" accept=".pdf" required
                                    class="w-full text-xs text-gray-600 border border-gray-300 rounded-lg
                                      file:mr-3 file:py-2 file:px-3 file:border-0 file:text-xs
                                      file:font-medium file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Catatan Mitigasi
                                </label>
                                <textarea name="notes" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs
                                         focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Ringkasan tindakan mitigasi yang telah dilakukan..."></textarea>
                            </div>
                            <button type="submit"
                                onclick="return confirm('Selesaikan proses mitigasi? Tindakan ini tidak bisa dibatalkan.')"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                                   rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Selesai & Upload Laporan
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ════ FORM: CATAT AKTIVITAS (status = in_progress) ════ --}}
            @if ($csirtProcess->status === 'in_progress')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Catat Aktivitas</h3>
                    <p class="text-xs text-gray-500 mb-3">
                        Dokumentasikan setiap tindakan yang dilakukan selama proses mitigasi.
                    </p>
                    <form method="POST" action="{{ route('csirt.reports.activity', $csirtProcess) }}">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Aktivitas</label>
                                <select name="type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                    <option value="update">Update</option>
                                    <option value="notification">Notifikasi (surat/email ke aset)</option>
                                    <option value="coordination">Koordinasi</option>
                                    <option value="technical">Tindakan Teknis</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Judul Aktivitas <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" required maxlength="200"
                                    placeholder="Contoh: Kirim surat teguran ke pengelola aset"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Detail</label>
                                <textarea name="body" rows="3" maxlength="5000"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs
                               focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Penjelasan lebih lanjut (opsional)..."></textarea>
                            </div>

                            {{-- TOMBOL SIMPAN --}}
                            <button type="submit"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
           rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Simpan Aktivitas
                            </button>
                        </div>
                    </form>
                </div>
            @endif


            {{-- ════ STATUS CLOSED ════ --}}
            @if ($csirtProcess->status === 'closed')
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-700">Mitigasi Selesai</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">
                        Diselesaikan pada {{ $csirtProcess->closed_at?->format('d M Y, H:i') }} WITA.
                    </p>
                    @if ($csirtProcess->mitigation_file)
                        <a href="{{ route('csirt.reports.download', $csirtProcess) }}" target="_blank"
                            class="w-full inline-flex items-center justify-center gap-2 py-2 px-3
                      bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs
                      font-medium border border-indigo-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Lihat Laporan Mitigasi
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>

@endsection
