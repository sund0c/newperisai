{{-- ── PASSWORD EXPIRY WARNING ────────────────────────────────────── --}}
@php
    $daysLeft = auth()->user()->daysUntilPasswordExpiry();
@endphp

@if ($daysLeft <= 14 && $daysLeft > 0)
    <a href="{{ route('password.change') }}"
        class="flex items-start gap-2.5 px-3 py-2.5 mb-3 rounded-lg bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 transition-colors">
        <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" />
        </svg>
        <div>
            <p class="text-xs font-semibold text-amber-400">Password Hampir Kedaluwarsa</p>
            <p class="text-xs text-amber-400/70">Sisa {{ $daysLeft }} hari — klik untuk ganti</p>
        </div>
    </a>
@elseif($daysLeft <= 0)
    <a href="{{ route('password.change') }}"
        class="flex items-start gap-2.5 px-3 py-2.5 mb-3 rounded-lg bg-red-500/10 border border-red-500/30 hover:bg-red-500/20 transition-colors">
        <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" />
        </svg>
        <div>
            <p class="text-xs font-semibold text-red-400">Password Kedaluwarsa!</p>
            <p class="text-xs text-red-400/70">Segera perbarui password Anda</p>
        </div>
    </a>
@endif
{{-- ─────────────────────────────────────────────────────────────────── --}}

@php
    $navClass = 'flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all duration-150';
    $activeClass = 'bg-blue-600 text-white font-medium';
    $inactiveClass = 'text-slate-300 hover:bg-slate-700 hover:text-white';
@endphp

<!-- Dashboard -->
<a href="{{ route('dashboard') }}"
    class="{{ $navClass }} {{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
    </svg>
    Dashboard
</a>

@role('admin')
    {{-- SYSTEM --}}
    <div class="pt-4 mt-4 border-t border-slate-700">
        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">System</p>
        <a href="{{ route('admin.opd.index') }}"
            class="{{ $navClass }} {{ request()->routeIs('admin.opd.*') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 21h18M3 10h18M3 7l9-4 9 4M4 10h1v11H4V10zm5 0h1v11H9V10zm5 0h1v11h-1V10zm5 0h1v11h-1V10z" />
            </svg>
            Perangkat Daerah
        </a>
        <a href="{{ route('admin.klasifikasi.index') }}"
            class="{{ $navClass }} {{ request()->routeIs('admin.klasifikasi.*') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A2 2 0 013 10V5a2 2 0 012-2z" />
            </svg>
            Klasifikasi Aset
        </a>
        <a href="{{ route('admin.tahunaktif.index') }}"
            class="{{ $navClass }} {{ request()->routeIs('admin.tahunaktif.*') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Tahun Aktif
        </a>
        <a href="{{ route('admin.periods.index') }}"
            class="{{ $navClass }} {{ request()->routeIs('admin.periods.*') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <rect x="3" y="4" width="18" height="17" rx="2" stroke-width="2" />
                <line x1="3" y1="9" x2="21" y2="9" stroke-width="2" />
                <line x1="8" y1="2" x2="8" y2="6" stroke-width="2" stroke-linecap="round" />
                <line x1="16" y1="2" x2="16" y2="6" stroke-width="2" stroke-linecap="round" />
                <text x="12" y="19" text-anchor="middle" font-size="7" font-weight="700" fill="currentColor"
                    stroke="none">25</text>
            </svg>
            Periode Waktu
        </a>
        <a href="{{ route('admin.users.index') }}"
            class="{{ $navClass }} {{ request()->routeIs('admin.users.*') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Manajemen User
        </a>
        <a href="{{ route('admin.master-se.index') }}" class="{{ $navClass }} {{ $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Master Kategorisasi SE
        </a>
    </div>

    {{-- ASET --}}
    <div class="pt-4 mt-4 border-t border-slate-700">
        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Aset</p>
        <a href="{{ route('admin.assets.index') }}"
            class="{{ $navClass }} {{ request()->routeIs('admin.assets.*') ? $activeClass : $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Aset Inventory
        </a>

        <a href="{{ route('admin.asset-criticality.index') }}" class="{{ $navClass }} {{ $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Kritikalitas Aset
        </a>
        <a href="{{ route('admin.asset-iiv.index') }}" class="{{ $navClass }} {{ $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Infra Informasi Vital
        </a>
        <a href="{{ route('admin.asset-se.index') }}" class="{{ $navClass }} {{ $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
            Kategorisasi SE
        </a>
    </div>

    {{-- PELINDUNGAN DATA PRIBADI --}}
    <div class="pt-4 mt-4 border-t border-slate-700">
        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pelindungan Data Pribadi</p>
        <a href="{{ route('admin.ropa.index') }}" class="{{ $navClass }} {{ $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            RoPA
        </a>
        <a href="{{ route('admin.dpia.index') }}" class="{{ $navClass }} {{ $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            DPIA
        </a>
    </div>

    {{-- AUDIT --}}
    <div class="pt-4 mt-4 border-t border-slate-700">
        <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Audit</p>
        <a href="{{ route('admin.users.index') }}" class="{{ $navClass }} {{ $inactiveClass }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Risk Register
        </a>
    </div>
@endrole
