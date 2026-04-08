<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA - CSIRT Bali</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4 font-[Inter]">

    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16">
                <img src="{{ asset('images/logo.png') }}" alt="CSIRT Bali" class="h-16 w-auto object-contain mb-4">
            </div>
            <h1 class="text-2xl font-bold text-white">Verifikasi Dua Faktor</h1>
            <p class="text-blue-300 text-sm">CSIRT Provinsi Bali</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="text-center mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-1">Masukkan Kode Authenticator</h2>
                <p class="text-sm text-gray-500">
                    Buka <strong>Google Authenticator</strong>, <strong>Aegis</strong>, atau aplikasi TOTP lainnya
                    di smartphone Anda dan masukkan kode 6 digit yang ditampilkan.
                </p>
            </div>

            <form method="POST" action="{{ route('2fa.verify') }}" id="verify-form">
                @csrf
                <div class="mb-4">
                    <label for="otp" class="block text-sm font-medium text-gray-700 mb-2 text-center">Kode
                        OTP</label>
                    <input type="text" name="otp" id="otp" maxlength="6" inputmode="numeric"
                        pattern="[0-9]{6}" autocomplete="one-time-code" placeholder="000000" autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-center text-2xl font-mono tracking-[0.6em] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required>
                    <p class="mt-1.5 text-xs text-gray-400 text-center">Kode berlaku selama 30 detik</p>
                </div>

                <!-- Timer visual — informatif saja, bukan trigger submit -->
                <div class="mb-5">
                    <div class="flex items-center justify-between text-xs text-gray-400 mb-1">
                        <span>Sisa waktu kode saat ini</span>
                        <span id="timer-text">-- detik</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div id="timer-bar" class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000"
                            style="width: 100%">
                        </div>
                    </div>
                    {{--
                        Timer hanya visual/informatif — membantu user tahu kapan kode akan
                        berganti sehingga mereka bisa menunggu kode baru jika hampir habis.
                        Tidak ada auto-submit. User submit sendiri.
                    --}}
                </div>

                <button type="submit" id="submit-btn"
                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm">
                    Verifikasi &amp; Masuk
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-gray-600">
                        Keluar &amp; Gunakan Akun Lain
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-blue-300/70">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" />
            </svg>
            Koneksi aman &amp; terenkripsi
        </div>
    </div>

    <script>
        // Timer countdown sinkron dengan periode TOTP (30 detik)
        // Hanya informatif — tidak memicu submit otomatis
        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            const remaining = 30 - (now % 30);
            const percent = (remaining / 30) * 100;

            document.getElementById('timer-text').textContent = remaining + ' detik';
            const bar = document.getElementById('timer-bar');
            bar.style.width = percent + '%';

            if (remaining <= 5) {
                bar.className = 'bg-red-500 h-1.5 rounded-full transition-all duration-1000';
            } else if (remaining <= 10) {
                bar.className = 'bg-amber-500 h-1.5 rounded-full transition-all duration-1000';
            } else {
                bar.className = 'bg-blue-500 h-1.5 rounded-full transition-all duration-1000';
            }
        }

        updateTimer();
        setInterval(updateTimer, 1000);

        // Filter input: hanya angka, tidak auto-submit
        document.getElementById('otp').addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });

        // Cegah double-submit (klik tombol dua kali cepat)
        document.getElementById('verify-form').addEventListener('submit', function() {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Memverifikasi...';
        });
    </script>
</body>

</html>
