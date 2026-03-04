<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - CSIRT Bali</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 flex items-center justify-center p-4 font-[Inter]">

    <div class="w-full max-w-md">

        {{-- Logo & Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16">
                <img src="{{ asset('images/logo.png') }}" alt="CSIRT Bali" class="h-16 w-auto object-contain mb-4">
            </div>
            <h1 class="text-2xl font-bold text-white">Aduan Insiden & Kerentanan</h1>
            <p class="text-blue-300 text-sm mt-1">PEMERINTAH PROVINSI BALI</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-1">Pembaruan Password Wajib</h2>
            <p class="text-sm text-amber-600 mb-6">
                @if($isForced)
                    Demi keamanan akun Anda, password harus diperbarui sebelum melanjutkan.
                @else
                    Password Anda akan kedaluwarsa {{ $daysLeft <= 0 ? 'hari ini' : 'dalam '.$daysLeft.' hari' }}.
                @endif
            </p>

            @if(session('warning'))
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800 flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
                </svg>
                {{ session('warning') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
                @endforeach
            </div>
            @endif

            {{-- Ketentuan password --}}
            <div class="mb-5 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                <p class="font-semibold mb-1">Ketentuan password baru:</p>
                <ul class="space-y-0.5 list-disc list-inside">
                    <li>Minimal 8 karakter</li>
                    <li>Kombinasi huruf besar & kecil</li>
                    <li>Mengandung angka dan simbol (!@#$%)</li>
                    <li>Tidak boleh sama dengan {{ auth()->user()::PASSWORD_HISTORY_LIMIT }} password terakhir</li>
                    <li>Tidak boleh pernah bocor (dicek via HaveIBeenPwned)</li>
                </ul>
            </div>

            <form method="POST" action="{{ route('password.change.update') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat Ini</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                   placeholder="••••••••">
                            <button type="button" onclick="toggleField('current_password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password" id="new_password" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                   placeholder="Buat password baru yang kuat">
                            <button type="button" onclick="toggleField('new_password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="confirm_password" required
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-10"
                                   placeholder="Ulangi password baru">
                            <button type="button" onclick="toggleField('confirm_password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm mt-2">
                        Perbarui Password
                    </button>
                </div>
            </form>

            @if(!$isForced)
            <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600">
                    Lewati untuk sekarang (berlaku {{ $daysLeft }} hari lagi)
                </a>
            </div>
            @endif
        </div>

        {{-- Security Notice --}}
        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-blue-300/70">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
            </svg>
            Koneksi aman & terenkripsi
        </div>
    </div>

    <script>
    function toggleField(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
    </script>
</body>
</html>
