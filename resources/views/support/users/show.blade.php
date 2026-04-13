@extends('layouts.admin')

@section('page-title', $user->name)
@section('page-subtitle', $user->organization)

@section('content')

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Kolom Kiri: Info User ───────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Info User --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi User</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Nama</p>
                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="text-gray-700">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Instansi</p>
                        <p class="text-gray-700">{{ $user->organization }}</p>
                    </div>
                    @if ($user->phone)
                        <div>
                            <p class="text-xs text-gray-500">Telepon</p>
                            <p class="text-gray-700">{{ $user->phone }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500">Terdaftar</p>
                        <p class="text-gray-700">{{ $user->created_at->format('d M Y, H:i') }} WITA</p>
                        <p class="text-xs text-gray-400">{{ $user->created_at->utc()->format('d M Y, H:i') }} UTC</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        @if ($user->is_active)
                            <span
                                class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                        @else
                            <span
                                class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Statistik</h3>
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <p class="text-xl font-bold text-blue-700">{{ $totalTickets }}</p>
                        <p class="text-xs text-blue-600 mt-0.5">Total Laporan</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <p class="text-xl font-bold text-indigo-700">{{ $totalHistorical }}</p>
                        <p class="text-xs text-indigo-600 mt-0.5">Historis</p>
                    </div>
                </div>
            </div>

            {{-- Aksi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Aksi</h3>
                <a href="{{ route('support.users.historical.create', $user) }}"
               class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5
                      bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                      rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Input Tiket Historis
            </a>
                <form method="POST" action="{{ route('support.users.reset-password', $user) }}"
                    onsubmit="return confirm('Reset password user ini? Password baru akan dikirim ke email mereka.')">
                    @csrf
                    <button type="submit"
                        class="w-full mt-2 inline-flex items-center justify-center gap-2 px-4 py-2.5
                   bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold
                   rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Reset Password
                    </button>
                </form>
            </div>

            <a href="{{ route('support.users.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar
            </a>
        </div>

        {{-- ── Kolom Kanan: Riwayat Tiket ─────────────────────────────── --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Riwayat Tiket Laporan</h3>
                </div>

                @if ($reports->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-8">Belum ada tiket.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($reports as $report)
                            @php
                                $stc = \App\Models\Report::statusColor()[$report->status] ?? 'gray';
                                $vrc = $report->validation_result
                                    ? \App\Models\Report::validationResultColor()[$report->validation_result] ?? 'gray'
                                    : null;
                                $vrl = $report->validation_result
                                    ? \App\Models\Report::validationResultLabel()[$report->validation_result] ?? ''
                                    : null;
                            @endphp
                            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="font-mono text-xs text-gray-500">{{ $report->ticket_number }}</p>
                                        @if ($report->is_historical)
                                            <span
                                                class="inline-flex px-1.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700">
                                                Historis
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm font-medium text-gray-800 truncate max-w-[280px] mt-0.5">
                                        {{ $report->title }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $report->created_at->format('d M Y') }} WITA
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-3">
                                    @if ($vrc)
                                        <span
                                            class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                     bg-{{ $vrc }}-100 text-{{ $vrc }}-700">
                                            {{ $vrl }}
                                        </span>
                                    @endif
                                    <span
                                        class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                     bg-{{ $stc }}-100 text-{{ $stc }}-700">
                                        {{ $report->status_label }}
                                    </span>
                                    @if (!$report->is_historical)
                                        <a href="{{ route('support.reports.show', $report) }}"
                                            class="text-xs text-blue-600 hover:underline shrink-0">Detail</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($reports->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100">
                            {{ $reports->links() }}
                        </div>
                    @endif

                @endif
            </div>
        </div>

    </div>

@endsection
