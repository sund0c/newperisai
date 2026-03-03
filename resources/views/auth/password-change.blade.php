<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - CSIRT Bali</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4 font-[Inter]">
    <div class="w-full max-w-md">

        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-500 rounded-2xl shadow-lg mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white">Pembaruan Password Wajib</h1>
            <p class="text-amber-300 text-sm mt-1">
                @if($isForced)
                    Demi keamanan akun Anda, password harus diperbarui.
                @else
                    Password Anda akan kedaluwarsa {{ $daysLeft <= 0 ? 'hari ini' : 'dalam '.$daysLeft.' hari' }}.
                @endif
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">

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

            <!-- Info kebijakan password -->
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
                        <input type="password" name="current_password" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Buat password baru yang kuat">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ulangi password baru">
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors">
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

        <p class="text-center text-xs text-blue-300/60 mt-4">
            Password berlaku hingga {{ $expiresAt }}
        </p>
    </div>
</body>
</html>
