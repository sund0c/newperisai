<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeliharaan Sistem — CSIRT Bali</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css'])
</head>
<body class="h-full bg-slate-50 font-[Inter] flex items-center justify-center px-4">

    <div class="max-w-md w-full text-center">

        {{-- Ikon --}}
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/>
                </svg>
            </div>
        </div>

        {{-- Logo & Nama --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="CSIRT Bali" class="h-7 w-auto object-contain">
            <span class="text-slate-700 font-semibold text-base">Aduan CSIRT Pemprov Bali</span>
        </div>

        {{-- Judul --}}
        <h1 class="text-2xl font-bold text-slate-800 mb-3">
            Sistem Sedang Pemeliharaan
        </h1>

        {{-- Deskripsi --}}
        <p class="text-slate-500 text-sm leading-relaxed mb-6">
            Kami sedang melakukan pemeliharaan sistem untuk meningkatkan kualitas layanan.
            Mohon maaf atas ketidaknyamanan ini.
        </p>

        {{-- Divider --}}
        <div class="border-t border-slate-200 my-6"></div>

        {{-- Info kontak --}}
        <p class="text-slate-500 text-sm mb-3">
            Selama proses pemeliharaan, Anda tetap dapat melaporkan insiden siber melalui email:
        </p>

        <a href="mailto:csirt@baliprov.go.id"
           class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-700
                  text-white text-sm font-semibold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            csirt@baliprov.go.id
        </a>

        {{-- Footer --}}
        <p class="text-xs text-slate-400 mt-8">
            &copy; {{ date('Y') }} CSIRT Provinsi Bali — Pemerintah Provinsi Bali
        </p>

    </div>

</body>
</html>
