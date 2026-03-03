<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup 2FA - CSIRT Bali</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4 font-[Inter]">

    <div class="w-full max-w-lg py-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-2xl shadow-lg mb-3">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Setup Two-Factor Auth</h1>
            <p class="text-blue-300 text-sm">CSIRT Provinsi Bali - Keamanan Akun</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
                @endforeach
            </div>
            @endif

            <!-- Step 1: Scan QR -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="flex items-center justify-center w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full">1</span>
                    <h2 class="text-sm font-semibold text-gray-700">Install Google Authenticator</h2>
                </div>
                <p class="text-xs text-gray-500 ml-8">
                    Download <strong>Google Authenticator</strong> atau <strong>Authy</strong> di smartphone Anda
                    (tersedia di App Store & Google Play).
                </p>
            </div>

            <!-- Step 2: Scan QR -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="flex items-center justify-center w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full">2</span>
                    <h2 class="text-sm font-semibold text-gray-700">Scan QR Code</h2>
                </div>
                <div class="ml-8">
                    <div class="inline-block p-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm mb-3">
                        {{-- Render QR code via Google Charts API --}}
                        {{-- Dengan ini: --}}
<img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}"
     alt="QR Code 2FA"
     class="w-44 h-44 rounded">
                    </div>
                    <p class="text-xs text-gray-500">
                        Tidak bisa scan? Masukkan kode manual berikut ke aplikasi authenticator:
                    </p>
                    <div class="mt-2 flex items-center gap-2">
                        <code id="secret-code" class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono text-gray-800 tracking-widest select-all">
                            {{ $secret }}
                        </code>
                        <button type="button" onclick="copySecret()"
                                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-medium transition-colors">
                            Salin
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Verifikasi OTP -->
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="flex items-center justify-center w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full">3</span>
                    <h2 class="text-sm font-semibold text-gray-700">Verifikasi Kode OTP</h2>
                </div>
                <div class="ml-8">
                    <p class="text-xs text-gray-500 mb-3">
                        Masukkan 6 digit kode dari aplikasi authenticator untuk mengaktifkan 2FA.
                    </p>
                    <form method="POST" action="{{ route('2fa.enable') }}">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text"
                                   name="otp"
                                   maxlength="6"
                                   inputmode="numeric"
                                   pattern="[0-9]{6}"
                                   autocomplete="one-time-code"
                                   placeholder="000000"
                                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-center text-xl font-mono tracking-[0.5em] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm">
                                Aktifkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info security -->
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                    </svg>
                    <p class="text-xs text-amber-700">
                        <strong>Penting:</strong> Simpan kode secret di tempat aman sebagai backup.
                        Jika Anda kehilangan akses ke aplikasi authenticator, hubungi administrator.
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600">
                    Lewati untuk sekarang
                </a>
            </div>
        </div>
    </div>

    <script>
    function copySecret() {
        const code = document.getElementById('secret-code').innerText.trim();
        navigator.clipboard.writeText(code).then(() => {
            const btn = event.target;
            btn.textContent = 'Tersalin!';
            btn.classList.add('bg-green-100', 'text-green-700');
            setTimeout(() => {
                btn.textContent = 'Salin';
                btn.classList.remove('bg-green-100', 'text-green-700');
            }, 2000);
        });
    }
    </script>
</body>
</html>
