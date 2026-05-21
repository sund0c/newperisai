<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KerawananPerangkatKerasSeeder extends Seeder
{
    const ID_PK     = 'a1b93053-cc54-4f84-97e6-ad22898dccdb';
    const ID_PK_PC  = 'a1b93053-ceb8-4f62-9778-ab2092328488';
    const ID_PK_SRV = 'a1b93053-cef0-4309-ab32-4e872cbb6c14';
    const ID_PK_NET = 'a1b93053-cf1f-4469-97a4-02e838c931c5';
    const ID_PK_STR = 'a1b93053-cf70-4ef0-9744-84e2e7d3444f';

    public function run(): void
    {
        // ── GLOBAL PERANGKAT KERAS ─────────────────────────────────────────
        $setId = $this->createSet(
            'global_class',
            self::ID_PK,
            '1.0',
            'Versi awal — kerawanan umum berlaku untuk semua sub-kelas Perangkat Keras.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Perangkat keras tidak memiliki inventaris yang lengkap dan mutakhir sehingga tidak seluruh aset diketahui keberadaan dan kondisinya',
                'ancaman_tipikal'  => 'Pihak tidak berwenang dari luar organisasi menargetkan atau mengeksploitasi perangkat yang tidak tercatat karena tidak ada perlindungan yang diterapkan padanya',
                'kategori'         => 'Kesalahan Pengelolaan Aset',
                'dampak_tipikal'   => 'Perangkat yang tidak terdaftar berhasil dieksploitasi atau dicuri tanpa diketahui organisasi; insiden tidak dapat diinvestigasi karena keberadaan perangkat tidak pernah tercatat',
                'area_dampak'      => ['Kinerja', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Inventaris perangkat keras dibuat secara manual dan tidak selalu diperbarui saat ada penambahan, perpindahan, atau penghapusan aset',
                'mitigasi_tipikal' => 'Pelihara inventaris perangkat keras yang lengkap dan selalu mutakhir; lakukan audit fisik secara berkala untuk memverifikasi kesesuaian dengan kondisi nyata; tandai setiap perangkat dengan identifikasi yang jelas',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Akses fisik ke perangkat keras tidak dibatasi sehingga pihak yang tidak berwenang dapat menyentuh, memodifikasi, atau mengambil perangkat secara langsung',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang memiliki akses ke ruangan tempat perangkat berada memanipulasi, mencuri, atau menyambungkan perangkat tambahan tanpa diketahui',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Perangkat dicuri atau dirusak; perangkat penyadap dipasang tanpa sepengetahuan organisasi; konfigurasi perangkat diubah untuk membuka akses tidak sah',
                'area_dampak'      => ['Operasional TIK', 'Kinerja', 'Finansial'],
                'kontrol_tipikal'  => 'Akses ke ruang perangkat hanya dibatasi oleh kunci fisik biasa tanpa pencatatan siapa yang masuk dan kapan',
                'mitigasi_tipikal' => 'Terapkan kontrol akses fisik berlapis ke ruangan perangkat; catat setiap akses masuk dan keluar; pasang kamera pengawas di area perangkat kritikal; kunci perangkat pada tempatnya untuk mencegah pencurian',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Perangkat keras tidak mendapat pembaruan firmware secara berkala sehingga kelemahan yang sudah diketahui publik belum ditangani',
                'ancaman_tipikal'  => 'Pihak tidak berwenang memanfaatkan kelemahan yang sudah diketahui pada firmware perangkat yang belum diperbarui untuk mengambil alih kendali atau mengganggu operasional',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Perangkat dapat dikendalikan dari jarak jauh; layanan yang bergantung pada perangkat terganggu; kelemahan yang sebenarnya sudah ada solusinya tetap menjadi risiko',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Pembaruan firmware dilakukan secara ad-hoc tanpa jadwal yang teratur; tidak ada pemantauan pengumuman kelemahan dari produsen',
                'mitigasi_tipikal' => 'Tetapkan jadwal pembaruan firmware secara berkala; pantau pengumuman kelemahan dari produsen perangkat; terapkan prosedur pembaruan darurat untuk kelemahan dengan tingkat keparahan tinggi',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Perangkat keras menggunakan konfigurasi bawaan pabrik termasuk kata sandi default yang tidak diganti sehingga mudah diakses oleh pihak yang mengetahui nilai defaultnya',
                'ancaman_tipikal'  => 'Pihak tidak berwenang menggunakan kata sandi default atau memanfaatkan layanan bawaan yang tidak diperlukan untuk mengakses dan mengendalikan perangkat',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Perangkat dapat diakses dan dikonfigurasi ulang oleh pihak luar; layanan yang bergantung pada perangkat terganggu atau disalahgunakan',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Perangkat dipasang dengan konfigurasi bawaan pabrik; penggantian kata sandi default tidak dilakukan secara sistematis',
                'mitigasi_tipikal' => 'Ganti seluruh kata sandi default sebelum perangkat dioperasikan; nonaktifkan layanan dan antarmuka yang tidak diperlukan; terapkan panduan konfigurasi keamanan dasar untuk setiap jenis perangkat',
            ],
            [
                'nomor_urut'       => 5,
                'deskripsi'        => 'Tidak ada prosedur yang memastikan data dihapus secara tuntas dari perangkat sebelum dipindahtangankan, diperbaiki, atau dibuang',
                'ancaman_tipikal'  => 'Pihak yang menerima perangkat bekas — termasuk teknisi perbaikan atau pengepul barang elektronik — memulihkan dan menyalahgunakan data yang masih tersimpan',
                'kategori'         => 'Pencurian Data Pribadi',
                'dampak_tipikal'   => 'Data sensitif organisasi dan data pribadi pengguna bocor melalui perangkat yang dibuang atau dipindahtangankan; pelanggaran kewajiban kerahasiaan dan regulasi perlindungan data',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi'],
                'kontrol_tipikal'  => 'Perangkat dipindahtangankan atau dibuang tanpa prosedur penghapusan data yang terstandar; penghapusan biasa dianggap sudah cukup',
                'mitigasi_tipikal' => 'Terapkan prosedur penghapusan data yang tuntas sebelum perangkat dipindahtangankan atau dibuang; dokumentasikan proses penghapusan sebagai bukti kepatuhan; pertimbangkan penghancuran fisik untuk media penyimpanan yang sangat sensitif',
            ],
            [
                'nomor_urut'       => 6,
                'deskripsi'        => 'Tidak ada mekanisme pemantauan kondisi fisik dan operasional perangkat sehingga kerusakan atau anomali tidak terdeteksi sejak dini',
                'ancaman_tipikal'  => 'Kondisi eksternal yang tidak terduga — seperti lonjakan arus listrik, suhu lingkungan berlebih, atau getaran fisik — merusak perangkat yang tidak terlindungi atau tidak terpantau',
                'kategori'         => 'Terganggunya Keberlangsungan Layanan',
                'dampak_tipikal'   => 'Kegagalan perangkat terjadi tiba-tiba tanpa persiapan; layanan terhenti; pemulihan membutuhkan waktu lebih lama karena tidak ada informasi kondisi sebelum kegagalan',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Pemantauan kondisi perangkat dilakukan secara visual dan periodik tanpa alat bantu otomatis',
                'mitigasi_tipikal' => 'Pasang sistem pemantauan kondisi perangkat secara otomatis; tetapkan ambang batas untuk kondisi kritis dan konfigurasikan peringatan dini; lakukan pemeliharaan preventif secara terjadwal',
            ],
        ]);

        // ── SPESIFIK: SERVER ───────────────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PK_SRV,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik perangkat Server.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Server dapat diakses untuk keperluan administrasi melalui jaringan publik tanpa pembatasan dan tanpa lapisan pengamanan tambahan',
                'ancaman_tipikal'  => 'Pihak tidak berwenang dari mana saja di internet mencoba mengakses antarmuka administrasi server untuk mengambil alih kendali penuh',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Server beserta seluruh sistem dan data yang dijalankannya dapat diakses dan dimanipulasi; dampak meluas ke seluruh layanan yang bergantung pada server',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Akses administrasi server hanya dilindungi oleh kata sandi tanpa pembatasan berdasarkan jaringan atau lokasi',
                'mitigasi_tipikal' => 'Batasi akses administrasi server hanya dari jaringan internal; wajibkan verifikasi tambahan untuk setiap akses administrasi; nonaktifkan antarmuka administrasi yang tidak digunakan',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Server tidak memiliki redundansi atau sistem cadangan yang dapat mengambil alih operasional saat server utama mengalami kegagalan',
                'ancaman_tipikal'  => 'Serangan dari luar yang melumpuhkan layanan, bencana yang menimpa lokasi server, atau gangguan eksternal lainnya menyebabkan server tidak dapat beroperasi',
                'kategori'         => 'Terganggunya Keberlangsungan Layanan',
                'dampak_tipikal'   => 'Semua layanan yang dijalankan server tidak dapat diakses; pemulihan membutuhkan waktu yang tidak pasti; kerugian operasional yang signifikan',
                'area_dampak'      => ['Layanan Organisasi', 'Kinerja', 'Finansial'],
                'kontrol_tipikal'  => 'Hanya ada satu server untuk setiap layanan tanpa sistem cadangan yang siap mengambil alih',
                'mitigasi_tipikal' => 'Siapkan server cadangan yang dapat mengambil alih operasional secara otomatis atau dalam waktu singkat; uji kemampuan failover secara berkala; dokumentasikan prosedur pemulihan layanan',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Layanan dan port yang tidak diperlukan untuk operasional dibiarkan aktif di server sehingga memperluas permukaan yang dapat diserang',
                'ancaman_tipikal'  => 'Pihak tidak berwenang memanfaatkan layanan yang tidak diperlukan namun masih aktif sebagai titik masuk untuk mengeksploitasi server',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Celah tambahan terbuka melalui layanan yang tidak dipantau; eksploitasi berhasil melalui jalur yang tidak terduga dan tidak terlindungi',
                'area_dampak'      => ['Operasional TIK'],
                'kontrol_tipikal'  => 'Server dipasang dengan seluruh layanan bawaan aktif; penonaktifan layanan yang tidak diperlukan tidak dilakukan secara sistematis',
                'mitigasi_tipikal' => 'Nonaktifkan seluruh layanan dan port yang tidak diperlukan untuk operasional; audit secara berkala layanan yang aktif di setiap server; terapkan prinsip konfigurasi minimal',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Tidak ada pemisahan yang memadai antara server yang menangani fungsi berbeda sehingga kompromi pada satu server dapat dengan mudah merambat ke server lainnya',
                'ancaman_tipikal'  => 'Pihak yang berhasil mengakses satu server memanfaatkan koneksi antar server yang tidak dibatasi untuk merambat ke server lain yang lebih kritikal',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Kompromi pada satu server merambat ke seluruh infrastruktur; data dari berbagai sistem dapat diakses sekaligus; dampak insiden menjadi jauh lebih luas',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Server-server yang berbeda fungsi ditempatkan dalam satu jaringan yang sama tanpa pembatasan komunikasi antar server',
                'mitigasi_tipikal' => 'Pisahkan server berdasarkan fungsi dan tingkat kekritisannya dalam segmen jaringan yang berbeda; terapkan pembatasan komunikasi yang ketat antar segmen; pantau lalu lintas antar segmen untuk mendeteksi anomali',
            ],
        ]);

        // ── SPESIFIK: PC/LAPTOP/SMARTPHONE ────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PK_PC,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik PC, Laptop, dan Smartphone.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Pengguna perangkat memiliki hak akses administrator penuh sehingga dapat menginstal perangkat lunak dan mengubah konfigurasi sistem tanpa pengawasan',
                'ancaman_tipikal'  => 'Pihak luar mengirimkan perangkat lunak berbahaya atau mengeksploitasi celah pada perangkat; karena pengguna memiliki hak administrator, penyerang langsung mendapatkan kendali penuh',
                'kategori'         => 'Insiden Serangan Malware',
                'dampak_tipikal'   => 'Perangkat lunak berbahaya terpasang tanpa hambatan; konfigurasi keamanan sistem diubah; perangkat menjadi titik awal penyebaran ancaman ke seluruh jaringan',
                'area_dampak'      => ['Operasional TIK', 'Kinerja'],
                'kontrol_tipikal'  => 'Pengguna diberi hak akses administrator untuk kemudahan operasional sehari-hari tanpa mempertimbangkan risiko keamanan',
                'mitigasi_tipikal' => 'Berikan pengguna hak akses standar untuk kegiatan sehari-hari; pisahkan akun administrator untuk keperluan pengelolaan sistem; terapkan mekanisme persetujuan untuk instalasi perangkat lunak baru',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Perangkat tidak dilindungi oleh perangkat lunak keamanan yang aktif dan selalu diperbarui untuk mendeteksi dan mencegah ancaman',
                'ancaman_tipikal'  => 'Perangkat lunak berbahaya yang masuk melalui email, unduhan, atau media penyimpanan portabel berhasil berjalan dan menyebar tanpa terdeteksi',
                'kategori'         => 'Insiden Serangan Malware',
                'dampak_tipikal'   => 'Data di perangkat dicuri atau dienkripsi oleh perangkat lunak berbahaya; perangkat menjadi sarana penyebaran ancaman ke seluruh jaringan organisasi',
                'area_dampak'      => ['Operasional TIK', 'Kinerja', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Perangkat lunak keamanan terpasang namun tidak selalu diperbarui secara teratur',
                'mitigasi_tipikal' => 'Pastikan seluruh perangkat memiliki perangkat lunak keamanan yang aktif dengan pembaruan otomatis; pantau status perlindungan secara terpusat; tangani perangkat yang tidak terlindungi secara prioritas',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Perangkat tidak terkunci secara otomatis saat ditinggalkan sehingga pihak lain dapat mengakses sistem yang sedang aktif',
                'ancaman_tipikal'  => 'Pihak lain yang berada di sekitar tempat kerja mengakses perangkat yang ditinggalkan pengguna dalam kondisi aktif dan tidak terkunci',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Data yang sedang terbuka dapat dilihat, disalin, atau dimanipulasi; tindakan tidak sah dilakukan atas nama pengguna yang meninggalkan perangkat',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Penguncian layar hanya dilakukan secara manual oleh pengguna saat meninggalkan perangkat',
                'mitigasi_tipikal' => 'Konfigurasikan penguncian layar otomatis setelah periode tidak aktif yang singkat; sosialisasikan kebiasaan mengunci perangkat sebelum ditinggalkan; terapkan kebijakan meja bersih',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Perangkat tidak dienkripsi sehingga jika dicuri atau hilang seluruh data di dalamnya dapat langsung diakses oleh penemu atau pencuri',
                'ancaman_tipikal'  => 'Perangkat yang hilang atau dicuri diakses oleh pihak yang menemukannya untuk membaca dan menyalin seluruh data yang tersimpan',
                'kategori'         => 'Pencurian Data Pribadi',
                'dampak_tipikal'   => 'Data sensitif organisasi dan data pribadi pengguna bocor melalui perangkat yang hilang; informasi yang tersimpan dapat disalahgunakan oleh pihak yang tidak berwenang',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Finansial'],
                'kontrol_tipikal'  => 'Perangkat tidak menggunakan enkripsi penyimpanan; keamanan data bergantung sepenuhnya pada penguncian layar',
                'mitigasi_tipikal' => 'Aktifkan enkripsi seluruh penyimpanan perangkat; pastikan kata sandi perangkat cukup kuat; aktifkan fitur penghapusan data jarak jauh jika tersedia; laporkan kehilangan perangkat segera',
            ],
        ]);

        // ── SPESIFIK: PERANGKAT JARINGAN ──────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PK_NET,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Perangkat Jaringan.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Lalu lintas jaringan tidak dipantau sehingga aktivitas yang mencurigakan atau tidak normal tidak terdeteksi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang telah masuk ke jaringan bergerak secara bebas antar sistem tanpa terdeteksi dalam waktu yang lama',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Ancaman yang sudah masuk ke jaringan tidak terdeteksi dan terus beroperasi; kerusakan meluas sebelum insiden akhirnya diketahui',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Pemantauan lalu lintas jaringan tidak dilakukan secara aktif; peninjauan log hanya dilakukan jika ada laporan insiden',
                'mitigasi_tipikal' => 'Terapkan pemantauan lalu lintas jaringan secara aktif dan berkelanjutan; konfigurasikan peringatan otomatis untuk pola lalu lintas yang tidak normal; tinjau log jaringan secara berkala',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Jaringan tidak dibagi ke dalam segmen-segmen terpisah sehingga semua perangkat dapat berkomunikasi satu sama lain tanpa pembatasan',
                'ancaman_tipikal'  => 'Pihak yang berhasil masuk ke salah satu bagian jaringan dapat dengan bebas mengakses seluruh perangkat dan sistem lain yang terhubung',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Kompromi pada satu titik jaringan memberikan akses ke seluruh infrastruktur; penyebaran ancaman antar sistem tidak dapat dibatasi',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Seluruh perangkat berada dalam satu jaringan datar tanpa pemisahan berdasarkan fungsi atau tingkat kepercayaan',
                'mitigasi_tipikal' => 'Segmentasikan jaringan berdasarkan fungsi dan tingkat kekritisan; terapkan kontrol yang membatasi komunikasi antar segmen hanya untuk yang diperlukan; pantau lalu lintas di batas antar segmen',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Jaringan nirkabel menggunakan protokol enkripsi yang lemah atau tidak memerlukan autentikasi yang memadai untuk terhubung',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang berada dalam jangkauan sinyal jaringan nirkabel terhubung ke jaringan organisasi tanpa izin',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Pihak tidak berwenang mendapatkan akses ke jaringan internal; lalu lintas jaringan dapat disadap; sumber daya jaringan disalahgunakan',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Jaringan nirkabel menggunakan protokol keamanan yang sudah lama atau kata sandi yang sama untuk semua pengguna',
                'mitigasi_tipikal' => 'Gunakan protokol enkripsi jaringan nirkabel yang kuat dan terkini; pisahkan jaringan nirkabel tamu dari jaringan internal; pantau perangkat yang terhubung secara berkala',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Aturan penyaringan lalu lintas jaringan tidak dikonfigurasi secara ketat sehingga komunikasi yang seharusnya tidak diizinkan dapat tetap berlangsung',
                'ancaman_tipikal'  => 'Pihak tidak berwenang menggunakan jalur komunikasi yang tidak dibatasi untuk mengakses sistem internal atau mengekstrak data',
                'kategori'         => 'Keamanan Infrastruktur',
                'dampak_tipikal'   => 'Data sensitif bocor melalui jalur komunikasi yang tidak dipantau; sistem internal dapat diakses melalui jalur yang tidak terduga',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Aturan penyaringan lalu lintas menggunakan pendekatan mengizinkan semua kecuali yang diblokir',
                'mitigasi_tipikal' => 'Terapkan prinsip tolak semua kecuali yang secara eksplisit diizinkan pada aturan penyaringan; tinjau dan perbarui aturan secara berkala; hapus aturan yang sudah tidak diperlukan',
            ],
        ]);

        // ── SPESIFIK: PERANGKAT PENYIMPANAN ───────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_PK_STR,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Perangkat Penyimpanan.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Data yang tersimpan di perangkat penyimpanan tidak dienkripsi sehingga dapat langsung dibaca jika perangkat diakses secara fisik atau dicuri',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang mendapatkan akses fisik ke perangkat penyimpanan atau mencurinya dapat membaca seluruh data tanpa hambatan',
                'kategori'         => 'Pencurian Data Pribadi',
                'dampak_tipikal'   => 'Seluruh data yang tersimpan dapat diakses dan disalin; kebocoran data dalam skala besar dengan risiko pelanggaran regulasi perlindungan data',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Finansial'],
                'kontrol_tipikal'  => 'Data disimpan dalam format biasa tanpa enkripsi; keamanan data bergantung sepenuhnya pada keamanan fisik perangkat',
                'mitigasi_tipikal' => 'Enkripsi seluruh data yang tersimpan di perangkat penyimpanan; kelola kunci enkripsi secara terpisah dan aman; pastikan data tetap tidak terbaca meskipun perangkat jatuh ke tangan yang salah',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Kapasitas penyimpanan tidak dipantau secara aktif sehingga penuhnya kapasitas dapat menyebabkan kegagalan sistem secara tiba-tiba',
                'ancaman_tipikal'  => 'Lonjakan data yang tidak terduga dari sumber eksternal — seperti serangan yang mengisi storage atau pertumbuhan tiba-tiba akibat kejadian luar — menghabiskan kapasitas yang tidak dipantau',
                'kategori'         => 'Terganggunya Keberlangsungan Layanan',
                'dampak_tipikal'   => 'Sistem tidak dapat menyimpan data baru; layanan yang bergantung pada penyimpanan terhenti; data transaksi yang seharusnya tersimpan menjadi hilang',
                'area_dampak'      => ['Operasional TIK', 'Layanan Organisasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Pemantauan kapasitas dilakukan secara manual dan periodik; tidak ada peringatan otomatis saat kapasitas mendekati batas',
                'mitigasi_tipikal' => 'Terapkan pemantauan kapasitas otomatis dengan peringatan dini; tetapkan kebijakan pengelolaan data termasuk retensi dan penghapusan data yang sudah tidak diperlukan',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Tidak ada mekanisme yang memverifikasi keutuhan data yang tersimpan sehingga kerusakan atau manipulasi data tidak terdeteksi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang dari luar yang berhasil mengakses storage memanipulasi atau merusak data secara sengaja; atau kejadian eksternal seperti bencana merusak media penyimpanan',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Data yang rusak atau telah dimanipulasi digunakan untuk pengambilan keputusan; integritas seluruh dataset diragukan; pemulihan data yang valid menjadi sulit',
                'area_dampak'      => ['Kinerja', 'Hukum dan Regulasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Tidak ada pemeriksaan keutuhan data secara berkala; kerusakan data baru diketahui saat data hendak digunakan',
                'mitigasi_tipikal' => 'Terapkan mekanisme verifikasi keutuhan data secara berkala; simpan nilai referensi untuk memungkinkan deteksi perubahan yang tidak sah; lakukan pemeriksaan konsistensi data secara terjadwal',
            ],
        ]);
    }

    private function createSet(string $scopeType, string $scopeId, string $versi, string $catatan): string
    {
        $id = (string) Str::uuid();
        DB::table('vulnerability_sets')->insertOrIgnore([
            'id' => $id,
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
            'versi' => $versi,
            'is_active' => true,
            'catatan_perubahan' => $catatan,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return $id;
    }

    private function insertItems(string $setId, array $items): void
    {
        foreach (array_chunk($items, 50) as $chunk) {
            DB::table('vulnerability_items')->insertOrIgnore(
                array_map(fn($item) => array_merge($item, [
                    'id' => (string) Str::uuid(),
                    'set_id' => $setId,
                    'area_dampak' => json_encode($item['area_dampak'] ?? []),
                    'catatan_platform' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]), $chunk)
            );
        }
    }
}
