<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PERISAI-PROVBALI')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>

<body class="h-full bg-gray-50 font-[Inter]" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
        @click="sidebarOpen = false">
    </div>

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 bg-slate-900 transition-transform duration-300 ease-in-out"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-6 bg-slate-800 shrink-0">
            <div class="flex items-center gap-3">
                <div class="inline-flex items-center justify-center w-8 h-8">
                    <img src="{{ asset('images/logo_perisai.svg') }}" alt="CSIRT Bali" class="h-8 w-auto object-contain mb-4">
                </div>
                <div>
                    <p class="text-white font-semibold text-sm">PERISAI Pemprov Bali</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- User Info -->
        <div class="px-4 py-3 border-b border-slate-700">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 text-xs capitalize">
                        {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            @include('layouts.partials.sidebar-nav')
        </nav>

        <!-- Bottom Actions -->
        <div class="px-3 py-4 border-t border-slate-700 space-y-1">
            <a href="{{ route('profile.index') }}"
                class="flex items-center gap-3 px-3 py-2 text-sm text-slate-300 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profil & Keamanan
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 text-sm text-red-400 rounded-lg hover:bg-red-900/30 hover:text-red-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="lg:pl-64 flex flex-col min-h-full">

        <!-- Top Bar -->
        <header
            class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200 shadow-sm">

            <!-- Hamburger (mobile) -->
            <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Kanan: Tahun Switcher + 2FA Badge -->
            <div class="flex items-center gap-3 ml-auto">

                {{-- Tahun Switcher --}}
                @if (isset($allTahun) && $allTahun->isNotEmpty())
                    <div class="relative" x-data="{ open: false }">

                        {{-- Trigger: Badge Pill --}}
                        <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-full text-sm font-medium hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Tahun: <strong>{{ $tahunContext?->tahun ?? '-' }}</strong></span>
                            <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-xl shadow-lg z-50 py-1 overflow-hidden"
                            style="display:none">

                            <p
                                class="px-3 py-1.5 text-xs text-gray-400 font-medium uppercase tracking-wide border-b border-gray-100">
                                Pilih Tahun
                            </p>

                            @foreach ($allTahun as $t)
                                <button onclick="setTahunContext('{{ $t->id }}')"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm transition-colors
                    {{ isset($tahunContext) && $tahunContext?->id === $t->id
                        ? 'bg-blue-50 text-blue-700 font-semibold'
                        : 'text-gray-700 hover:bg-gray-50' }}">
                                    <span>{{ $t->tahun }}</span>
                                    <span class="flex items-center gap-1">
                                        @if ($t->is_active)
                                            <span
                                                class="text-xs text-green-600 bg-green-50 border border-green-200 rounded-full px-1.5 py-0.5 leading-none">
                                                aktif
                                            </span>
                                        @endif
                                        @if (isset($tahunContext) && $tahunContext?->id === $t->id)
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 2FA Badge --}}
                @if (!auth()->user()->google2fa_enabled)
                    <a href="{{ route('2fa.setup') }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs bg-amber-50 text-amber-700 border border-amber-200 rounded-full hover:bg-amber-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Aktifkan 2FA
                    </a>
                @else
                    <span
                        class="flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2.5 py-1 rounded-full border border-green-200">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                        </svg>
                        2FA Aktif
                    </span>
                @endif

            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 px-6 pb-8">
            <div class="mb-6 pt-4">
                <h1 class="text-xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                @hasSection('page-subtitle')
                    <p class="text-sm text-gray-500 mt-1">@yield('page-subtitle')</p>
                @endif
            </div>
            @yield('content')
        </main>

    </div>

    <!-- Tahun Switcher Script -->
    @if (isset($allTahun) && $allTahun->isNotEmpty())
        <script>
            document.getElementById('tahun-switcher')?.addEventListener('change', function() {
                fetch('{{ route('admin.tahunaktif.set-context') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            tahunaktif_id: this.value
                        }),
                    })
                    .then(res => res.ok ? window.location.reload() : Promise.reject())
                    .catch(() => alert('Gagal mengganti tahun. Silakan coba lagi.'));
            });
        </script>

        <script>
            function setTahunContext(id) {
                fetch('{{ route('admin.tahunaktif.set-context') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            tahunaktif_id: id
                        }),
                    })
                    .then(res => res.ok ? window.location.reload() : Promise.reject())
                    .catch(() => alert('Gagal mengganti tahun. Silakan coba lagi.'));
            }
        </script>

        @stack('scripts')
    @endif

    @stack('scripts')

</body>

</html>
