<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem — CSIRT Bali</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-gray-50 font-[Inter] flex items-center justify-center px-4">

    <div class="max-w-md w-full text-center">

        {{-- Logo CSIRT --}}
        <img src="{{ asset('images/logo.png') }}"
             alt="CSIRT Bali"
             class="h-20 w-auto object-contain mx-auto mb-8">

        {{-- Label --}}
        <p class="text-xs font-medium text-gray-400 uppercase tracking-widest mb-3">
            Aduan CSIRT Pemprov Bali
        </p>

        {{-- Judul --}}
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            Sistem Sedang Pemeliharaan
        </h1>

        {{-- Deskripsi --}}
        <p class="text-sm text-gray-500 leading-relaxed mb-3">
            Kami sedang melakukan pemeliharaan sistem untuk meningkatkan kualitas layanan.
            Mohon maaf atas ketidaknyamanan ini.
        </p>

        <p class="text-sm text-gray-500 leading-relaxed mb-10">
            Selama proses pemeliharaan, Anda tetap dapat melaporkan insiden siber melalui email:
        </p>

        {{-- Tombol Email --}}
        <a href="mailto:csirt@baliprov.go.id"
           class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700
                  text-white text-sm font-semibold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            csirt@baliprov.go.id
        </a>

        {{-- Logout --}}
        @auth
        <div class="mt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 text-xs text-gray-400
                               hover:text-red-500 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar dari akun
                </button>
            </form>
        </div>
        @endauth

        {{-- Footer --}}
        <p class="text-xs text-gray-400 mt-16">
            &copy; {{ date('Y') }} CSIRT Provinsi Bali
        </p>

    </div>

</body>
</html>
