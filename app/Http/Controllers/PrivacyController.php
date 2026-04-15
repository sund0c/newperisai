<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - CSIRT Bali</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 font-[Inter] text-white">

    {{-- Sticky Nav --}}
    <header class="sticky top-0 z-50 backdrop-blur-md bg-slate-900/70 border-b border-white/10">
        <div class="max-w-4xl mx-auto px-6 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="CSIRT Bali" class="h-8 w-auto object-contain">
                <span class="text-sm font-semibold text-white/90 hidden sm:block">CSIRT Bali</span>
                <span class="text-white/20 hidden sm:block">·</span>
                <span class="text-sm text-blue-300 hidden sm:block">Kebijakan Privasi</span>
            </div>
            <a href="{{ url()->previous() }}"
                class="flex items-center gap-1.5 text-xs text-blue-300 hover:text-white transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-6 py-10 pb-20">

        {{-- Hero --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 bg-blue-500/10 border border-blue-500/30 rounded-full px-4 py-1.5 text-xs font-semibold text-blue-300 uppercase tracking-widest mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                Dokumen Resmi
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Kebijakan Privasi</h1>
            <p class="text-blue-300 text-sm mb-5">Aduan Insiden &amp; Kerentanan — Pemerintah Provinsi Bali</p>
            <div class="flex items-center justify-center gap-5 text-xs text-white/40 flex-wrap">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Berlaku sejak: {{ $effectiveDate }}
                </span>
                <span class="text-white/20">|</span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Diperbarui: {{ $lastUpdated }}
                </span>
            </div>
        </div>

        {{-- Table of Contents --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-8">
            <p class="text-xs font-bold uppercase tracking-widest text-blue-300 mb-3 flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Daftar Isi
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-0.5">
                @foreach ([
                ['#pendahuluan', '1. Pendahuluan'],
                ['#data-dikumpulkan', '2. Data yang Dikumpulkan'],
                ['#penggunaan-data', '3. Penggunaan Data'],
                ['#penyimpanan', '4. Penyimpanan &amp; Keamanan'],
                ['#berbagi-data', '5. Berbagi Data'],
                ['#hak-pengguna', '6. Hak Pengguna'],
                ['#cookies', '7. Cookies'],
                ['#perubahan', '8. Perubahan Kebijakan'],
                ['#kontak', '9. Hubungi Kami'],
                ] as [$anchor, $label])
                <a href="{{ $anchor }}"
                    class="text-sm text-white/60 hover:text-blue-300 py-1.5 border-b border-white/5 transition-colors">
                    {!! $label !!}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Policy Sections --}}
        <div class="space-y-4">

            {{-- 01 Pendahuluan --}}
            <section id="pendahuluan" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">01</span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 mb-3">Pendahuluan</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-3">
                            CSIRT Bali (<em>Computer Security Incident Response Team</em> Provinsi Bali) berkomitmen untuk melindungi privasi dan keamanan data pribadi setiap individu yang menggunakan layanan pelaporan insiden keamanan siber kami.
                        </p>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi Anda sesuai dengan <strong class="text-gray-800">Undang-Undang Nomor 27 Tahun 2022</strong> tentang Perlindungan Data Pribadi (UU PDP) Republik Indonesia.
                        </p>
                        <div class="flex gap-3 bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-700">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="leading-relaxed">Dengan menggunakan layanan kami, Anda menyetujui pengumpulan dan penggunaan informasi sesuai kebijakan ini. Jika Anda tidak menyetujui, mohon tidak menggunakan layanan kami.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 02 Data yang Dikumpulkan --}}
            <section id="data-dikumpulkan" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">02</span>
                    </div>
                    <div class="w-full">
                        <h2 class="text-base font-bold text-gray-900 mb-3">Data yang Dikumpulkan</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Kami mengumpulkan informasi yang Anda berikan secara langsung saat melaporkan insiden keamanan siber, antara lain:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ([
                            ['Identitas', ['Nama lengkap', 'Nomor identitas (NIK/NIP)', 'Jabatan / instansi']],
                            ['Kontak', ['Alamat email', 'Nomor telepon', 'Alamat instansi']],
                            ['Insiden', ['Deskripsi insiden', 'Timestamp kejadian', 'Sistem / aset terdampak', 'Bukti pendukung (log, screenshot)']],
                            ['Teknis', ['Alamat IP pengirim', 'User-agent browser', 'Log akses sistem']],
                            ] as [$label, $items])
                            <div class="border border-gray-100 rounded-xl p-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">{{ $label }}</p>
                                <ul class="space-y-1">
                                    @foreach ($items as $item)
                                    <li class="flex items-center gap-2 text-sm text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                                        {{ $item }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- 03 Penggunaan Data --}}
            <section id="penggunaan-data" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">03</span>
                    </div>
                    <div class="w-full">
                        <h2 class="text-base font-bold text-gray-900 mb-3">Penggunaan Data</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Data yang Anda berikan digunakan <strong class="text-gray-800">semata-mata</strong> untuk keperluan penanganan insiden keamanan siber dan tidak akan digunakan untuk tujuan komersial.
                        </p>
                        <div class="space-y-2">
                            @foreach ($usages as $usage)
                            <div class="flex items-start gap-3 text-sm text-gray-600">
                                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-50 border border-green-200 flex items-center justify-center mt-0.5">
                                    <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                {{ $usage }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- 04 Penyimpanan & Keamanan --}}
            <section id="penyimpanan" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">04</span>
                    </div>
                    <div class="w-full">
                        <h2 class="text-base font-bold text-gray-900 mb-3">Penyimpanan &amp; Keamanan Data</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Kami menerapkan langkah-langkah keamanan teknis dan organisasi sesuai standar untuk melindungi data Anda dari akses tidak sah, perubahan, pengungkapan, atau penghancuran.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ([
                            ['green', 'Enkripsi Data', 'Data sensitif dienkripsi dengan AES-256 saat penyimpanan dan TLS 1.3 saat transmisi.'],
                            ['blue', 'Kontrol Akses', 'Akses dibatasi hanya untuk personel CSIRT Bali yang berwenang sesuai tugas resmi.'],
                            ['orange', 'Pemantauan 24/7', 'Sistem pemantauan aktif mendeteksi dan merespons potensi ancaman keamanan data.'],
                            ['purple', 'Retensi 5 Tahun', 'Data disimpan 5 tahun sejak tanggal laporan, kemudian dihapus secara aman.'],
                            ] as [$color, $title, $desc])
                            <div class="border border-gray-100 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="w-2 h-2 rounded-full
                                        @if($color === 'green') bg-green-400
                                        @elseif($color === 'blue') bg-blue-400
                                        @elseif($color === 'orange') bg-orange-400
                                        @else bg-purple-400 @endif flex-shrink-0"></span>
                                    <p class="text-sm font-semibold text-gray-800">{{ $title }}</p>
                                </div>
                                <p class="text-sm text-gray-500 leading-relaxed">{{ $desc }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- 05 Berbagi Data --}}
            <section id="berbagi-data" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">05</span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 mb-3">Berbagi Data dengan Pihak Ketiga</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            CSIRT Bali <strong class="text-gray-800">tidak menjual, menyewakan, atau memperjualbelikan</strong> data pribadi Anda kepada pihak ketiga.
                        </p>
                        <div class="flex gap-3 bg-amber-50 border border-amber-100 rounded-xl p-4 text-sm text-amber-800">
                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                            <p class="leading-relaxed">Data hanya dibagikan kepada instansi pemerintah terkait (BSSN, Kominfo, Kepolisian) untuk penegakan hukum atau koordinasi penanganan insiden siber nasional, berdasarkan dasar hukum yang sah.</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 06 Hak Pengguna --}}
            <section id="hak-pengguna" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">06</span>
                    </div>
                    <div class="w-full">
                        <h2 class="text-base font-bold text-gray-900 mb-3">Hak Anda sebagai Subjek Data</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">Sesuai UU PDP, Anda memiliki hak-hak berikut terkait data pribadi Anda:</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ([
                            ['Akses', 'Meminta salinan data pribadi yang kami simpan tentang Anda.'],
                            ['Koreksi', 'Meminta perbaikan data yang tidak akurat atau tidak lengkap.'],
                            ['Penghapusan', 'Meminta penghapusan data dalam kondisi yang diatur undang-undang.'],
                            ['Keberatan', 'Mengajukan keberatan atas pemrosesan data dalam situasi tertentu.'],
                            ] as [$label, $desc])
                            <div class="border border-gray-100 rounded-xl p-4 text-center">
                                <span class="inline-block bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full mb-2">{{ $label }}</span>
                                <p class="text-xs text-gray-500 leading-relaxed">{{ $desc }}</p>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Untuk menggunakan hak-hak Anda, silakan hubungi kami melalui kontak pada bagian 9.</p>
                    </div>
                </div>
            </section>

            {{-- 07 Cookies --}}
            <section id="cookies" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">07</span>
                    </div>
                    <div class="w-full">
                        <h2 class="text-base font-bold text-gray-900 mb-3">Cookies &amp; Teknologi Pelacakan</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Portal CSIRT Bali menggunakan cookies yang <strong class="text-gray-800">diperlukan secara teknis</strong> untuk memastikan fungsi sistem berjalan dengan baik. Kami tidak menggunakan cookies pihak ketiga untuk tujuan periklanan atau analitik komersial.
                        </p>
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-800 text-white">
                                    <tr>
                                        <th class="text-left px-4 py-3 text-xs font-semibold tracking-wider">Nama Cookie</th>
                                        <th class="text-left px-4 py-3 text-xs font-semibold tracking-wider">Tujuan</th>
                                        <th class="text-left px-4 py-3 text-xs font-semibold tracking-wider">Masa Berlaku</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ([
                                    ['XSRF-TOKEN', 'Perlindungan CSRF pada formulir', 'Sesi browser'],
                                    ['csirt_session', 'Manajemen sesi pengguna', '2 jam'],
                                    ['remember_token', 'Autentikasi persisten (opsional)', '30 hari'],
                                    ] as [$name, $purpose, $duration])
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3">
                                            <code class="bg-slate-100 text-blue-700 text-xs px-2 py-0.5 rounded font-mono">{{ $name }}</code>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">{{ $purpose }}</td>
                                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $duration }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 08 Perubahan Kebijakan --}}
            <section id="perubahan" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">08</span>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 mb-3">Perubahan Kebijakan</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-2">
                            Kami berhak memperbarui Kebijakan Privasi ini sewaktu-waktu. Perubahan signifikan akan diinformasikan melalui notifikasi di portal kami atau melalui email ke alamat terdaftar, minimal <strong class="text-gray-800">14 hari</strong> sebelum perubahan berlaku.
                        </p>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Penggunaan layanan kami setelah tanggal efektif perubahan dianggap sebagai persetujuan Anda terhadap kebijakan yang telah diperbarui.
                        </p>
                    </div>
                </div>
            </section>

            {{-- 09 Kontak --}}
            <section id="kontak" class="bg-white rounded-2xl shadow-2xl p-7">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                        <span class="text-xs font-bold text-blue-600">09</span>
                    </div>
                    <div class="w-full">
                        <h2 class="text-base font-bold text-gray-900 mb-3">Hubungi Kami</h2>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">
                            Jika Anda memiliki pertanyaan atau ingin menggunakan hak-hak Anda, silakan hubungi <strong class="text-gray-800">Data Protection Officer (DPO)</strong> CSIRT Bali:
                        </p>
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 space-y-3">
                            <div class="flex items-center gap-3 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <a href="mailto:{{ $contactEmail }}" class="text-blue-600 hover:underline">{{ $contactEmail }}</a>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $contactPhone }}</span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ $contactAddress }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-700">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Senin–Jumat, 08.00–16.00 WITA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>{{-- end .space-y-4 --}}

        {{-- Footer --}}
        <div class="mt-10 flex flex-col items-center gap-3">
            <a href="{{ url()->previous() }}"
                class="inline-flex items-center gap-2 py-2.5 px-5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Formulir
            </a>
            <div class="flex items-center gap-2 text-xs text-blue-300/70">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                © {{ date('Y') }} CSIRT Bali — Dinas Komunikasi, Informatika dan Statistik Provinsi Bali
            </div>
        </div>

    </div>

</body>

</html>