<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KerawananPerangkatLunakSeeder extends Seeder
{
    const ID_PL     = 'a1b93053-cc1a-4612-8ed0-9a544388bbc5';
    const ID_PL_WEB = 'a1b8ffb3-40fb-48d5-8bec-aad7047294c7';
    const ID_PL_MOB = 'a1b93053-d042-4c47-93a0-09463c537c03';
    const ID_PL_DSK = 'a1b93053-d143-4772-be2a-9870b32e4678';
    const ID_PL_OS  = 'a1b93053-cfa4-46af-84a4-d4ee89be1390';
    const ID_PL_UTL = 'a1b93053-cfd2-46f8-8219-61bbb06f37fc';

    public function run(): void
    {
        // ── GLOBAL PERANGKAT LUNAK ─────────────────────────────────────────
        $setId = $this->createSet(
            'global_class',
            self::ID_PL,
            '1.0',
            'Versi awal — kerawanan umum berlaku untuk semua sub-kelas Perangkat Lunak.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Sistem tidak menerapkan aturan pembentukan kata sandi yang memadai — tidak ada batas panjang minimal, tidak wajib kombinasi karakter, dan tidak ada masa berlaku kata sandi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mencoba masuk ke sistem menggunakan kata sandi yang lemah, mudah ditebak, atau diperoleh dari kebocoran data di tempat lain',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Akun pengguna atau administrator berhasil diambil alih; data dan fungsi sistem disalahgunakan oleh pihak yang tidak berhak; reputasi organisasi tercoreng',
                'area_dampak'      => ['Reputasi', 'Keamanan Data', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Pengguna membuat kata sandi sendiri tanpa aturan teknis yang diterapkan di sistem; panduan kata sandi hanya bersifat imbauan',
                'mitigasi_tipikal' => 'Terapkan aturan kata sandi secara teknis: panjang minimal, kombinasi karakter wajib, masa berlaku maksimal, dan riwayat kata sandi agar tidak dapat digunakan ulang; tampilkan indikator kekuatan kata sandi',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Sistem tidak membatasi jumlah percobaan login yang gagal dan tidak mengunci akun secara otomatis setelah percobaan berlebihan',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mencoba masuk secara paksa dengan mencoba ribuan kombinasi kata sandi secara otomatis dan berulang dalam waktu singkat',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Akun pengguna berhasil dibobol tanpa terdeteksi; layanan terganggu akibat lonjakan percobaan login yang masif',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Tidak ada mekanisme pembatasan percobaan login yang diterapkan secara teknis; sistem menerima percobaan tanpa batas',
                'mitigasi_tipikal' => 'Terapkan penguncian akun sementara setelah sejumlah percobaan gagal; batasi frekuensi permintaan login per pengguna dan per jaringan; wajibkan verifikasi tambahan (MFA)',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Sistem menyimpan kata sandi pengguna dalam format yang tidak terlindungi atau menggunakan metode perlindungan yang sudah tidak aman dan mudah dipecahkan',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang berhasil mengakses basis data sistem dapat langsung memperoleh dan menggunakan kata sandi seluruh pengguna tanpa upaya tambahan',
                'kategori'         => 'Pencurian Data Pribadi',
                'dampak_tipikal'   => 'Seluruh akun pengguna terancam sekaligus saat terjadi kebocoran data; penyerang dapat masuk ke sistem dan layanan lain yang menggunakan kata sandi yang sama',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Kata sandi disimpan menggunakan mekanisme bawaan sistem tanpa audit keamanan berkala terhadap metode penyimpanan',
                'mitigasi_tipikal' => 'Simpan kata sandi menggunakan algoritma hashing yang kuat dan modern; audit berkala terhadap metode penyimpanan; paksa penggantian kata sandi seluruh pengguna jika metode lama terdeteksi',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Mekanisme pemulihan kata sandi tidak aman — tautan pemulihan tidak memiliki batas waktu, dapat digunakan berulang kali, atau prosesnya memberikan petunjuk tentang keberadaan suatu akun',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mengambil alih akun pengguna melalui jalur pemulihan kata sandi yang seharusnya hanya bersifat darurat',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Akun pengguna diambil alih tanpa sepengetahuan pemiliknya; seluruh data dalam akun tersebut dapat diakses dan disalahgunakan',
                'area_dampak'      => ['Operasional TIK', 'Reputasi'],
                'kontrol_tipikal'  => 'Fitur pemulihan kata sandi tersedia namun tanpa pembatasan waktu berlaku tautan dan frekuensi permintaan',
                'mitigasi_tipikal' => 'Batasi masa berlaku tautan pemulihan; pastikan tautan hanya dapat digunakan satu kali; batasi frekuensi permintaan per akun; kirim notifikasi ke pemilik akun setiap ada permintaan pemulihan',
            ],
            [
                'nomor_urut'       => 5,
                'deskripsi'        => 'Sesi pengguna tidak berakhir secara otomatis saat tidak aktif dan tidak benar-benar dihapus di server saat pengguna keluar dari sistem',
                'ancaman_tipikal'  => 'Pihak lain yang memiliki akses ke perangkat atau jaringan yang sama memanfaatkan sesi yang masih aktif untuk masuk ke sistem tanpa perlu login ulang',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Perangkat yang ditinggalkan atau dicuri memungkinkan pihak lain mengakses sistem sepenuhnya atas nama pengguna yang sah',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Sesi berakhir hanya saat pengguna menutup aplikasi di sisi perangkat tanpa konfirmasi penghapusan dari server',
                'mitigasi_tipikal' => 'Terapkan batas waktu sesi otomatis untuk kondisi tidak aktif dan batas waktu absolut; pastikan server menghapus sesi secara permanen saat logout atau ganti kata sandi; batasi satu sesi aktif per pengguna',
            ],
            [
                'nomor_urut'       => 6,
                'deskripsi'        => 'Token sesi pengguna dapat ditebak, disadap, atau digunakan ulang karena tidak diperbarui setelah proses login berhasil dan tidak memiliki tingkat keacakan yang memadai',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mencuri atau menebak token sesi pengguna yang sedang aktif untuk mengambil alih sesi tanpa perlu mengetahui kata sandi',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Pihak lain dapat menggunakan sistem sepenuhnya atas nama pengguna yang sah; seluruh aktivitas tercatat atas nama pengguna korban',
                'area_dampak'      => ['Operasional TIK', 'Reputasi'],
                'kontrol_tipikal'  => 'Token sesi dihasilkan oleh mekanisme bawaan sistem tanpa audit terhadap kualitas keacakan dan masa berlaku',
                'mitigasi_tipikal' => 'Pastikan token sesi diperbarui setiap kali pengguna berhasil login; gunakan mekanisme pembangkit token yang tidak dapat ditebak; simpan catatan sesi di server agar dapat dicabut kapan saja',
            ],
            [
                'nomor_urut'       => 7,
                'deskripsi'        => 'Sistem tidak memeriksa secara konsisten apakah pengguna memiliki hak akses yang sesuai sebelum menjalankan setiap fungsi atau menampilkan data tertentu',
                'ancaman_tipikal'  => 'Pengguna terdaftar dengan hak terbatas, atau pihak luar yang memanfaatkan celah, berupaya mengakses fungsi dan data yang bukan kewenangannya',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Data milik pengguna atau unit lain dapat dilihat, diubah, atau dihapus; fungsi administratif dijalankan oleh pihak yang tidak berwenang',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Pembatasan akses hanya terlihat di tampilan antarmuka pengguna tanpa pemeriksaan ulang di sisi server pada setiap permintaan',
                'mitigasi_tipikal' => 'Terapkan pemeriksaan hak akses di sisi server untuk setiap fungsi dan data secara konsisten; pastikan setiap pengguna hanya dapat mengakses data yang menjadi kewenangannya; terapkan prinsip hak akses minimal',
            ],
            [
                'nomor_urut'       => 8,
                'deskripsi'        => 'Sistem tidak memeriksa dan membersihkan data yang dimasukkan pengguna sebelum diproses, sehingga data berbahaya yang disisipkan dapat dijalankan oleh sistem sebagai instruksi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang menyisipkan perintah berbahaya melalui kolom input sistem untuk memanipulasi data, mencuri informasi, atau mengendalikan sistem dari jarak jauh',
                'kategori'         => 'Ketidaksesuaian Pengelolaan Aplikasi',
                'dampak_tipikal'   => 'Data sistem dapat dicuri, diubah, atau dihapus secara massal; sistem dapat dikendalikan sepenuhnya oleh pihak luar; layanan tidak dapat diakses',
                'area_dampak'      => ['Operasional TIK', 'Kinerja', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Pemeriksaan data masukan hanya dilakukan di sisi tampilan antarmuka tanpa pemeriksaan ulang di sisi server sebelum data diproses',
                'mitigasi_tipikal' => 'Terapkan pemeriksaan data masukan secara ketat di sisi server untuk setiap fungsi yang menerima input; definisikan format yang diizinkan dan tolak semua yang tidak sesuai; pisahkan data dari instruksi sistem',
            ],
            [
                'nomor_urut'       => 9,
                'deskripsi'        => 'Sistem menampilkan informasi teknis kepada pengguna saat terjadi kesalahan, atau menyimpan informasi sensitif di catatan sistem yang tidak dilindungi dengan baik',
                'ancaman_tipikal'  => 'Pihak luar melakukan pengintaian terhadap sistem untuk mengumpulkan informasi teknis yang dapat dimanfaatkan untuk melancarkan serangan lebih terarah',
                'kategori'         => 'Ketidaksesuaian Pengelolaan Aplikasi',
                'dampak_tipikal'   => 'Pihak tidak berwenang memperoleh gambaran rinci tentang struktur dan kelemahan sistem yang dimanfaatkan untuk merancang serangan lebih tepat sasaran',
                'area_dampak'      => ['Operasional TIK', 'Keamanan Infrastruktur'],
                'kontrol_tipikal'  => 'Sistem menampilkan pesan kesalahan bawaan yang mengandung detail teknis; catatan sistem belum dilindungi dengan kebijakan akses yang memadai',
                'mitigasi_tipikal' => 'Tampilkan hanya pesan kesalahan generik kepada pengguna; simpan detail teknis hanya di catatan sistem yang terproteksi dan hanya dapat diakses administrator; terapkan kebijakan retensi catatan sistem',
            ],
            [
                'nomor_urut'       => 10,
                'deskripsi'        => 'Komponen atau layanan pendukung yang digunakan sistem tidak diperbarui secara berkala sehingga kelemahan yang sudah diketahui publik belum ditangani',
                'ancaman_tipikal'  => 'Pihak tidak berwenang memanfaatkan kelemahan yang sudah diketahui publik pada komponen pendukung sistem yang belum mendapat pembaruan',
                'kategori'         => 'Ketidaksesuaian Pengelolaan Aplikasi',
                'dampak_tipikal'   => 'Sistem dapat disusupi melalui celah yang sebetulnya sudah ada solusinya; data dicuri atau layanan terganggu meskipun komponen inti sudah dirancang dengan baik',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Pembaruan komponen pendukung dilakukan secara manual tanpa jadwal yang teratur dan tanpa pemantauan pengumuman kelemahan',
                'mitigasi_tipikal' => 'Tetapkan jadwal pembaruan komponen pendukung secara berkala; pantau pengumuman kelemahan dari sumber resmi; terapkan prosedur pembaruan darurat untuk kelemahan dengan tingkat keparahan tinggi',
            ],
            [
                'nomor_urut'       => 11,
                'deskripsi'        => 'Sistem tidak mencatat aktivitas pengguna secara menyeluruh dan tidak memiliki mekanisme pendeteksian perilaku yang tidak wajar',
                'ancaman_tipikal'  => 'Pihak luar yang menyusup ke dalam sistem, atau investigator eksternal yang menyelidiki insiden, tidak dapat melacak aktivitas yang terjadi karena catatan tidak tersedia atau tidak lengkap',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Data dimanipulasi, dihapus, atau dibocorkan oleh pihak internal; penyalahgunaan sulit dibuktikan karena tidak ada jejak aktivitas yang memadai',
                'area_dampak'      => ['Kinerja', 'Hukum dan Regulasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Pencatatan aktivitas hanya tersedia pada fungsi tertentu tanpa cakupan menyeluruh; tidak ada proses tinjauan berkala',
                'mitigasi_tipikal' => 'Terapkan pencatatan aktivitas yang menyeluruh dan tidak dapat diubah untuk semua operasi penting; terapkan pemisahan tugas; lakukan tinjauan berkala terhadap catatan aktivitas',
            ],
            [
                'nomor_urut'       => 12,
                'deskripsi'        => 'Tidak ada prosedur pencadangan data yang teratur dan teruji, serta tidak ada rencana pemulihan yang terdokumentasi jika sistem mengalami kegagalan',
                'ancaman_tipikal'  => 'Kejadian tidak terduga seperti kerusakan perangkat, serangan siber, kesalahan operasional, atau bencana alam mengancam ketersediaan dan keutuhan seluruh data sistem',
                'kategori'         => 'Terganggunya Keberlangsungan Layanan',
                'dampak_tipikal'   => 'Data operasional hilang permanen; layanan terhenti dalam waktu lama; kewajiban hukum penyimpanan data tidak dapat dipenuhi; kerugian operasional yang signifikan',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Hukum dan Regulasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Pencadangan dilakukan secara manual tanpa jadwal yang pasti; belum pernah dilakukan uji pemulihan untuk memastikan data cadangan dapat digunakan',
                'mitigasi_tipikal' => 'Terapkan pencadangan otomatis secara berkala ke lokasi yang terpisah; uji pemulihan data secara berkala; tetapkan target waktu dan titik pemulihan yang realistis; dokumentasikan prosedur pemulihan bencana',
            ],
        ]);

        // ── SPESIFIK: APLIKASI WEB ─────────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PL_WEB,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik aplikasi berbasis website.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Seluruh proses verifikasi identitas pengguna hanya dilakukan di sisi perangkat pengguna tanpa konfirmasi ulang dari server, sehingga dapat dilewati dengan memodifikasi permintaan secara langsung',
                'ancaman_tipikal'  => 'Pihak tidak berwenang melewati halaman login dengan memanipulasi data yang dikirimkan ke server sehingga proses verifikasi yang sebenarnya tidak pernah dijalankan',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Pihak tidak berwenang berhasil masuk ke sistem tanpa kredensial yang valid; seluruh fungsi dan data sistem dapat diakses dan disalahgunakan',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Validasi formulir login dilakukan menggunakan skrip di sisi perangkat pengguna; server menerima data tanpa melakukan verifikasi ulang secara mandiri',
                'mitigasi_tipikal' => 'Pindahkan seluruh proses verifikasi identitas ke sisi server; pemeriksaan di sisi perangkat pengguna hanya untuk kenyamanan tampilan bukan keamanan; setiap permintaan ke server diverifikasi ulang sebelum diproses',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Komunikasi antara perangkat pengguna dan server tidak dienkripsi sehingga seluruh data yang dikirim dan diterima dapat dibaca oleh pihak lain yang berada di jalur komunikasi yang sama',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang berada di jaringan yang sama menyadap lalu lintas data antara pengguna dan server untuk mencuri informasi login dan data sensitif lainnya',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Kata sandi, token sesi, dan data sensitif dicuri saat dikirimkan melalui jaringan; akun pengguna diambil alih menggunakan informasi yang berhasil disadap',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi', 'Reputasi'],
                'kontrol_tipikal'  => 'Sistem dapat diakses melalui protokol komunikasi yang tidak terenkripsi; belum ada pengalihan otomatis ke protokol terenkripsi',
                'mitigasi_tipikal' => 'Aktifkan enkripsi untuk seluruh komunikasi antara perangkat pengguna dan server; terapkan pengalihan otomatis dari protokol tidak terenkripsi; nonaktifkan versi protokol enkripsi yang sudah usang; perbarui sertifikat sebelum kedaluwarsa',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Halaman dan fungsi administrasi sistem dapat diakses langsung dari internet tanpa pembatasan berdasarkan lokasi jaringan dan tanpa lapisan verifikasi tambahan',
                'ancaman_tipikal'  => 'Pihak tidak berwenang dari mana saja di internet mencoba mengakses halaman administrasi sistem untuk mengambil alih kendali penuh',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Seluruh konfigurasi sistem, data pengguna, dan fungsi administrasi dapat diakses dan dimanipulasi; dampaknya jauh lebih parah dibanding pembobolan akun pengguna biasa',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Halaman administrasi hanya dilindungi oleh nama pengguna dan kata sandi tanpa pembatasan akses berdasarkan jaringan',
                'mitigasi_tipikal' => 'Batasi akses ke halaman administrasi hanya dari jaringan internal yang terpercaya; wajibkan verifikasi tambahan (MFA) untuk semua akun administrasi; tempatkan antarmuka administrasi di jalur yang tidak mudah ditemukan dari internet',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Sistem tidak membatasi volume permintaan yang masuk sehingga layanan dapat dilumpuhkan melalui pengiriman permintaan dalam jumlah sangat besar secara bersamaan',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mengirimkan permintaan dalam jumlah sangat besar secara bersamaan untuk membuat sistem tidak mampu melayani pengguna yang sah',
                'kategori'         => 'Terganggunya Keberlangsungan Layanan',
                'dampak_tipikal'   => 'Layanan tidak dapat diakses oleh pengguna yang sah; proses operasional yang bergantung pada sistem terhenti; kepercayaan publik terhadap keandalan layanan menurun',
                'area_dampak'      => ['Layanan Organisasi', 'Reputasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Tidak ada pembatasan volume permintaan yang masuk; infrastruktur tidak dirancang untuk menghadapi lonjakan permintaan yang tidak wajar',
                'mitigasi_tipikal' => 'Terapkan pembatasan volume permintaan di tingkat aplikasi dan jaringan; siapkan saluran layanan alternatif saat sistem utama tidak dapat diakses; dokumentasikan prosedur penanganan gangguan layanan',
            ],
            [
                'nomor_urut'       => 5,
                'deskripsi'        => 'Sistem tidak memverifikasi bahwa pengguna yang mengakses suatu data atau menjalankan suatu fungsi adalah pemilik yang sah dari data atau fungsi tersebut',
                'ancaman_tipikal'  => 'Pengguna terdaftar memanipulasi parameter pada tautan atau formulir untuk mengakses atau mengubah data milik pengguna atau unit lain yang bukan haknya',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Data milik pengguna atau instansi lain dapat dilihat, diubah, atau dihapus; privasi dan kerahasiaan data lintas unit terganggu; berpotensi melanggar regulasi perlindungan data pribadi',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Sistem mengambil data berdasarkan parameter yang dikirim pengguna tanpa memverifikasi apakah pengguna berhak atas data dimaksud',
                'mitigasi_tipikal' => 'Terapkan pemeriksaan kepemilikan data di sisi server untuk setiap permintaan akses; gunakan pengenal data yang tidak mudah ditebak; pastikan pengguna hanya dapat mengakses data yang menjadi haknya',
            ],
            [
                'nomor_urut'       => 6,
                'deskripsi'        => 'Konten yang ditampilkan di halaman web tidak dibersihkan dengan benar sehingga skrip berbahaya yang dimasukkan melalui kolom input dapat dijalankan di perangkat pengguna lain',
                'ancaman_tipikal'  => 'Pihak tidak berwenang menyisipkan skrip berbahaya melalui kolom input yang kemudian dijalankan secara otomatis di perangkat pengguna lain yang membuka halaman tersebut',
                'kategori'         => 'Insiden Web Defacement',
                'dampak_tipikal'   => 'Sesi pengguna lain dicuri tanpa sepengetahuan korban; pengguna diarahkan ke situs palsu; tampilan sistem dirusak; data yang ditampilkan di perangkat korban dicuri',
                'area_dampak'      => ['Reputasi', 'Operasional TIK', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Data yang dimasukkan pengguna ditampilkan kembali di halaman web tanpa proses pembersihan atau pengkodean karakter yang memadai',
                'mitigasi_tipikal' => 'Terapkan pengkodean otomatis untuk seluruh data yang ditampilkan di halaman web; audit seluruh titik tampilan data; terapkan kebijakan keamanan konten yang membatasi sumber skrip yang diizinkan',
            ],
            [
                'nomor_urut'       => 7,
                'deskripsi'        => 'Sistem tidak memvalidasi jenis, ukuran, dan isi berkas yang diunggah oleh pengguna sehingga berkas berbahaya dapat masuk ke server dan berpotensi dieksekusi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mengunggah berkas berbahaya yang menyamar sebagai dokumen biasa untuk mendapatkan akses atau kendali atas server',
                'kategori'         => 'Insiden Serangan Malware',
                'dampak_tipikal'   => 'Server dapat dikendalikan sepenuhnya oleh pihak luar; berkas berbahaya disebarkan kepada pengguna lain; seluruh data di server dapat diakses, dimodifikasi, atau dihapus',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Unggahan berkas hanya dibatasi dari sisi ukuran tanpa pemeriksaan terhadap jenis dan isi berkas yang sesungguhnya',
                'mitigasi_tipikal' => 'Terapkan pemeriksaan berlapis: jenis berkas yang diizinkan, kesesuaian nama dan isi berkas, ukuran maksimal; simpan berkas di lokasi yang tidak dapat diakses langsung melalui browser; berikan nama berkas secara acak',
            ],
            [
                'nomor_urut'       => 8,
                'deskripsi'        => 'Token sesi dikirimkan melalui alamat URL halaman web sehingga dapat bocor melalui riwayat browser, catatan server, atau tautan yang tidak sengaja dibagikan pengguna',
                'ancaman_tipikal'  => 'Pihak tidak berwenang memperoleh token sesi aktif dari riwayat browser atau catatan server untuk mengambil alih sesi yang masih berlaku',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Sesi pengguna diambil alih tanpa perlu mengetahui kata sandi; seluruh aktivitas dalam sesi tersebut dilakukan atas nama pengguna korban',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Token sesi kemungkinan disertakan dalam alamat URL pada beberapa tautan atau pengalihan halaman yang dibuat sistem',
                'mitigasi_tipikal' => 'Pastikan token sesi hanya dikirimkan melalui cookie yang terproteksi, tidak pernah melalui alamat URL; konfigurasi cookie sesi agar tidak dapat diakses skrip halaman dan hanya dikirim melalui koneksi terenkripsi',
            ],
            [
                'nomor_urut'       => 9,
                'deskripsi'        => 'Sistem tidak memverifikasi bahwa permintaan yang masuk benar-benar disengaja oleh pengguna yang terautentikasi, sehingga tindakan dapat dipicu tanpa sepengetahuan pengguna melalui halaman lain',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mengelabui pengguna yang sedang login untuk secara tidak sadar menjalankan tindakan berbahaya melalui tautan atau halaman yang telah disiapkan',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Tindakan tidak sah dilakukan atas nama pengguna yang sah tanpa sepengetahuannya: perubahan data, penghapusan, atau transaksi yang tidak pernah dimaksudkan pengguna',
                'area_dampak'      => ['Operasional TIK', 'Kinerja'],
                'kontrol_tipikal'  => 'Sistem memproses setiap permintaan dari sesi pengguna yang login tanpa memverifikasi apakah permintaan tersebut benar-benar disengaja pengguna',
                'mitigasi_tipikal' => 'Sertakan token unik yang tidak dapat ditebak pada setiap formulir yang mengubah data; verifikasi token di sisi server sebelum memproses permintaan; terapkan konfirmasi tambahan untuk tindakan yang kritis',
            ],
        ]);

        // ── SPESIFIK: MOBILE ───────────────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PL_MOB,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik aplikasi berbasis mobile (Android/iOS).'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Logika verifikasi identitas dan aturan bisnis kritis diimplementasikan sepenuhnya di dalam paket aplikasi mobile yang dapat dianalisis dan dimodifikasi oleh pihak luar',
                'ancaman_tipikal'  => 'Pihak tidak berwenang menganalisis paket aplikasi untuk memahami cara kerja verifikasi, lalu memodifikasinya untuk melewati proses autentikasi',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Pihak tidak berwenang berhasil mengakses sistem tanpa melalui proses verifikasi yang sah; kontrol akses yang ada menjadi tidak bermakna',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Validasi dilakukan di dalam kode aplikasi mobile tanpa verifikasi ulang di server untuk setiap tindakan yang dilakukan',
                'mitigasi_tipikal' => 'Pindahkan seluruh logika verifikasi ke sisi server; aplikasi mobile hanya mengirim permintaan dan menerima respons; terapkan pengamanan terhadap kode aplikasi agar tidak mudah dianalisis',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Data sensitif seperti token akses, kata sandi, dan informasi pengguna disimpan di penyimpanan lokal perangkat tanpa enkripsi yang memadai',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang memiliki akses fisik ke perangkat atau menggunakan perangkat lunak khusus mengekstrak data sensitif dari penyimpanan lokal aplikasi',
                'kategori'         => 'Pencurian Data Pribadi',
                'dampak_tipikal'   => 'Token akses dan data pengguna dicuri; pihak tidak berwenang dapat mengakses sistem dan layanan lain menggunakan data yang berhasil diekstrak',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Data aplikasi disimpan di penyimpanan lokal perangkat tanpa enkripsi; tidak ada pemisahan antara data sensitif dan tidak sensitif',
                'mitigasi_tipikal' => 'Gunakan fasilitas penyimpanan aman yang disediakan sistem operasi perangkat untuk menyimpan data sensitif; enkripsi seluruh data yang disimpan secara lokal; hapus data sensitif dari penyimpanan lokal saat pengguna logout',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Informasi rahasia seperti kunci akses API, kata sandi layanan, atau alamat server sensitif tertanam langsung di dalam kode atau berkas konfigurasi aplikasi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang menganalisis paket aplikasi yang dapat diunduh publik untuk mengekstrak informasi rahasia yang tertanam di dalamnya',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Kunci akses dan kredensial layanan diperoleh pihak luar; layanan backend diakses secara tidak sah; potensi penyalahgunaan yang sulit dideteksi',
                'area_dampak'      => ['Operasional TIK', 'Keamanan Infrastruktur'],
                'kontrol_tipikal'  => 'Kunci akses dan konfigurasi disimpan langsung di kode sumber yang terbundle dalam paket aplikasi',
                'mitigasi_tipikal' => 'Pindahkan semua kredensial ke server; gunakan konfigurasi yang diambil dari server saat aplikasi pertama berjalan; terapkan pemindaian otomatis untuk mendeteksi informasi rahasia sebelum aplikasi dirilis',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Aplikasi tidak memverifikasi keaslian sertifikat server saat berkomunikasi, sehingga tidak dapat mendeteksi upaya penyadapan meskipun menggunakan protokol terenkripsi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang menempatkan diri di antara aplikasi dan server dengan menggunakan sertifikat palsu untuk menyadap dan memanipulasi seluruh komunikasi',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Seluruh komunikasi antara aplikasi dan server dapat dibaca dan dimanipulasi meskipun terlihat terenkripsi; data sensitif pengguna dicuri dalam perjalanan',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Aplikasi menggunakan enkripsi standar tanpa mekanisme verifikasi tambahan terhadap keaslian sertifikat server',
                'mitigasi_tipikal' => 'Terapkan verifikasi keaslian sertifikat server dengan membandingkan terhadap daftar sertifikat yang diizinkan; siapkan mekanisme pembaruan daftar sertifikat tanpa harus merilis versi aplikasi baru',
            ],
            [
                'nomor_urut'       => 5,
                'deskripsi'        => 'Aplikasi tidak mendeteksi dan tidak membatasi pengoperasian pada perangkat yang telah dimodifikasi sehingga mekanisme keamanan sistem operasi tidak lagi dapat diandalkan',
                'ancaman_tipikal'  => 'Pengguna atau pihak lain menjalankan aplikasi pada perangkat yang telah dimodifikasi untuk melewati pembatasan sistem operasi dan mengekstrak data yang seharusnya terlindungi',
                'kategori'         => 'Ketidaksesuaian Pengelolaan Aplikasi',
                'dampak_tipikal'   => 'Data sensitif yang tersimpan di aplikasi dapat diekstrak dengan lebih mudah; mekanisme perlindungan yang bergantung pada keamanan sistem operasi menjadi tidak efektif',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Aplikasi berjalan normal pada semua perangkat tanpa memeriksa kondisi keamanan perangkat terlebih dahulu',
                'mitigasi_tipikal' => 'Terapkan deteksi modifikasi perangkat saat aplikasi pertama berjalan; tampilkan peringatan dan batasi akses ke fungsi sensitif pada perangkat yang terdeteksi telah dimodifikasi; perbarui mekanisme deteksi secara berkala',
            ],
        ]);

        // ── SPESIFIK: DESKTOP ──────────────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PL_DSK,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik aplikasi berbasis desktop.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Kredensial, token akses, atau konfigurasi sensitif disimpan dalam berkas di sistem operasi tanpa enkripsi dan tanpa pembatasan akses yang memadai',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang memiliki akses ke komputer pengguna membaca berkas konfigurasi untuk mendapatkan kredensial dan mengakses sistem menggunakan identitas pengguna yang sah',
                'kategori'         => 'Pencurian Data Pribadi',
                'dampak_tipikal'   => 'Kredensial dan token akses dicuri; sistem dan layanan yang menggunakan kredensial tersebut dapat diakses secara tidak sah tanpa sepengetahuan pengguna',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Konfigurasi aplikasi termasuk kredensial disimpan dalam berkas teks biasa di direktori aplikasi tanpa proteksi tambahan',
                'mitigasi_tipikal' => 'Gunakan fasilitas penyimpanan kredensial yang disediakan sistem operasi; enkripsi seluruh berkas konfigurasi yang mengandung informasi sensitif; terapkan pembatasan akses berkas sehingga hanya proses yang berwenang yang dapat membacanya',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Berkas instalasi atau pembaruan aplikasi tidak dilengkapi dengan tanda tangan digital sehingga keaslian dan integritasnya tidak dapat diverifikasi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mendistribusikan versi aplikasi yang telah dimodifikasi kepada pengguna yang mengira sedang menginstal atau memperbarui aplikasi yang sah',
                'kategori'         => 'Insiden Serangan Malware',
                'dampak_tipikal'   => 'Pengguna menginstal aplikasi yang mengandung komponen berbahaya; sistem komputer pengguna dapat dikendalikan dari jarak jauh; data yang diproses aplikasi dapat dicuri',
                'area_dampak'      => ['Operasional TIK', 'Reputasi', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Aplikasi didistribusikan tanpa tanda tangan digital; tidak ada mekanisme bagi pengguna untuk memverifikasi keaslian berkas instalasi',
                'mitigasi_tipikal' => 'Terapkan tanda tangan digital pada setiap berkas instalasi dan pembaruan; sediakan mekanisme verifikasi yang mudah bagi pengguna; distribusikan aplikasi melalui saluran resmi yang terverifikasi',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Aplikasi dijalankan dengan hak akses yang melebihi kebutuhan fungsionalnya sehingga jika dieksploitasi dapat memberikan dampak yang lebih luas',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang berhasil mengeksploitasi celah pada aplikasi dapat memanfaatkan hak akses berlebih yang dimiliki aplikasi untuk melakukan tindakan yang lebih merusak',
                'kategori'         => 'Ketidaksesuaian Pengelolaan Aplikasi',
                'dampak_tipikal'   => 'Eksploitasi pada aplikasi berdampak lebih luas; penyerang dapat mengakses dan memodifikasi berkas sistem, menginstal perangkat lunak berbahaya, atau mengubah konfigurasi sistem',
                'area_dampak'      => ['Operasional TIK', 'Kinerja'],
                'kontrol_tipikal'  => 'Aplikasi dijalankan dengan hak akses administrator atau penuh tanpa pertimbangan terhadap prinsip hak akses minimal',
                'mitigasi_tipikal' => 'Jalankan aplikasi dengan hak akses minimal yang dibutuhkan untuk berfungsi; pisahkan komponen yang memerlukan hak akses tinggi; minta hak akses tambahan hanya saat diperlukan',
            ],
        ]);

        // ── SPESIFIK: SISTEM OPERASI ───────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PL_OS,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik aset Sistem Operasi.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Sistem operasi tidak mendapat pembaruan keamanan secara berkala sehingga celah yang sudah diketahui publik tetap terbuka',
                'ancaman_tipikal'  => 'Pihak tidak berwenang memanfaatkan celah keamanan yang sudah diketahui publik pada sistem operasi yang belum diperbarui untuk mengambil kendali atau mengakses data',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Sistem operasi beserta seluruh layanan dan data yang berjalan di atasnya dapat dieksploitasi; dampak meluas ke semua aplikasi yang bergantung pada sistem operasi tersebut',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Pembaruan sistem operasi dilakukan secara manual tanpa jadwal yang teratur; tidak ada pemantauan pengumuman celah keamanan dari vendor',
                'mitigasi_tipikal' => 'Aktifkan pembaruan keamanan otomatis atau tetapkan jadwal pembaruan rutin; pantau pengumuman keamanan dari vendor sistem operasi; terapkan prosedur pembaruan darurat untuk celah dengan tingkat keparahan tinggi',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Sistem operasi dikonfigurasi dengan layanan dan fitur yang tidak diperlukan dalam kondisi aktif sehingga memperluas permukaan yang dapat diserang',
                'ancaman_tipikal'  => 'Pihak tidak berwenang memanfaatkan layanan atau fitur sistem operasi yang aktif namun tidak dipantau sebagai titik masuk untuk mengeksploitasi sistem',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Celah tambahan terbuka melalui layanan yang tidak diperlukan namun tetap aktif; eksploitasi berhasil melalui jalur yang tidak terduga dan tidak terlindungi',
                'area_dampak'      => ['Operasional TIK'],
                'kontrol_tipikal'  => 'Sistem operasi dipasang dengan seluruh layanan bawaan aktif; penonaktifan layanan yang tidak diperlukan tidak dilakukan secara sistematis',
                'mitigasi_tipikal' => 'Nonaktifkan seluruh layanan dan fitur sistem operasi yang tidak diperlukan untuk operasional; audit secara berkala layanan yang aktif; terapkan prinsip konfigurasi minimal',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Akun pengguna pada sistem operasi tidak dikelola dengan baik — terdapat akun yang tidak digunakan, akun bersama, atau akun dengan hak akses berlebihan',
                'ancaman_tipikal'  => 'Pihak tidak berwenang memanfaatkan akun yang tidak aktif atau akun bawaan yang tidak dinonaktifkan untuk mendapatkan akses ke sistem operasi',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Akses tidak sah ke sistem operasi memberikan kendali penuh atas seluruh sumber daya yang dikelola; jejak aktivitas tidak jelas karena akun digunakan oleh banyak pihak',
                'area_dampak'      => ['Operasional TIK', 'Kinerja'],
                'kontrol_tipikal'  => 'Manajemen akun sistem operasi dilakukan secara ad-hoc; akun yang sudah tidak digunakan tidak selalu dinonaktifkan tepat waktu',
                'mitigasi_tipikal' => 'Terapkan kebijakan pengelolaan akun yang ketat: nonaktifkan akun bawaan yang tidak diperlukan, berikan akun individual untuk setiap pengguna, tinjau dan hapus akun yang tidak aktif secara berkala',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Aktivitas pada sistem operasi tidak dipantau dan tidak dicatat secara memadai sehingga tindakan tidak sah tidak dapat terdeteksi atau diinvestigasi',
                'ancaman_tipikal'  => 'Pihak yang berhasil mendapatkan akses ke sistem operasi bergerak bebas tanpa meninggalkan jejak yang dapat digunakan untuk investigasi',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Aktivitas tidak sah berlangsung dalam waktu lama tanpa diketahui; investigasi insiden tidak dapat dilakukan secara efektif karena tidak ada catatan yang memadai',
                'area_dampak'      => ['Kinerja', 'Hukum dan Regulasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Pencatatan aktivitas sistem operasi menggunakan pengaturan default yang tidak mencakup semua kejadian penting; log tidak ditinjau secara berkala',
                'mitigasi_tipikal' => 'Aktifkan pencatatan aktivitas yang komprehensif pada sistem operasi; pastikan log disimpan di lokasi yang aman dan tidak dapat diubah; tinjau log secara berkala untuk mendeteksi anomali',
            ],
        ]);

        // ── SPESIFIK: SISTEM UTILITY ───────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PL_UTL,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik aset Sistem Utility.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Sistem utility diinstal dan dioperasikan tanpa melalui proses persetujuan dan inventarisasi resmi sehingga keberadaan dan penggunaannya tidak terpantau',
                'ancaman_tipikal'  => 'Pegawai menginstal sistem utility yang tidak terverifikasi yang ternyata mengandung komponen berbahaya, atau utility yang sah dimanfaatkan oleh pihak tidak berwenang untuk tujuan yang merugikan',
                'kategori'         => 'Insiden Serangan Malware',
                'dampak_tipikal'   => 'Perangkat lunak berbahaya masuk ke dalam jaringan organisasi melalui utility yang tidak terverifikasi; sumber daya sistem disalahgunakan tanpa sepengetahuan pengelola',
                'area_dampak'      => ['Operasional TIK', 'Kinerja'],
                'kontrol_tipikal'  => 'Pegawai dapat menginstal sistem utility secara mandiri tanpa proses persetujuan; tidak ada inventaris resmi atas utility yang terpasang',
                'mitigasi_tipikal' => 'Tetapkan kebijakan yang mengharuskan persetujuan sebelum menginstal sistem utility; pelihara inventaris seluruh utility yang diizinkan; audit secara berkala utility yang terpasang dan hapus yang tidak diizinkan',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Sistem utility yang memiliki kemampuan akses luas seperti alat administrasi jarak jauh dan alat diagnostik tidak dibatasi penggunaannya dan tidak dipantau',
                'ancaman_tipikal'  => 'Pihak tidak berwenang memanfaatkan utility dengan kemampuan akses luas yang tidak dilindungi untuk mengendalikan sistem, mengekstrak data, atau menyebarkan ancaman ke seluruh jaringan',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Kendali penuh atas sistem diperoleh melalui utility yang sah namun disalahgunakan; aktivitas tidak sah berlangsung tanpa terdeteksi karena menggunakan alat yang diizinkan',
                'area_dampak'      => ['Operasional TIK', 'Keamanan Infrastruktur'],
                'kontrol_tipikal'  => 'Utility dengan kemampuan akses luas tersedia untuk seluruh pengguna tanpa pembatasan dan tanpa pencatatan penggunaan',
                'mitigasi_tipikal' => 'Batasi akses ke utility dengan kemampuan tinggi hanya kepada yang benar-benar membutuhkan; catat setiap penggunaan utility tersebut; tinjau log penggunaan secara berkala untuk mendeteksi penyalahgunaan',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Sistem utility tidak mendapat pembaruan secara berkala sehingga celah keamanan pada utility yang digunakan tetap terbuka',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mengeksploitasi celah yang diketahui pada sistem utility yang belum diperbarui untuk mendapatkan akses atau mengganggu operasional',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Sistem utility yang dieksploitasi memberikan jalan masuk ke sistem yang lebih kritikal; gangguan pada utility yang kritikal menghentikan proses operasional yang bergantung padanya',
                'area_dampak'      => ['Operasional TIK', 'Kinerja'],
                'kontrol_tipikal'  => 'Pembaruan sistem utility tidak terjadwal dan dilakukan secara ad-hoc; utility yang sudah tidak didukung vendor masih tetap digunakan',
                'mitigasi_tipikal' => 'Sertakan sistem utility dalam program pembaruan berkala organisasi; ganti utility yang sudah tidak mendapat dukungan keamanan dari vendor; pantau pengumuman celah keamanan terkait utility yang digunakan',
            ],
        ]);
    }

    private function createSet(string $scopeType, string $scopeId, string $versi, string $catatan): string
    {
        $id = (string) Str::uuid();
        DB::table('vulnerability_sets')->insertOrIgnore([
            'id'                => $id,
            'scope_type'        => $scopeType,
            'scope_id'          => $scopeId,
            'versi'             => $versi,
            'is_active'         => true,
            'catatan_perubahan' => $catatan,
            'published_at'      => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
        return $id;
    }

    private function insertItems(string $setId, array $items): void
    {
        foreach (array_chunk($items, 50) as $chunk) {
            DB::table('vulnerability_items')->insertOrIgnore(
                array_map(fn($item) => array_merge($item, [
                    'id'               => (string) Str::uuid(),
                    'set_id'           => $setId,
                    'area_dampak'      => json_encode($item['area_dampak'] ?? []),
                    'catatan_platform' => null,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]), $chunk)
            );
        }
    }
}
