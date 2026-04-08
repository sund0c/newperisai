<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - CSIRT Bali</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4 font-[Inter]">

    <div class="w-full max-w-lg py-8">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 ">
                <img src="{{ asset('images/logo.png') }}" alt="CSIRT Bali" class="h-16 w-auto object-contain mb-4">
            </div>
            <h1 class="text-2xl font-bold text-white">Buat Akun Baru</h1>
            <p class="text-blue-300 text-sm">CSIRT Provinsi Bali - Sistem Aduan</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8">
            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Nama sesuai KTP">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="email@domain.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Instansi/Organisasi <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="organization" value="{{ old('organization') }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Dinas/Instansi/Masyarakat Umum">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password <span
                                    class="text-red-500">*</span></label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Min. 8 karakter dengan huruf, angka & simbol">
                            <p class="mt-1 text-xs text-gray-400">Min. 8 karakter, huruf besar/kecil, angka, dan simbol
                                (!@#$%)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password <span
                                    class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Ulangi password">
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-2 mt-2">
                        <input type="checkbox" name="terms" id="terms" required
                            class="w-4 h-4 mt-0.5 rounded border-gray-300 text-blue-600">
                        <label for="terms" class="text-sm text-gray-600">
                            Saya menyetujui bahwa informasi yang saya berikan adalah benar dan dapat
                            dipertanggungjawabkan.
                            Data akan digunakan sesuai <a href="#" class="text-blue-600 underline">kebijakan
                                privasi</a> CSIRT Bali.
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm mt-2">
                        Daftar & Kirim Verifikasi Email
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-500">
                    Sudah punya akun? <a href="{{ route('login') }}"
                        class="text-blue-600 hover:text-blue-700 font-medium">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
