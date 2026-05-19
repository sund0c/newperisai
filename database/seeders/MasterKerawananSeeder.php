<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MasterKerawananSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // 1. KELAS ASET
        // =====================================================================
        $classes = [
            ['kode' => 'PL', 'nama' => 'Perangkat Lunak',   'urutan' => 1],
            ['kode' => 'PK', 'nama' => 'Perangkat Keras',   'urutan' => 2],
            ['kode' => 'DI', 'nama' => 'Data & Informasi',  'urutan' => 3],
            ['kode' => 'SDM', 'nama' => 'Sumber Daya Manusia', 'urutan' => 4],
            ['kode' => 'SP', 'nama' => 'Sarana & Prasarana', 'urutan' => 5],
        ];

        $classIds = [];
        foreach ($classes as $class) {
            $id = (string) Str::uuid();
            $classIds[$class['kode']] = $id;
            DB::table('asset_classes')->insertOrIgnore([
                'id'         => $id,
                'kode'       => $class['kode'],
                'nama'       => $class['nama'],
                'urutan'     => $class['urutan'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =====================================================================
        // 2. SUB-KELAS ASET
        // =====================================================================
        $subclasses = [
            // Perangkat Lunak
            ['kode' => 'PL-WEB', 'class' => 'PL', 'nama' => 'Berbasis Web',        'urutan' => 1],
            ['kode' => 'PL-MOB', 'class' => 'PL', 'nama' => 'Berbasis Mobile',     'urutan' => 2],
            ['kode' => 'PL-DSK', 'class' => 'PL', 'nama' => 'Berbasis Desktop',    'urutan' => 3],
            ['kode' => 'PL-SAS', 'class' => 'PL', 'nama' => 'SaaS / Cloud Pihak Ketiga', 'urutan' => 4],
            // Perangkat Keras
            ['kode' => 'PK-SRV', 'class' => 'PK', 'nama' => 'Server',              'urutan' => 1],
            ['kode' => 'PK-WRK', 'class' => 'PK', 'nama' => 'Workstation / PC',    'urutan' => 2],
            ['kode' => 'PK-NET', 'class' => 'PK', 'nama' => 'Perangkat Jaringan',  'urutan' => 3],
            ['kode' => 'PK-STR', 'class' => 'PK', 'nama' => 'Perangkat Storage',   'urutan' => 4],
            ['kode' => 'PK-IOT', 'class' => 'PK', 'nama' => 'Perangkat IoT / Embedded', 'urutan' => 5],
            // Data & Informasi
            ['kode' => 'DI-ELK', 'class' => 'DI', 'nama' => 'Data Elektronik',     'urutan' => 1],
            ['kode' => 'DI-FIS', 'class' => 'DI', 'nama' => 'Dokumen Fisik',       'urutan' => 2],
            // SDM
            ['kode' => 'SDM-INT', 'class' => 'SDM', 'nama' => 'Staf Internal',     'urutan' => 1],
            ['kode' => 'SDM-EXT', 'class' => 'SDM', 'nama' => 'Pihak Ketiga / Vendor', 'urutan' => 2],
        ];

        $subclassIds = [];
        foreach ($subclasses as $sub) {
            $id = (string) Str::uuid();
            $subclassIds[$sub['kode']] = $id;
            DB::table('asset_subclasses')->insertOrIgnore([
                'id'             => $id,
                'asset_class_id' => $classIds[$sub['class']],
                'kode'           => $sub['kode'],
                'nama'           => $sub['nama'],
                'urutan'         => $sub['urutan'],
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // =====================================================================
        // 3. VULNERABILITY SETS & ITEMS
        // =====================================================================

        // --- Global Perangkat Lunak v1.0 ---
        $setGlobalPL = (string) Str::uuid();
        DB::table('vulnerability_sets')->insertOrIgnore([
            'id'                => $setGlobalPL,
            'scope_type'        => 'global_class',
            'scope_id'          => $classIds['PL'],
            'versi'             => '1.0',
            'is_active'         => true,
            'catatan_perubahan' => 'Versi awal — kerawanan umum berlaku untuk semua sub-kelas Perangkat Lunak.',
            'published_at'      => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $globalPLItems = [
            [
                'nomor_urut'      => 1,
                'deskripsi'       => 'Tidak ada kebijakan manajemen kata sandi yang kuat (panjang minimal, kompleksitas, rotasi berkala)',
                'kontrol_tipikal' => 'Kata sandi dibuat bebas oleh pengguna tanpa validasi format; tidak ada kebijakan tertulis yang diterapkan secara teknis di sistem',
                'mitigasi_tipikal' => 'Terapkan password policy secara teknis: minimal 12 karakter, wajib kombinasi huruf besar+kecil+angka+simbol; password expiry 90 hari; password history 5 entri; strength meter real-time (zxcvbn lokal); blocklist password umum lokal',
            ],
            [
                'nomor_urut'      => 2,
                'deskripsi'       => 'Tidak ada kebijakan kompleksitas kata sandi (karakter spesial, campuran huruf besar/kecil, angka) dan masa berlaku',
                'kontrol_tipikal' => 'Sistem menerima kata sandi dengan format apapun tanpa validasi kompleksitas',
                'mitigasi_tipikal' => 'Enforced complexity rule di level aplikasi: wajib mengandung minimal 1 huruf besar, 1 huruf kecil, 1 angka, 1 simbol; tolak password yang tidak memenuhi syarat sebelum disimpan',
            ],
            [
                'nomor_urut'      => 3,
                'deskripsi'       => 'Kata sandi disimpan dalam plaintext atau menggunakan algoritma hash lemah tanpa salt (MD5/SHA1)',
                'kontrol_tipikal' => 'Penyimpanan kata sandi menggunakan metode bawaan framework tanpa verifikasi kekuatan algoritma',
                'mitigasi_tipikal' => 'Migrasi ke bcrypt (cost factor ≥ 12) atau Argon2id melalui Hash::make() Laravel; audit seluruh kode untuk memastikan tidak ada plaintext/MD5/SHA1; paksa reset password seluruh pengguna setelah migrasi',
            ],
            [
                'nomor_urut'      => 4,
                'deskripsi'       => 'Mekanisme pemulihan kata sandi (forgot password) rentan terhadap enumerasi akun atau account takeover',
                'kontrol_tipikal' => 'Fitur reset password tersedia namun tanpa pembatasan waktu token dan tanpa throttling permintaan',
                'mitigasi_tipikal' => 'Token reset single-use dan expire 60 menit; throttle 3×/jam per IP; response API identik terlepas akun terdaftar atau tidak (anti-enumeration); token disimpan sebagai hash di DB; kirim notifikasi email setiap ada permintaan reset',
            ],
            [
                'nomor_urut'      => 5,
                'deskripsi'       => 'Session ID tidak dihasilkan menggunakan CSPRNG (Cryptographically Secure Pseudo-Random Number Generator) sehingga dapat diprediksi',
                'kontrol_tipikal' => 'Session ID dihasilkan oleh mekanisme default tanpa verifikasi sumber entropi kriptografis',
                'mitigasi_tipikal' => 'Pastikan session menggunakan PHP random_bytes() / openssl_random_pseudo_bytes(); gunakan session driver database di Laravel; panjang session ID minimal 128-bit entropy; audit konfigurasi php.ini dan session.php',
            ],
            [
                'nomor_urut'      => 6,
                'deskripsi'       => 'Session fixation vulnerability — session ID tidak di-regenerate setelah proses autentikasi berhasil',
                'kontrol_tipikal' => 'Session ID yang sama digunakan sebelum dan sesudah login',
                'mitigasi_tipikal' => 'Panggil Session::regenerate(true) segera setelah autentikasi berhasil; implementasi konsisten melalui Laravel Fortify; verifikasi melalui pengujian manual',
            ],
            [
                'nomor_urut'      => 7,
                'deskripsi'       => 'Session token tidak memiliki entropi yang cukup dan nilainya dapat diprediksi atau di-brute-force',
                'kontrol_tipikal' => 'Panjang dan kompleksitas session token tidak diverifikasi',
                'mitigasi_tipikal' => 'Audit panjang dan entropi session token; pastikan minimal 128-bit; gunakan session driver database untuk auditability penuh',
            ],
            [
                'nomor_urut'      => 8,
                'deskripsi'       => 'Tidak ada mekanisme session timeout — tidak ada idle timeout maupun absolute timeout',
                'kontrol_tipikal' => 'Sesi tidak memiliki batas waktu yang dikonfigurasi; pengguna tetap login meskipun tidak aktif berjam-jam',
                'mitigasi_tipikal' => 'Idle timeout 15 menit (admin/operator), 30 menit (pengguna biasa); absolute timeout maksimum 8 jam; implementasi via Laravel session config dan middleware custom; tampilkan peringatan UI 2 menit sebelum sesi berakhir',
            ],
            [
                'nomor_urut'      => 9,
                'deskripsi'       => 'Tidak ada mekanisme invalidasi sesi yang efektif saat pengguna logout atau mengganti password',
                'kontrol_tipikal' => 'Logout hanya menghapus cookie di sisi klien tanpa invalidasi sesi di server',
                'mitigasi_tipikal' => 'Server-side session invalidation: hapus record sesi dari DB saat logout; saat ganti password, invalidasi semua sesi aktif; enforce single active session per pengguna; gunakan session driver database',
            ],
            [
                'nomor_urut'      => 10,
                'deskripsi'       => 'Missing function-level access control — tidak ada pengecekan otorisasi per endpoint/fungsi',
                'kontrol_tipikal' => 'Pembatasan akses hanya berdasarkan tampilan menu di UI, tanpa middleware otorisasi di sisi server',
                'mitigasi_tipikal' => 'Implementasi RBAC menggunakan Spatie Laravel Permission secara konsisten di semua route dan controller; gunakan middleware role: dan permission: pada setiap route group; audit route list untuk memastikan tidak ada endpoint tanpa proteksi',
            ],
            [
                'nomor_urut'      => 11,
                'deskripsi'       => 'Tidak ada validasi kepemilikan resource saat mengakses data (IDOR — Insecure Direct Object Reference)',
                'kontrol_tipikal' => 'Sistem mengambil resource berdasarkan ID tanpa memverifikasi bahwa resource milik pengguna yang sedang login',
                'mitigasi_tipikal' => 'Tambahkan ownership check (where user_id = auth id) di setiap query; gunakan UUID v4 sebagai primary key; implementasi Laravel Policy per resource; response 403 untuk resource yang tidak dimiliki',
            ],
            [
                'nomor_urut'      => 12,
                'deskripsi'       => 'Input validation hanya menggunakan pendekatan blacklist yang dapat di-circumvent',
                'kontrol_tipikal' => 'Filter input menggunakan regex blacklist atau strip karakter tertentu tanpa validasi format ketat',
                'mitigasi_tipikal' => 'Ganti seluruh validasi blacklist dengan whitelist approach; definisikan format yang diizinkan (tipe data, panjang, regex) untuk setiap field; gunakan Laravel Validation Rules eksplisit; tolak semua input yang tidak sesuai definisi',
            ],
            [
                'nomor_urut'      => 13,
                'deskripsi'       => 'Input tidak divalidasi untuk tipe data, panjang, format, dan range di sisi server',
                'kontrol_tipikal' => 'Validasi input minimal hanya di sisi klien; server menerima data apapun yang dikirim',
                'mitigasi_tipikal' => 'Implementasi server-side validation ketat menggunakan Laravel Form Request untuk setiap endpoint; validasi mencakup: type, max length, format, range, dan required fields; return 422 dengan pesan error generik',
            ],
            [
                'nomor_urut'      => 14,
                'deskripsi'       => 'Tidak ada parameterized queries / prepared statements — query dibangun dengan konkatenasi string (SQL Injection risk)',
                'kontrol_tipikal' => 'Sebagian query menggunakan raw SQL string concatenation dengan input pengguna langsung',
                'mitigasi_tipikal' => 'Refactor seluruh query menggunakan Eloquent ORM atau Laravel Query Builder dengan parameter binding; larang DB::statement() dengan interpolasi variabel; lakukan SAST scan; tambahkan ke coding standard dan checklist code review',
            ],
            [
                'nomor_urut'      => 15,
                'deskripsi'       => 'Tidak ada sanitasi input untuk mencegah command injection (OS Command, LDAP, XPath Injection)',
                'kontrol_tipikal' => 'Aplikasi mungkin meneruskan input pengguna ke fungsi shell atau query non-SQL tanpa sanitasi',
                'mitigasi_tipikal' => 'Hindari exec(), shell_exec(), system(), passthru() dengan input pengguna; jika mutlak diperlukan gunakan escapeshellarg(); jalankan PHP dengan privilege minimal; audit codebase untuk penggunaan fungsi shell; gunakan library parameterized untuk LDAP/XPath',
            ],
        ];

        $this->insertItems($setGlobalPL, $globalPLItems);

        // --- Spesifik Web-based v1.0 ---
        $setWebBased = (string) Str::uuid();
        DB::table('vulnerability_sets')->insertOrIgnore([
            'id'                => $setWebBased,
            'scope_type'        => 'subclass',
            'scope_id'          => $subclassIds['PL-WEB'],
            'versi'             => '1.0',
            'is_active'         => true,
            'catatan_perubahan' => 'Versi awal — kerawanan spesifik aplikasi berbasis web.',
            'published_at'      => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $webItems = [
            [
                'nomor_urut'      => 1,
                'deskripsi'       => 'Verifikasi kredensial hanya dilakukan di sisi klien (JavaScript/frontend) tanpa validasi ulang di server-side',
                'kontrol_tipikal' => 'Validasi form login menggunakan JavaScript sisi klien; server hanya menerima data tanpa re-validasi',
                'mitigasi_tipikal' => 'Pindahkan seluruh logika autentikasi ke server-side; client-side validation hanya untuk UX; implementasi middleware autentikasi Laravel (Fortify); lakukan penetration test untuk verifikasi tidak ada bypass',
            ],
            [
                'nomor_urut'      => 2,
                'deskripsi'       => 'Proses autentikasi tidak menggunakan TLS/SSL — kredensial dikirim dalam plaintext dan dapat disadap (sniffing)',
                'kontrol_tipikal' => 'Aplikasi dapat diakses melalui HTTP; belum ada redirect paksa ke HTTPS',
                'mitigasi_tipikal' => 'Pasang sertifikat TLS 1.2/1.3; redirect HTTP → HTTPS wajib di Nginx; aktifkan HSTS header (max-age=31536000; includeSubDomains); nonaktifkan TLS 1.0/1.1 dan cipher suite lemah; verifikasi SSL Labs ≥ Grade A',
            ],
            [
                'nomor_urut'      => 3,
                'deskripsi'       => 'Tidak ada mekanisme rate limiting dan account lockout untuk mencegah serangan brute force pada endpoint login',
                'kontrol_tipikal' => 'Endpoint login menerima percobaan tanpa batas tanpa delay, CAPTCHA, atau penguncian akun',
                'mitigasi_tipikal' => 'Account lockout: 5 percobaan gagal → kunci 30 menit; rate limiting Laravel throttle middleware (maks 5 req/menit per IP); progressive delay setelah 3 percobaan; log dan alert otomatis pola brute force massal; implementasi MFA (TOTP)',
            ],
            [
                'nomor_urut'      => 4,
                'deskripsi'       => 'Tidak ada mekanisme deteksi dan pencegahan serangan DoS/DDoS pada endpoint publik aplikasi web',
                'kontrol_tipikal' => 'Endpoint publik menerima request tanpa pembatasan; tidak ada proteksi anti-DDoS',
                'mitigasi_tipikal' => 'Rate limiting di level aplikasi (Laravel throttle) dan level jaringan (firewall/IPS); rencana failover ke saluran alternatif saat portal down; monitoring uptime; SLA terdokumentasi',
            ],
            [
                'nomor_urut'      => 5,
                'deskripsi'       => 'Session ID tidak divalidasi dan tidak di-associate dengan atribut klien (IP address/user agent) sehingga session token yang dicuri dapat digunakan dari lokasi berbeda',
                'kontrol_tipikal' => 'Sesi valid dari IP dan browser manapun tanpa validasi atribut klien',
                'mitigasi_tipikal' => 'Simpan fingerprint sesi (user agent, hash IP) saat login; invalidasi sesi jika fingerprint berubah drastis; implementasi sebagai middleware Laravel; catat anomali ke audit log',
            ],
            [
                'nomor_urut'      => 6,
                'deskripsi'       => 'Session token dikirim melalui URL parameter (misal ?session_id=xxx) — rentan terhadap session hijacking via Referer header, browser history, dan server log',
                'kontrol_tipikal' => 'Session ID mungkin disertakan di URL pada beberapa endpoint atau link yang dibuat sistem',
                'mitigasi_tipikal' => 'Pastikan session ID hanya dikirim melalui cookie (bukan URL); konfigurasi session.php: same_site=Strict, http_only=true, secure=true; nonaktifkan session.use_trans_sid di PHP; audit seluruh link dan redirect aplikasi',
            ],
            [
                'nomor_urut'      => 7,
                'deskripsi'       => 'Antarmuka admin dapat diakses langsung dari internet tanpa pembatasan IP (whitelisting) dan tanpa MFA',
                'kontrol_tipikal' => 'Admin panel hanya dilindungi username dan password biasa; tidak ada pembatasan akses berdasarkan jaringan',
                'mitigasi_tipikal' => 'Batasi akses route admin hanya dari IP internal via middleware IP whitelist; wajibkan MFA (TOTP) untuk semua akun Admin dan Super Admin; pertimbangkan path admin tidak mudah ditebak; alert jika ada percobaan akses admin dari IP asing',
            ],
            [
                'nomor_urut'      => 8,
                'deskripsi'       => 'Tidak ada output encoding saat menampilkan data yang diinput pengguna ke halaman web — rentan terhadap stored/reflected XSS',
                'kontrol_tipikal' => 'Data dari database atau input pengguna ditampilkan langsung ke HTML tanpa encoding',
                'mitigasi_tipikal' => 'Pastikan seluruh output menggunakan Blade {{ }} (auto-escape); audit semua {!! !!} dan ganti kecuali benar-benar diperlukan; implementasi CSP header strict; validasi tidak ada penggunaan innerHTML tanpa sanitasi di JavaScript',
            ],
            [
                'nomor_urut'      => 9,
                'deskripsi'       => 'Tidak ada validasi file upload — tidak ada pengecekan ekstensi, MIME type/content-type, dan magic bytes',
                'kontrol_tipikal' => 'File upload hanya membatasi ukuran file tanpa validasi tipe konten',
                'mitigasi_tipikal' => 'Validasi berlapis: whitelist ekstensi (pdf, jpg, png); validasi MIME type server-side via finfo; validasi magic bytes; simpan file di luar document root; rename dengan UUID; nonaktifkan eksekusi script di direktori upload via Nginx; scan antivirus jika tersedia',
            ],
        ];

        $this->insertItems($setWebBased, $webItems);

        // --- Spesifik Mobile v1.0 ---
        $setMobile = (string) Str::uuid();
        DB::table('vulnerability_sets')->insertOrIgnore([
            'id'                => $setMobile,
            'scope_type'        => 'subclass',
            'scope_id'          => $subclassIds['PL-MOB'],
            'versi'             => '1.0',
            'is_active'         => true,
            'catatan_perubahan' => 'Versi awal — kerawanan spesifik aplikasi mobile (Android/iOS).',
            'published_at'      => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $mobileItems = [
            [
                'nomor_urut'      => 1,
                'deskripsi'       => 'Verifikasi kredensial dan logika bisnis kritis diimplementasikan di sisi aplikasi (APK/IPA) yang dapat di-reverse engineering',
                'kontrol_tipikal' => 'Validasi dilakukan di dalam kode aplikasi mobile tanpa verifikasi ulang di server',
                'mitigasi_tipikal' => 'Pindahkan seluruh logika autentikasi dan otorisasi ke API server-side; aplikasi mobile hanya mengirim request dan menerima response; terapkan obfuscation pada kode APK/IPA',
            ],
            [
                'nomor_urut'      => 2,
                'deskripsi'       => 'Data sensitif (token, kredensial, data pribadi) tersimpan di local storage tidak terenkripsi (SharedPreferences, SQLite tanpa enkripsi)',
                'kontrol_tipikal' => 'Data aplikasi disimpan di storage lokal tanpa enkripsi',
                'mitigasi_tipikal' => 'Gunakan Android Keystore / iOS Keychain untuk menyimpan token dan credential; enkripsi database lokal (SQLCipher untuk SQLite); jangan pernah simpan password di storage lokal; hapus data sensitif saat logout',
            ],
            [
                'nomor_urut'      => 3,
                'deskripsi'       => 'Hardcoded credentials, API key, atau endpoint sensitif tertanam di dalam kode atau resource aplikasi mobile',
                'kontrol_tipikal' => 'API key dan konfigurasi disimpan langsung di source code atau file konfigurasi yang terbundle dalam APK/IPA',
                'mitigasi_tipikal' => 'Pindahkan semua credential ke server-side; gunakan runtime configuration yang diambil dari server saat startup; terapkan secret scanning di pipeline CI/CD sebelum build; rotate API key jika sudah ter-expose',
            ],
            [
                'nomor_urut'      => 4,
                'deskripsi'       => 'Certificate pinning tidak diimplementasikan — aplikasi menerima sertifikat TLS apapun sehingga rentan terhadap MitM meski menggunakan HTTPS',
                'kontrol_tipikal' => 'Aplikasi mobile menggunakan TLS default tanpa validasi sertifikat tambahan',
                'mitigasi_tipikal' => 'Implementasi certificate pinning (pin public key, bukan sertifikat) dengan mekanisme backup pin dan update path yang jelas; uji dengan tools MitM (Burp Suite) untuk verifikasi; sertakan prosedur pin rotation saat sertifikat server diperbarui',
            ],
            [
                'nomor_urut'      => 5,
                'deskripsi'       => 'Android backup diaktifkan (android:allowBackup=true) — data aplikasi dapat diambil oleh penyerang yang memiliki akses fisik via adb backup',
                'kontrol_tipikal' => 'Konfigurasi AndroidManifest.xml menggunakan nilai default yang mengizinkan backup',
                'mitigasi_tipikal' => 'Set android:allowBackup="false" di AndroidManifest.xml; jika backup diperlukan gunakan Android Auto Backup dengan aturan exclude untuk data sensitif; uji dengan adb backup untuk verifikasi',
            ],
            [
                'nomor_urut'      => 6,
                'deskripsi'       => 'Screenshot tidak dinonaktifkan pada layar yang menampilkan data sensitif — data dapat bocor melalui screenshot atau app switcher preview',
                'kontrol_tipikal' => 'Tidak ada pembatasan screenshot pada layar sensitif (login, data pribadi, laporan)',
                'mitigasi_tipikal' => 'Terapkan FLAG_SECURE (Android) atau hideSnapshotFromRecents (iOS) pada Activity/Screen yang menampilkan data sensitif; uji pada mode task switcher untuk verifikasi preview tidak menampilkan data',
            ],
            [
                'nomor_urut'      => 7,
                'deskripsi'       => 'Tidak ada deteksi root/jailbreak — aplikasi berjalan normal di perangkat yang telah di-root/jailbreak sehingga data dapat diekstrak',
                'kontrol_tipikal' => 'Aplikasi tidak memeriksa kondisi keamanan perangkat sebelum berjalan',
                'mitigasi_tipikal' => 'Implementasi root/jailbreak detection saat startup; tampilkan peringatan dan batasi fungsi sensitif pada perangkat yang terdeteksi; gunakan library yang diperbarui secara berkala karena teknik bypass terus berkembang',
            ],
        ];

        $this->insertItems($setMobile, $mobileItems);

        // --- Spesifik Desktop v1.0 ---
        $setDesktop = (string) Str::uuid();
        DB::table('vulnerability_sets')->insertOrIgnore([
            'id'                => $setDesktop,
            'scope_type'        => 'subclass',
            'scope_id'          => $subclassIds['PL-DSK'],
            'versi'             => '1.0',
            'is_active'         => true,
            'catatan_perubahan' => 'Versi awal — kerawanan spesifik aplikasi desktop.',
            'published_at'      => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $desktopItems = [
            [
                'nomor_urut'      => 1,
                'deskripsi'       => 'Kredensial atau token autentikasi disimpan dalam file konfigurasi plaintext di filesystem lokal tanpa enkripsi',
                'kontrol_tipikal' => 'Konfigurasi aplikasi termasuk credential disimpan di file .ini, .conf, atau registry tanpa proteksi',
                'mitigasi_tipikal' => 'Gunakan OS credential store (Windows Credential Manager, macOS Keychain, Linux Secret Service); enkripsi file konfigurasi sensitif menggunakan DPAPI atau equivalent; jangan pernah log credential ke file log',
            ],
            [
                'nomor_urut'      => 2,
                'deskripsi'       => 'Binary aplikasi tidak diproteksi terhadap reverse engineering dan tampering — tidak ada code signing',
                'kontrol_tipikal' => 'Aplikasi didistribusikan tanpa digital signature atau mekanisme verifikasi integritas',
                'mitigasi_tipikal' => 'Terapkan code signing pada binary menggunakan sertifikat yang valid; implementasi integrity check saat startup; terapkan obfuscation pada bagian kode yang mengandung logika bisnis kritis',
            ],
            [
                'nomor_urut'      => 3,
                'deskripsi'       => 'Mekanisme auto-update tidak memverifikasi integritas dan keaslian paket update — rentan terhadap supply chain attack',
                'kontrol_tipikal' => 'Update diunduh dan diinstal tanpa verifikasi signature atau checksum',
                'mitigasi_tipikal' => 'Implementasi verifikasi signature digital pada setiap paket update sebelum instalasi; gunakan channel update yang terenkripsi (HTTPS); verifikasi checksum (SHA-256) setelah download; rollback otomatis jika verifikasi gagal',
            ],
            [
                'nomor_urut'      => 4,
                'deskripsi'       => 'Komunikasi IPC (Inter-Process Communication) antar komponen aplikasi tidak diamankan dan dapat dieksploitasi oleh proses lain',
                'kontrol_tipikal' => 'Komponen aplikasi berkomunikasi via IPC tanpa autentikasi atau enkripsi',
                'mitigasi_tipikal' => 'Terapkan autentikasi pada channel IPC; gunakan named pipe dengan ACL yang ketat; enkripsi data yang dikirim via IPC jika mengandung informasi sensitif; audit semua endpoint IPC yang terbuka',
            ],
            [
                'nomor_urut'      => 5,
                'deskripsi'       => 'Aplikasi berjalan dengan privilege berlebihan (misal: sebagai Administrator/root) melebihi kebutuhan fungsionalnya',
                'kontrol_tipikal' => 'Aplikasi dijalankan dengan privilege penuh tanpa mempertimbangkan principle of least privilege',
                'mitigasi_tipikal' => 'Implementasi privilege separation: jalankan komponen dengan privilege minimal yang dibutuhkan; gunakan UAC elevation hanya untuk operasi yang memerlukan; audit permission yang diminta aplikasi; pisahkan proses dengan privilege berbeda',
            ],
        ];

        $this->insertItems($setDesktop, $desktopItems);

        // --- Spesifik SaaS/Cloud v1.0 ---
        $setSaaS = (string) Str::uuid();
        DB::table('vulnerability_sets')->insertOrIgnore([
            'id'                => $setSaaS,
            'scope_type'        => 'subclass',
            'scope_id'          => $subclassIds['PL-SAS'],
            'versi'             => '1.0',
            'is_active'         => true,
            'catatan_perubahan' => 'Versi awal — kerawanan spesifik aset SaaS/Cloud pihak ketiga.',
            'published_at'      => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        $saasItems = [
            [
                'nomor_urut'      => 1,
                'deskripsi'       => 'Tidak ada Data Processing Agreement (DPA) atau perjanjian keamanan data yang mengikat dengan vendor SaaS',
                'kontrol_tipikal' => 'Penggunaan layanan SaaS hanya berdasarkan Terms of Service umum tanpa perjanjian pengolahan data khusus',
                'mitigasi_tipikal' => 'Negosiasikan dan tandatangani DPA dengan vendor; DPA harus mencakup: lokasi data, retensi, prosedur breach notification, hak audit, dan kepatuhan regulasi Indonesia (UU PDP); libatkan Biro Hukum Pemprov',
            ],
            [
                'nomor_urut'      => 2,
                'deskripsi'       => 'Data sensitif instansi (data pribadi warga, dokumen rahasia, anggaran) dimasukkan ke dalam platform SaaS pihak ketiga tanpa kebijakan penggunaan yang jelas',
                'kontrol_tipikal' => 'Pengguna menggunakan layanan SaaS tanpa panduan tentang jenis data yang boleh/tidak boleh diproses',
                'mitigasi_tipikal' => 'Buat kebijakan penggunaan SaaS: kategorikan data yang boleh diproses (publik/internal/rahasia); sosialisasikan ke seluruh pengguna; terapkan training awareness; bentuk mekanisme pelaporan jika ada pelanggaran',
            ],
            [
                'nomor_urut'      => 3,
                'deskripsi'       => 'Akun SaaS digunakan bersama (shared) oleh beberapa pengguna tanpa akun individual — tidak ada auditability aktivitas per pengguna',
                'kontrol_tipikal' => 'Satu akun SaaS digunakan bergantian oleh beberapa pegawai',
                'mitigasi_tipikal' => 'Alokasikan akun individual per pengguna jika plan memungkinkan; catat log penggunaan secara manual jika fitur audit log tidak tersedia; nonaktifkan akun segera saat pegawai pindah/keluar',
            ],
            [
                'nomor_urut'      => 4,
                'deskripsi'       => 'Tidak ada rencana kontinuitas jika layanan SaaS tidak tersedia (downtime vendor, penghentian layanan, perubahan kebijakan harga)',
                'kontrol_tipikal' => 'Ketergantungan penuh pada ketersediaan layanan vendor tanpa prosedur fallback',
                'mitigasi_tipikal' => 'Dokumentasikan proses kerja alternatif saat layanan SaaS tidak tersedia; identifikasi layanan pengganti; pastikan data dapat diekspor dari platform vendor (vendor lock-in mitigation); review SLA vendor secara berkala',
            ],
            [
                'nomor_urut'      => 5,
                'deskripsi'       => 'Tidak ada evaluasi kepatuhan keamanan vendor secara berkala — postur keamanan vendor tidak dipantau setelah kontrak ditandatangani',
                'kontrol_tipikal' => 'Evaluasi keamanan vendor hanya dilakukan saat pengadaan awal',
                'mitigasi_tipikal' => 'Jadwalkan review tahunan kepatuhan keamanan vendor: minta laporan audit (ISO 27001, SOC 2); pantau pengumuman breach dari vendor; evaluasi ulang saat ada perubahan signifikan pada layanan atau kebijakan vendor',
            ],
        ];

        $this->insertItems($setSaaS, $saasItems);
    }

    private function insertItems(string $setId, array $items): void
    {
        $chunks = array_chunk($items, 50);
        foreach ($chunks as $chunk) {
            $rows = array_map(fn($item) => array_merge($item, [
                'id'         => (string) Str::uuid(),
                'set_id'     => $setId,
                'created_at' => now(),
                'updated_at' => now(),
            ]), $chunk);
            DB::table('vulnerability_items')->insertOrIgnore($rows);
        }
    }
}
