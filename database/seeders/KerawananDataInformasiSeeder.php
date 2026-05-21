<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KerawananDataInformasiSeeder extends Seeder
{
    const ID_DI     = 'a1b93053-cb56-40fa-8747-921a184462e5';
    const ID_DI_PBP = 'a1b93053-cd63-42a1-a1e0-f7ba330b5db6';
    const ID_DI_FRM = 'a1b93053-cdd6-4988-83eb-f890e74458d8';
    const ID_DI_LOG = 'a1b93053-ce0f-48da-a27c-3b8e7c62de2b';
    const ID_DI_DB  = 'a1b93053-ce45-4cf8-9a83-489933455439';
    const ID_DI_KTR = 'a1b93053-ce82-4e5f-83a6-e6d63c20db5e';

    public function run(): void
    {
        // ── GLOBAL DATA & INFORMASI ────────────────────────────────────────
        $setId = $this->createSet(
            'global_class',
            self::ID_DI,
            '1.0',
            'Versi awal — kerawanan umum berlaku untuk semua sub-kelas Data dan Informasi.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Data dan informasi tidak diklasifikasikan berdasarkan tingkat kepentingan dan kerahasiaannya sehingga seluruh data diperlakukan dengan cara yang sama',
                'ancaman_tipikal'  => 'Pihak tidak berwenang mengakses data yang seharusnya dilindungi lebih ketat karena tidak ada tanda atau mekanisme pembeda antara data publik dan data rahasia',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Data rahasia berhasil diakses oleh pihak tidak berwenang; kebocoran terjadi tanpa disadari',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Tidak ada sistem klasifikasi data yang formal; tingkat perlindungan ditentukan oleh masing-masing pengelola secara tidak terstandar',
                'mitigasi_tipikal' => 'Terapkan kebijakan klasifikasi data dengan kategori yang jelas; tandai setiap data sesuai kategorinya; tentukan aturan penanganan yang berbeda untuk setiap kategori; latih seluruh pegawai',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Akses ke data tidak dibatasi berdasarkan kebutuhan nyata pengguna sehingga banyak pihak memiliki akses ke data yang tidak relevan dengan tugasnya',
                'ancaman_tipikal'  => 'Pihak dari luar unit pemilik data — termasuk pegawai unit lain, vendor, atau pihak eksternal — mengakses dan menyalahgunakan data yang seharusnya tidak menjadi haknya',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Data sensitif dapat diakses oleh pihak yang tidak memerlukan; risiko kebocoran data meningkat secara proporsional dengan luasnya akses yang diberikan',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Hak akses data diberikan secara luas untuk kemudahan operasional tanpa mempertimbangkan prinsip kebutuhan minimum',
                'mitigasi_tipikal' => 'Terapkan prinsip hak akses minimum; tinjau hak akses secara berkala dan cabut yang sudah tidak relevan',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Tidak ada prosedur pengelolaan data yang mengatur siklus hidup data dari penciptaan hingga penghapusan, termasuk retensi dan pemusnahan',
                'ancaman_tipikal'  => 'Pihak tidak berwenang dari luar menemukan dan mengeksploitasi data lama yang seharusnya sudah dimusnahkan',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Penumpukan data yang tidak perlu meningkatkan dampak jika terjadi kebocoran; kewajiban hukum terkait retensi data tidak terpenuhi',
                'area_dampak'      => ['Hukum dan Regulasi', 'Operasional TIK'],
                'kontrol_tipikal'  => 'Data disimpan tanpa batas waktu yang jelas; penghapusan data dilakukan secara ad-hoc',
                'mitigasi_tipikal' => 'Tetapkan kebijakan retensi data yang jelas; jadwalkan peninjauan dan penghapusan data yang sudah melewati masa retensi; dokumentasikan proses penghapusan',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Perpindahan data ke pihak luar tidak dikendalikan dan tidak didokumentasikan dengan memadai',
                'ancaman_tipikal'  => 'Data yang dibagikan ke pihak luar tidak mendapat perlindungan yang setara sehingga bocor melalui jalur pihak ketiga yang lebih lemah keamanannya',
                'kategori'         => 'Kesalahan Pengelolaan oleh Pihak Ketiga',
                'dampak_tipikal'   => 'Data sensitif bocor melalui pihak yang menerima data; organisasi bertanggung jawab atas insiden yang terjadi di pihak penerima',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Finansial'],
                'kontrol_tipikal'  => 'Berbagi data ke pihak luar dilakukan berdasarkan permintaan tanpa mekanisme persetujuan formal',
                'mitigasi_tipikal' => 'Tetapkan prosedur persetujuan formal sebelum data dibagikan ke pihak luar; dokumentasikan setiap pertukaran data; pastikan penerima memiliki kewajiban perlindungan yang setara',
            ],
            [
                'nomor_urut'       => 5,
                'deskripsi'        => 'Tidak ada mekanisme yang memastikan keutuhan dan keakuratan data sepanjang siklus hidupnya sehingga perubahan yang tidak sah tidak dapat dideteksi',
                'ancaman_tipikal'  => 'Pihak internal atau eksternal memanipulasi data tanpa meninggalkan jejak yang dapat dideteksi',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Keputusan organisasi didasarkan pada data yang telah dimanipulasi; laporan resmi mengandung informasi yang tidak akurat',
                'area_dampak'      => ['Kinerja', 'Hukum dan Regulasi', 'Reputasi'],
                'kontrol_tipikal'  => 'Keutuhan data diasumsikan terjaga tanpa mekanisme verifikasi yang aktif',
                'mitigasi_tipikal' => 'Terapkan pencatatan setiap perubahan data beserta identitas pelaku dan waktunya; lakukan verifikasi keutuhan data secara berkala',
            ],
        ]);

        // ── SPESIFIK: DATABASE DAN DATA FILES ─────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_DI_DB,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Database dan Data Files.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Akses ke basis data dilakukan langsung tanpa lapisan pengamanan tambahan sehingga siapa pun yang memiliki kredensial basis data dapat mengakses seluruh data',
                'ancaman_tipikal'  => 'Pihak yang mendapatkan kredensial basis data mengakses seluruh data secara langsung tanpa melalui mekanisme kontrol akses aplikasi',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Seluruh data dalam basis data dapat diakses, diubah, atau dihapus secara massal; kontrol akses di tingkat aplikasi menjadi tidak bermakna',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Akses ke basis data menggunakan satu set kredensial yang digunakan bersama oleh semua komponen sistem',
                'mitigasi_tipikal' => 'Pisahkan akun basis data berdasarkan kebutuhan akses setiap komponen; terapkan prinsip hak akses minimum pada tingkat basis data; pantau dan catat seluruh akses langsung',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Data yang tersimpan di basis data tidak dienkripsi sehingga jika basis data diakses secara tidak sah seluruh data dapat langsung dibaca',
                'ancaman_tipikal'  => 'Pihak tidak berwenang yang mendapatkan akses ke berkas basis data secara langsung dapat membaca seluruh isinya tanpa hambatan',
                'kategori'         => 'Pencurian Data Pribadi',
                'dampak_tipikal'   => 'Kebocoran data dalam skala besar; data pribadi dan data rahasia organisasi terekspos sekaligus',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Finansial'],
                'kontrol_tipikal'  => 'Data disimpan dalam format biasa di basis data tanpa enkripsi pada kolom atau berkas yang sensitif',
                'mitigasi_tipikal' => 'Enkripsi kolom atau tabel yang mengandung data sensitif; enkripsi berkas basis data di tingkat penyimpanan; kelola kunci enkripsi secara terpisah dari basis data',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Tidak ada pemisahan yang memadai antara data yang digunakan untuk keperluan pengujian dan data nyata yang berisi informasi sensitif',
                'ancaman_tipikal'  => 'Data nyata yang mengandung informasi sensitif digunakan dalam lingkungan pengujian yang umumnya memiliki kontrol keamanan lebih longgar',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Data pengguna atau data operasional nyata bocor melalui lingkungan pengujian; potensi pelanggaran regulasi perlindungan data',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi'],
                'kontrol_tipikal'  => 'Data dari sistem produksi digunakan langsung di lingkungan pengujian',
                'mitigasi_tipikal' => 'Gunakan data anonim atau data sintetis untuk keperluan pengujian; jangan pernah menggunakan data nyata yang mengandung informasi sensitif di lingkungan pengujian',
            ],
        ]);

        // ── SPESIFIK: DATA LOG DAN AUDIT ──────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_DI_LOG,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Data Log dan Audit.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Catatan log tidak dilindungi dari modifikasi sehingga pihak yang melakukan tindakan tidak sah dapat menghapus atau mengubah jejak aktivitasnya',
                'ancaman_tipikal'  => 'Pihak internal yang melakukan penyalahgunaan menghapus atau memanipulasi catatan log untuk menghilangkan bukti aktivitas yang dilakukan',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Investigasi insiden tidak dapat dilakukan secara efektif; pelaku tidak dapat diidentifikasi; pertanggungjawaban tidak dapat ditegakkan',
                'area_dampak'      => ['Hukum dan Regulasi', 'Kinerja'],
                'kontrol_tipikal'  => 'Catatan log disimpan di sistem yang sama dengan sistem yang dimonitor dan dapat diakses oleh administrator sistem',
                'mitigasi_tipikal' => 'Simpan catatan log di sistem terpisah yang hanya dapat ditambahi (append-only); batasi akses ke catatan log; terapkan tanda waktu yang tidak dapat dimanipulasi',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Catatan log tidak mencakup kejadian yang cukup untuk memungkinkan rekonstruksi aktivitas saat terjadi insiden',
                'ancaman_tipikal'  => 'Pihak tidak berwenang dari luar yang menyusup ke sistem berhasil menyembunyikan jejaknya karena cakupan log tidak mencakup aktivitas yang mereka lakukan',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Penyebab dan cakupan insiden tidak dapat ditentukan; tindakan perbaikan tidak tepat sasaran',
                'area_dampak'      => ['Kinerja', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Log mencatat sebagian kejadian menggunakan pengaturan default tanpa kajian kecukupan cakupan pencatatan',
                'mitigasi_tipikal' => 'Definisikan kejadian apa saja yang harus dicatat berdasarkan kebutuhan investigasi; pastikan log mencakup identitas pelaku, waktu, tindakan, dan objek yang terdampak; uji kecukupan log secara berkala',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Catatan log tidak ditinjau secara berkala sehingga pola aktivitas yang mencurigakan tidak terdeteksi hingga insiden sudah terjadi',
                'ancaman_tipikal'  => 'Pihak tidak berwenang dari luar yang telah menyusup ke sistem beroperasi dalam jangka panjang tanpa diketahui karena tidak ada yang meninjau pola aktivitas secara rutin',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Ancaman yang sudah masuk beroperasi dalam waktu lama; dampak insiden lebih luas karena terlambat terdeteksi',
                'area_dampak'      => ['Operasional TIK', 'Kinerja'],
                'kontrol_tipikal'  => 'Log tersedia namun hanya ditinjau saat ada keluhan atau insiden yang dilaporkan oleh pengguna',
                'mitigasi_tipikal' => 'Tetapkan jadwal peninjauan log secara berkala; konfigurasikan peringatan otomatis untuk pola yang mencurigakan; tunjuk penanggung jawab yang jelas untuk pemantauan log',
            ],
        ]);

        // ── SPESIFIK: DOKUMEN KONTRAK DAN LEGAL ───────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_DI_KTR,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Dokumen Kontrak dan Legal.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Dokumen kontrak dan legal tidak disimpan di tempat yang terkunci dan terlindungi sehingga dapat diakses oleh pihak yang tidak berwenang',
                'ancaman_tipikal'  => 'Pihak tidak berwenang membaca, menyalin, atau mengambil dokumen kontrak yang mengandung informasi komersial dan data sensitif',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Informasi kontrak bocor ke pihak yang bersaing; posisi negosiasi organisasi melemah; potensi pelanggaran klausul kerahasiaan',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Finansial'],
                'kontrol_tipikal'  => 'Dokumen kontrak disimpan di lemari atau sistem penyimpanan yang tidak selalu terkunci',
                'mitigasi_tipikal' => 'Simpan dokumen kontrak di tempat yang terkunci dengan akses terbatas; untuk dokumen elektronik terapkan enkripsi dan kontrol akses berbasis peran; catat setiap akses ke dokumen sensitif',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Tidak ada sistem yang memantau masa berlaku kontrak sehingga kontrak yang sudah berakhir tidak diketahui dan tidak ditindaklanjuti tepat waktu',
                'ancaman_tipikal'  => 'Mitra atau vendor yang kontraknya telah berakhir memanfaatkan ketiadaan pembaruan kontrak untuk terus mengakses aset atau menuntut hak yang tidak berlaku',
                'kategori'         => 'Kesalahan Pengelolaan oleh Pihak Ketiga',
                'dampak_tipikal'   => 'Layanan atau pengadaan berlanjut tanpa landasan kontraktual yang sah; organisasi kehilangan perlindungan hukum',
                'area_dampak'      => ['Hukum dan Regulasi', 'Finansial'],
                'kontrol_tipikal'  => 'Pemantauan masa berlaku kontrak dilakukan secara manual dan bergantung pada ingatan pengelola kontrak',
                'mitigasi_tipikal' => 'Terapkan sistem pencatatan masa berlaku kontrak dengan peringatan otomatis; tetapkan prosedur perpanjangan atau pengakhiran yang jelas; audit status kontrak secara berkala',
            ],
        ]);

        // ── SPESIFIK: PROSES BISNIS/PROSEDUR ──────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_DI_PBP,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Proses Bisnis dan Prosedur.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Prosedur operasional tidak terdokumentasi atau dokumentasinya tidak mutakhir sehingga pelaksanaan bergantung pada pengetahuan individu',
                'ancaman_tipikal'  => 'Pergantian pegawai kunci atau keadaan darurat yang menyebabkan pegawai tidak dapat bekerja membuat proses terganggu karena pengetahuan tidak terdokumentasi',
                'kategori'         => 'Kesalahan Pengelolaan SDM',
                'dampak_tipikal'   => 'Proses operasional berjalan tidak konsisten; kualitas layanan menurun; risiko kesalahan meningkat karena tidak ada panduan yang jelas',
                'area_dampak'      => ['Kinerja', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Prosedur sebagian besar diturunkan secara lisan atau melalui praktik yang tidak tertulis',
                'mitigasi_tipikal' => 'Dokumentasikan seluruh prosedur operasional yang kritikal; tinjau dan perbarui dokumentasi secara berkala; pastikan dokumentasi dapat diakses oleh seluruh pegawai yang memerlukan',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Prosedur tidak mencakup langkah penanganan pengecualian dan kejadian yang tidak biasa sehingga pegawai tidak memiliki panduan saat menghadapi situasi di luar kebiasaan',
                'ancaman_tipikal'  => 'Situasi tidak biasa atau upaya manipulasi yang memanfaatkan ketiadaan prosedur untuk kondisi pengecualian berhasil melewati kontrol yang ada',
                'kategori'         => 'Kesalahan Pengelolaan SDM',
                'dampak_tipikal'   => 'Tindakan yang diambil saat kondisi pengecualian tidak konsisten dan berpotensi menciptakan celah keamanan',
                'area_dampak'      => ['Kinerja', 'Layanan Organisasi', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Prosedur hanya mencakup alur kerja normal; kondisi pengecualian diserahkan sepenuhnya pada kebijaksanaan pegawai',
                'mitigasi_tipikal' => 'Tambahkan panduan penanganan pengecualian dalam setiap prosedur operasional; definisikan kriteria eskalasi yang jelas; pastikan pegawai memahami kapan harus berkonsultasi dengan atasan',
            ],
        ]);

        // ── SPESIFIK: FORMULIR ─────────────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_DI_FRM,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik aset Formulir.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Formulir yang mengumpulkan data sensitif tidak memiliki mekanisme validasi yang memadai sehingga data yang tidak akurat atau berbahaya dapat masuk ke sistem',
                'ancaman_tipikal'  => 'Pihak yang mengisi formulir memasukkan data yang tidak benar atau menyisipkan konten berbahaya yang kemudian diproses oleh sistem',
                'kategori'         => 'Ketidaksesuaian Pengelolaan Aplikasi',
                'dampak_tipikal'   => 'Data yang tidak akurat tersimpan dalam sistem; konten berbahaya yang disisipkan dieksekusi oleh sistem',
                'area_dampak'      => ['Operasional TIK', 'Kinerja'],
                'kontrol_tipikal'  => 'Validasi formulir hanya dilakukan secara visual oleh petugas tanpa mekanisme pemeriksaan otomatis',
                'mitigasi_tipikal' => 'Terapkan validasi otomatis pada setiap formulir; definisikan format yang diizinkan untuk setiap kolom; lakukan pemeriksaan ulang sebelum data disimpan atau diproses',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Formulir fisik yang berisi data sensitif tidak dikendalikan dengan baik sehingga salinan dapat beredar di luar kendali organisasi',
                'ancaman_tipikal'  => 'Formulir fisik yang mengandung data sensitif jatuh ke tangan yang tidak berwenang karena tidak ada kontrol distribusi dan penyimpanan yang memadai',
                'kategori'         => 'Kesalahan Pengelolaan Data dan Informasi Terbatas',
                'dampak_tipikal'   => 'Data sensitif yang tercantum di formulir bocor; formulir dapat dimanipulasi atau dipalsukan; privasi pihak yang mengisi formulir terancam',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi'],
                'kontrol_tipikal'  => 'Formulir fisik didistribusikan tanpa penomoran atau pencatatan; tidak ada prosedur pengendalian salinan',
                'mitigasi_tipikal' => 'Terapkan penomoran dan pencatatan distribusi untuk formulir yang mengandung data sensitif; simpan formulir yang sudah diisi di tempat yang aman; musnahkan formulir yang tidak terpakai sesuai prosedur',
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
