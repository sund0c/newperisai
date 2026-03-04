{{-- ── PASSWORD EXPIRY WARNING ────────────────────────────────────── --}}
@php
    $daysLeft = auth()->user()->daysUntilPasswordExpiry();
@endphp

@if($daysLeft <= 14 && $daysLeft > 0)
<a href="{{ route('password.change') }}"
   class="flex items-start gap-2.5 px-3 py-2.5 mb-3 rounded-lg bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 transition-colors">
    <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
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
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
    </svg>
    <div>
        <p class="text-xs font-semibold text-red-400">Password Kedaluwarsa!</p>
        <p class="text-xs text-red-400/70">Segera perbarui password Anda</p>
    </div>
</a>
@endif
{{-- ─────────────────────────────────────────────────────────────────── --}}


@php
    $currentRoute = request()->routeIs('admin.*') ? 'admin' : (request()->routeIs('support.*') ? 'support' : 'public');
    $navClass = 'flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg transition-all duration-150';
    $activeClass = 'bg-blue-600 text-white font-medium';
    $inactiveClass = 'text-slate-300 hover:bg-slate-700 hover:text-white';
@endphp

<!-- Dashboard -->
<a href="{{ route('dashboard') }}"
   class="{{ $navClass }} {{ request()->routeIs('dashboard') ? $activeClass : $inactiveClass }}">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
    </svg>
    Dashboard
</a>

@role('admin')
<div class="pt-3">
    <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Administrasi</p>

    <a href="{{ route('admin.users.index') }}"
       class="{{ $navClass }} {{ request()->routeIs('admin.users.*') ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        Manajemen User
    </a>

    <a href="#" class="{{ $navClass }} {{ $inactiveClass }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Semua Tiket
        <span class="ml-auto bg-blue-100 text-blue-700 text-xs font-medium px-2 py-0.5 rounded-full">0</span>
    </a>

    <a href="#" class="{{ $navClass }} {{ $inactiveClass }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Laporan & Statistik
    </a>

    <a href="#" class="{{ $navClass }} {{ $inactiveClass }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Audit Log
    </a>
</div>
@endrole

@role('support')
<div class="pt-3">
    <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Penanganan Tiket</p>
    <a href="#" class="{{ $navClass }} {{ $inactiveClass }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Semua Tiket
    </a>
</div>
@endrole

@role('public')
<div class="pt-3">
    <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Laporan Saya</p>
    <a href="{{ route('public.reports.create') }}"
       class="{{ $navClass }} {{ request()->routeIs('public.reports.create') ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Buat Laporan Baru
    </a>
    <a href="{{ route('public.reports.index') }}"
       class="{{ $navClass }} {{ request()->routeIs('public.reports.*') && !request()->routeIs('public.reports.create') ? $activeClass : $inactiveClass }}">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Laporan Saya
    </a>
</div>
@endrole
