@extends('layouts.admin')

@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Selamat datang di panel administrasi CSIRT Bali')

@section('content')

    {{-- Maintenance Toggle --}}
    <div class="mb-6">
        <div
            class="bg-white rounded-xl shadow-sm border {{ $maintenanceActive ? 'border-amber-300' : 'border-gray-100' }} p-5">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                            {{ $maintenanceActive ? 'bg-amber-100' : 'bg-gray-100' }}">
                        <svg class="w-5 h-5 {{ $maintenanceActive ? 'text-amber-600' : 'text-gray-400' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Mode Maintenance</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if ($maintenanceActive)
                                <span class="text-amber-600 font-medium">Aktif</span>
                                — Pengguna publik tidak dapat mengakses sistem.
                            @else
                                <span class="text-green-600 font-medium">Tidak Aktif</span>
                                — Sistem berjalan normal untuk semua pengguna.
                            @endif
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.maintenance.toggle') }}">
                    @csrf
                    <button type="submit"
                        onclick="return confirm('{{ $maintenanceActive ? 'Nonaktifkan mode maintenance? Sistem akan kembali dapat diakses publik.' : 'Aktifkan mode maintenance? Pengguna publik tidak dapat mengakses sistem.' }}')"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors shrink-0
                               {{ $maintenanceActive
                                   ? 'bg-amber-500 hover:bg-amber-600 text-white'
                                   : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                        {{ $maintenanceActive ? 'Nonaktifkan' : 'Aktifkan Maintenance' }}
                    </button>
                </form>
            </div>

            @if ($maintenanceActive)
                <div class="mt-3 pt-3 border-t border-amber-200 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-xs text-amber-700">
                        Publik diarahkan ke halaman maintenance dengan info email <strong>csirt@baliprov.go.id</strong>.
                        Admin, Verifikator, dan Auditor tetap dapat login dan bekerja normal.
                    </p>
                </div>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total User</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tiket Masuk</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">0</p>
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Sedang Diproses</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">0</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">0</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
        <p class="text-sm text-gray-400 text-center py-8">Belum ada aktivitas.</p>
    </div>

@endsection
