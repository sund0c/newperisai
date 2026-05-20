<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KerawananSaranaPendukungSeeder extends Seeder
{
    const ID_SP      = 'a1b93053-cc88-4fd6-82f5-1f0351cb3a25'; // Sarana Pendukung
    const ID_SP_APP  = 'a1b93053-d070-436b-9cd1-8a5504bf8133'; // Support Appliance
    const ID_SP_FAC  = 'a1b93053-d0a0-4b33-9a1d-309d9d49d718'; // Support Facility

    public function run(): void
    {
        // =====================================================================
        // GLOBAL SARANA PENDUKUNG
        // =====================================================================
        $setId = $this->createSet(
            'global_class',
            self::ID_SP,
            '1.0',
            'Versi awal — kerawanan umum berlaku untuk semua sub-kelas Sarana Pendukung.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'      => 1,
                'deskripsi'       => 'Tidak ada rencana keberlangsungan operasional yang terdokumentasi untuk menghadapi gangguan pada sarana pendukung',
                'ancaman_tipikal' => 'Bencana alam, serangan fisik, atau gangguan eksternal yang menimpa sarana pendukung tidak dapat ditangani karena tidak ada prosedur alternatif yang disiapkan sebelumnya',
                'dampak_tipikal'  => 'Operasional organisasi terhenti dalam waktu yang tidak pasti; pemulihan berjalan lambat karena tidak ada panduan yang jelas; kerugian layanan kepada masyarakat',
                'kontrol_tipikal' => 'Penanganan gangguan dilakukan secara reaktif berdasarkan pengalaman dan inisiatif individu tanpa panduan tertulis yang terstandar',
                'mitigasi_tipikal' => 'Susun dan dokumentasikan rencana keberlangsungan operasional yang mencakup skenario gangguan sarana pendukung; uji rencana secara berkala; pastikan seluruh personel kunci memahami perannya',
            ],
            [
                'nomor_urut'      => 2,
                'deskripsi'       => 'Pemeliharaan sarana pendukung tidak dilakukan secara terjadwal sehingga kerusakan yang dapat dicegah terjadi secara tiba-tiba',
                'ancaman_tipikal' => 'Faktor eksternal seperti usia pakai, beban operasional berlebih, atau kondisi lingkungan yang tidak sesuai menyebabkan sarana pendukung gagal secara tiba-tiba',
                'dampak_tipikal'  => 'Kerusakan mendadak menghentikan operasional tanpa persiapan; biaya perbaikan darurat jauh lebih tinggi dari pemeliharaan rutin; pemulihan membutuhkan waktu lebih lama',
                'kontrol_tipikal' => 'Pemeliharaan hanya dilakukan saat sudah terjadi kerusakan atau keluhan; tidak ada jadwal pemeliharaan preventif yang terstruktur',
                'mitigasi_tipikal' => 'Terapkan program pemeliharaan preventif dengan jadwal yang terstruktur; dokumentasikan kondisi sarana pendukung dan riwayat pemeliharaan; prioritaskan pemeliharaan berdasarkan tingkat kekritisan',
            ],
            [
                'nomor_urut'      => 3,
                'deskripsi'       => 'Akses ke area kritis yang mengandung perangkat dan infrastruktur pendukung penting tidak dikendalikan secara memadai',
                'ancaman_tipikal' => 'Pihak yang tidak berwenang memasuki area yang berisi perangkat atau infrastruktur penting dan merusak, mencuri, atau memanipulasi komponen yang ada',
                'dampak_tipikal'  => 'Perangkat dan infrastruktur penting dirusak atau dicuri; layanan terganggu; keamanan data yang tersimpan di perangkat terancam',
                'kontrol_tipikal' => 'Akses ke area kritis dibatasi hanya dengan kunci fisik konvensional tanpa pencatatan siapa yang masuk dan keluar',
                'mitigasi_tipikal' => 'Terapkan sistem kontrol akses fisik berlapis ke area kritis; catat seluruh akses masuk dan keluar; pasang sistem pengawasan; lakukan audit fisik secara berkala',
            ],
            [
                'nomor_urut'      => 4,
                'deskripsi'       => 'Tidak ada prosedur keamanan fisik yang diterapkan secara konsisten untuk melindungi sarana pendukung dari ancaman fisik',
                'ancaman_tipikal' => 'Kerusakan yang disengaja, vandalisme, atau pencurian yang menargetkan sarana pendukung organisasi mengganggu operasional',
                'dampak_tipikal'  => 'Sarana pendukung rusak atau hilang; operasional terganggu; biaya penggantian yang tinggi; potensi kehilangan data jika perangkat penyimpanan ikut terdampak',
                'kontrol_tipikal' => 'Keamanan fisik bergantung pada petugas keamanan dan kunci pintu tanpa prosedur terstandar dan pengawasan yang konsisten',
                'mitigasi_tipikal' => 'Tetapkan prosedur keamanan fisik yang terstandar; pastikan prosedur diterapkan secara konsisten; lakukan audit keamanan fisik secara berkala; tingkatkan perlindungan untuk area dan perangkat yang paling kritikal',
            ],
        ]);

        // =====================================================================
        // SPESIFIK: SUPPORT APPLIANCE
        // =====================================================================
        $setId = $this->createSet(
            'subclass',
            self::ID_SP_APP,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Support Appliance.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'      => 1,
                'deskripsi'       => 'Pasokan daya listrik untuk perangkat dan sistem kritikal tidak memiliki sumber cadangan sehingga gangguan listrik langsung menyebabkan kegagalan operasional',
                'ancaman_tipikal' => 'Pemadaman listrik dari penyedia eksternal, lonjakan arus dari luar, atau bencana yang merusak infrastruktur listrik menghentikan seluruh perangkat yang tidak memiliki sumber cadangan',
                'dampak_tipikal'  => 'Seluruh sistem dan layanan yang menggunakan perangkat terkait terhenti secara mendadak; data yang sedang diproses berisiko rusak; pemulihan membutuhkan waktu setelah listrik kembali',
                'kontrol_tipikal' => 'Perangkat kritikal hanya menggunakan sumber listrik utama tanpa cadangan; tidak ada sistem perlindungan terhadap gangguan listrik',
                'mitigasi_tipikal' => 'Pasang sumber daya cadangan untuk perangkat kritikal; pastikan kapasitas cadangan cukup untuk memberikan waktu penanganan darurat yang memadai; uji sistem cadangan secara berkala',
            ],
            [
                'nomor_urut'      => 2,
                'deskripsi'       => 'Sistem pendingin untuk ruang perangkat tidak memiliki redundansi sehingga kegagalan sistem pendingin tunggal langsung mengancam keselamatan perangkat',
                'ancaman_tipikal' => 'Kerusakan mendadak pada unit pendingin — akibat faktor eksternal seperti pemadaman, gangguan mekanis, atau kondisi lingkungan ekstrem — menyebabkan suhu ruangan melonjak di luar kendali',
                'dampak_tipikal'  => 'Perangkat mengalami kerusakan akibat panas berlebih; sistem mati secara mendadak; pemulihan membutuhkan waktu dan biaya yang signifikan',
                'kontrol_tipikal' => 'Hanya terdapat satu unit sistem pendingin untuk setiap ruang perangkat tanpa unit cadangan yang siap diaktifkan',
                'mitigasi_tipikal' => 'Pasang sistem pendingin dengan redundansi; pastikan sistem cadangan dapat diaktifkan secara otomatis saat sistem utama gagal; pantau suhu ruangan secara terus-menerus dengan peringatan dini',
            ],
            [
                'nomor_urut'      => 3,
                'deskripsi'       => 'Koneksi internet dan jaringan komunikasi tidak memiliki jalur cadangan sehingga gangguan pada penyedia layanan utama memutus seluruh komunikasi',
                'ancaman_tipikal' => 'Penyedia layanan internet mengalami gangguan operasional, melakukan pemeliharaan tanpa pemberitahuan, atau terdampak bencana sehingga koneksi organisasi ke dunia luar terputus sepenuhnya',
                'dampak_tipikal'  => 'Layanan yang bergantung pada koneksi internet tidak dapat diakses; komunikasi dengan pihak eksternal terhenti; operasional berbasis cloud terganggu',
                'kontrol_tipikal' => 'Organisasi bergantung pada satu penyedia layanan internet tanpa jalur komunikasi alternatif',
                'mitigasi_tipikal' => 'Siapkan jalur komunikasi alternatif melalui penyedia layanan yang berbeda; dokumentasikan prosedur peralihan ke jalur alternatif; uji kemampuan peralihan secara berkala',
            ],
        ]);

        // =====================================================================
        // SPESIFIK: SUPPORT FACILITY
        // =====================================================================
        $setId = $this->createSet(
            'subclass',
            self::ID_SP_FAC,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Support Facility.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'      => 1,
                'deskripsi'       => 'Ruang server atau ruang perangkat tidak memiliki kontrol lingkungan yang memadai seperti pengatur suhu, kelembaban, dan pendeteksi kebakaran',
                'ancaman_tipikal' => 'Kebakaran, banjir, suhu ekstrem, atau bencana lain yang berasal dari luar ruangan perangkat merusak seluruh perangkat di dalamnya karena tidak ada kontrol lingkungan yang memadai',
                'dampak_tipikal'  => 'Perangkat mengalami kerusakan permanen; seluruh layanan yang bergantung pada perangkat tersebut terhenti; pemulihan membutuhkan waktu dan biaya yang signifikan',
                'kontrol_tipikal' => 'Pendingin ruangan standar digunakan tanpa pemantauan suhu terus-menerus; tidak ada sistem deteksi kebakaran khusus untuk ruang perangkat',
                'mitigasi_tipikal' => 'Pasang sistem kontrol lingkungan dengan pemantauan otomatis dan peringatan dini; pastikan sistem pendingin memiliki redundansi; pasang sistem deteksi dan pemadam kebakaran yang sesuai; uji sistem secara berkala',
            ],
            [
                'nomor_urut'      => 2,
                'deskripsi'       => 'Tata letak fasilitas tidak memisahkan area publik dari area terbatas sehingga pengunjung dapat dengan mudah memasuki area yang seharusnya hanya untuk personel berwenang',
                'ancaman_tipikal' => 'Pengunjung atau pihak tidak berwenang yang memasuki area terbatas mendapatkan akses ke perangkat, dokumen, atau percakapan yang bersifat rahasia',
                'dampak_tipikal'  => 'Informasi sensitif terekspos kepada pihak yang tidak berwenang melalui pengamatan langsung; perangkat di area terbatas dapat diakses atau dimanipulasi',
                'kontrol_tipikal' => 'Pembatasan area hanya berupa tanda atau papan petunjuk tanpa pembatas fisik yang efektif',
                'mitigasi_tipikal' => 'Rancang tata letak fasilitas yang memisahkan area publik dari area terbatas secara fisik; pasang kontrol akses di setiap titik masuk area terbatas; dampingi seluruh tamu di area sensitif',
            ],
            [
                'nomor_urut'      => 3,
                'deskripsi'       => 'Tidak ada sistem pengawasan yang memantau aktivitas fisik di area kritis sehingga kejadian yang mencurigakan tidak terdeteksi dan tidak terdokumentasi',
                'ancaman_tipikal' => 'Tindakan tidak sah yang terjadi di area kritis tidak terekam sehingga tidak dapat dideteksi saat terjadi maupun diinvestigasi setelahnya',
                'dampak_tipikal'  => 'Insiden fisik tidak terdeteksi tepat waktu; investigasi tidak dapat dilakukan karena tidak ada bukti rekaman; pelaku tidak dapat diidentifikasi',
                'kontrol_tipikal' => 'Tidak ada kamera pengawas di area kritis; pemantauan hanya dilakukan oleh petugas keamanan secara periodik',
                'mitigasi_tipikal' => 'Pasang sistem kamera pengawas di seluruh area kritis; pastikan rekaman disimpan dan terlindungi untuk periode yang memadai; tinjau rekaman secara berkala dan saat ada insiden',
            ],
            [
                'nomor_urut'      => 4,
                'deskripsi'       => 'Instalasi kabel dan infrastruktur jaringan tidak terlindungi secara fisik sehingga dapat diakses, dirusak, atau disadap secara fisik oleh pihak yang tidak berwenang',
                'ancaman_tipikal' => 'Pihak tidak berwenang mengakses kabel atau perangkat jaringan untuk memutus koneksi, merusak infrastruktur, atau memasang perangkat penyadap',
                'dampak_tipikal'  => 'Koneksi jaringan terganggu; data yang melintas dapat disadap; pemulihan membutuhkan waktu karena kerusakan fisik',
                'kontrol_tipikal' => 'Kabel jaringan terpasang tanpa pelindung fisik yang memadai; jalur kabel dapat diakses di berbagai titik tanpa kontrol',
                'mitigasi_tipikal' => 'Lindungi instalasi kabel dengan pelindung fisik yang memadai; pasang kabel di jalur yang tidak mudah diakses; dokumentasikan seluruh jalur kabel; periksa instalasi secara berkala untuk mendeteksi modifikasi tidak sah',
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
                    'catatan_platform' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]), $chunk)
            );
        }
    }
}
