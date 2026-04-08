<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - CSIRT Bali</title>
    @vite(['resources/css/app.css'])
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4 font-[Inter]">

    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 ">
                <img src="{{ asset('images/logo.png') }}" alt="CSIRT Bali" class="h-16 w-auto object-contain mb-4">
            </div>
            <h1 class="text-2xl font-bold text-white">Verifikasi Email Anda</h1>
            <p class="text-blue-300 text-sm mt-1">CSIRT Provinsi Bali</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">

            @if (session('status') == 'verification-link-sent')
                <div
                    class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                    </svg>
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            @endif

            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Cek Inbox Email Anda</h2>
                <p class="text-sm text-gray-500">
                    Kami telah mengirimkan link verifikasi ke
                    <span class="font-semibold text-gray-700">{{ auth()->user()->email }}</span>.
                    Klik link tersebut untuk mengaktifkan akun Anda.
                </p>
            </div>

            <div class="space-y-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm transition-colors">
                        Keluar
                    </button>
                </form>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 text-center">
                    Tidak menerima email? Periksa folder spam atau gunakan tombol "Kirim Ulang" di atas.
                </p>
            </div>
        </div>
    </div>

</body>

</html>
