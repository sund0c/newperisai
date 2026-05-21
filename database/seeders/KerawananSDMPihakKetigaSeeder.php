<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KerawananSDMPihakKetigaSeeder extends Seeder
{
    const ID_SK     = 'a1b93053-ccbe-4060-9317-65ff410a95d1';
    const ID_SK_MGT = 'a1b93053-d0cd-48f8-90b7-51b2182c66a6';
    const ID_SK_TEC = 'a1b93053-d0f9-454d-88cf-dd5c97e89d66';
    const ID_SK_OUT = 'a1b93053-d121-4585-815d-32913a031e4b';

    public function run(): void
    {
        // ── GLOBAL SDM DAN PIHAK KETIGA ───────────────────────────────────
        $setId = $this->createSet(
            'global_class',
            self::ID_SK,
            '1.0',
            'Versi awal — kerawanan umum berlaku untuk semua sub-kelas SDM dan Pihak Ketiga.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Tidak ada program pelatihan kesadaran keamanan informasi yang dilakukan secara rutin sehingga pegawai tidak memahami ancaman dan tanggung jawab keamanan mereka',
                'ancaman_tipikal'  => 'Pihak luar memanfaatkan ketidaktahuan pegawai melalui teknik manipulasi sosial, penipuan elektronik, atau permintaan yang tampak sah untuk memperoleh akses atau informasi',
                'kategori'         => 'Kesalahan Pengelolaan SDM',
                'dampak_tipikal'   => 'Pegawai secara tidak sengaja membocorkan informasi sensitif atau memberikan akses ke pihak yang tidak berwenang',
                'area_dampak'      => ['Reputasi', 'Kinerja', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Pelatihan keamanan hanya dilakukan saat orientasi pegawai baru dan tidak diulang secara berkala',
                'mitigasi_tipikal' => 'Selenggarakan pelatihan kesadaran keamanan secara berkala; simulasikan skenario ancaman nyata untuk menguji kesiapan; ukur dan evaluasi efektivitas program secara rutin',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Tidak ada prosedur yang memastikan hak akses dicabut secara menyeluruh dan tepat waktu saat pegawai atau pihak ketiga berpindah jabatan atau mengakhiri keterlibatannya',
                'ancaman_tipikal'  => 'Mantan pegawai atau pihak ketiga yang sudah tidak terlibat masih memiliki akses ke sistem dan data yang seharusnya sudah tidak menjadi haknya',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Mantan pegawai atau pihak ketiga mengakses sistem untuk mengambil data, merusak informasi, atau memperoleh keuntungan tidak sah',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi', 'Reputasi'],
                'kontrol_tipikal'  => 'Pencabutan akses dilakukan secara manual berdasarkan pemberitahuan dari unit SDM; proses tidak terstandar dan sering tertunda',
                'mitigasi_tipikal' => 'Tetapkan prosedur offboarding yang mencakup pencabutan seluruh akses pada hari yang sama dengan berakhirnya masa kerja; buat daftar periksa akses; verifikasi pencabutan setelah selesai',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Tidak ada pemisahan tugas yang memadai sehingga satu individu dapat mengendalikan seluruh proses dari awal hingga akhir tanpa pengawasan pihak lain',
                'ancaman_tipikal'  => 'Pihak luar yang memiliki kepentingan memanfaatkan ketiadaan pemisahan tugas untuk mempengaruhi proses dari luar',
                'kategori'         => 'Kesalahan Pengelolaan SDM',
                'dampak_tipikal'   => 'Kecurangan atau penyalahgunaan berlangsung dalam waktu lama tanpa terdeteksi; kerugian finansial atau reputasi bagi organisasi',
                'area_dampak'      => ['Finansial', 'Reputasi', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Pembagian tugas ditentukan berdasarkan ketersediaan pegawai bukan berdasarkan prinsip keamanan',
                'mitigasi_tipikal' => 'Identifikasi proses kritikal yang memerlukan pemisahan tugas; pastikan tidak ada individu yang memiliki kendali penuh; terapkan mekanisme persetujuan ganda untuk tindakan berisiko tinggi',
            ],
            [
                'nomor_urut'       => 4,
                'deskripsi'        => 'Tidak ada prosedur pelaporan insiden keamanan yang jelas sehingga pegawai tidak tahu harus melapor ke mana jika menemukan atau mengalami insiden',
                'ancaman_tipikal'  => 'Pihak luar yang melancarkan serangan atau melakukan pelanggaran berhasil menghindari penanganan karena korban atau saksi tidak tahu cara melaporkannya',
                'kategori'         => 'Kesalahan Pengelolaan SDM',
                'dampak_tipikal'   => 'Insiden yang sebenarnya sudah diketahui tidak tertangani tepat waktu; dampak insiden meluas karena terlambat ditangani',
                'area_dampak'      => ['Kinerja', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Pelaporan insiden dilakukan secara informal melalui atasan langsung tanpa prosedur yang terstandar',
                'mitigasi_tipikal' => 'Tetapkan prosedur pelaporan insiden yang jelas dengan saluran yang mudah diakses; pastikan pegawai memahami apa yang harus dilaporkan; jamin tidak ada konsekuensi negatif bagi pelapor yang beritikad baik',
            ],
            [
                'nomor_urut'       => 5,
                'deskripsi'        => 'Tidak ada proses pemeriksaan latar belakang yang proporsional sebelum memberikan akses ke sistem dan informasi sensitif',
                'ancaman_tipikal'  => 'Pihak luar yang memiliki niat merugikan organisasi berhasil masuk sebagai pegawai dan mendapatkan akses ke sistem serta informasi sensitif',
                'kategori'         => 'Kesalahan Pengelolaan SDM',
                'dampak_tipikal'   => 'Risiko ancaman dari dalam meningkat secara signifikan; insiden keamanan yang disengaja lebih mungkin terjadi',
                'area_dampak'      => ['Operasional TIK', 'Reputasi', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Pemeriksaan latar belakang hanya dilakukan untuk posisi tertentu atau tidak dilakukan sama sekali',
                'mitigasi_tipikal' => 'Lakukan pemeriksaan latar belakang yang proporsional dengan tingkat akses yang akan diberikan; pertimbangkan aspek keamanan informasi dalam proses seleksi',
            ],
        ]);

        // ── SPESIFIK: MANAGEMENT ──────────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_SK_MGT,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik SDM level Manajemen.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Pimpinan tidak memberikan dukungan yang nyata terhadap program keamanan informasi sehingga program tidak mendapat sumber daya dan perhatian yang cukup',
                'ancaman_tipikal'  => 'Ancaman siber dan kejadian keamanan dari luar tidak dapat ditangani secara efektif karena program keamanan tidak didukung sumber daya yang memadai',
                'kategori'         => 'Kesalahan Pengelolaan SDM',
                'dampak_tipikal'   => 'Program keamanan informasi hanya berjalan di atas kertas tanpa implementasi nyata; organisasi rentan terhadap ancaman yang seharusnya dapat dicegah',
                'area_dampak'      => ['Kinerja', 'Reputasi', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Keamanan informasi dianggap sebagai urusan teknis semata yang didelegasikan sepenuhnya ke tim TI',
                'mitigasi_tipikal' => 'Sosialisasikan risiko keamanan informasi kepada pimpinan dalam bahasa bisnis; libatkan pimpinan dalam pengambilan keputusan terkait keamanan; tetapkan penanggung jawab keamanan di level manajemen',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Tidak ada kebijakan keamanan informasi yang disetujui pimpinan dan dikomunikasikan kepada seluruh pegawai',
                'ancaman_tipikal'  => 'Pihak luar memanfaatkan inkonsistensi perilaku pegawai — yang tidak memiliki panduan baku — untuk mengeksploitasi celah yang timbul dari tindakan yang tidak terstandar',
                'kategori'         => 'Kesalahan Pengelolaan SDM',
                'dampak_tipikal'   => 'Perilaku yang tidak aman dipraktikkan karena tidak ada aturan yang jelas; inkonsistensi dalam penanganan informasi di seluruh organisasi',
                'area_dampak'      => ['Kinerja', 'Hukum dan Regulasi', 'Reputasi'],
                'kontrol_tipikal'  => 'Panduan keamanan informasi ada namun tidak formal, tidak disetujui resmi, dan tidak dikomunikasikan secara menyeluruh',
                'mitigasi_tipikal' => 'Susun kebijakan keamanan informasi yang formal dan disetujui pimpinan; komunikasikan kepada seluruh pegawai; tinjau dan perbarui kebijakan secara berkala',
            ],
        ]);

        // ── SPESIFIK: TECHNICAL ───────────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_SK_TEC,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik SDM level Teknis.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Staf teknis memiliki hak akses yang sangat luas ke seluruh sistem tanpa pembatasan berdasarkan tanggung jawab pekerjaannya',
                'ancaman_tipikal'  => 'Pihak luar yang berhasil mengkompromasi akun staf teknis mendapatkan akses sangat luas ke seluruh sistem',
                'kategori'         => 'Penyalahgunaan Kontrol Akses',
                'dampak_tipikal'   => 'Data di sistem yang tidak menjadi tanggung jawab staf tersebut dapat diakses dan disalahgunakan; penyalahgunaan sulit dideteksi',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Staf teknis diberikan akses luas atas nama efisiensi operasional',
                'mitigasi_tipikal' => 'Terapkan prinsip hak akses minimal untuk staf teknis berdasarkan tanggung jawab spesifiknya; pisahkan akses ke sistem berbeda; pantau aktivitas melalui audit log yang komprehensif',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Tidak ada prosedur yang mengharuskan perubahan konfigurasi sistem melalui proses review dan persetujuan sebelum diterapkan',
                'ancaman_tipikal'  => 'Pihak luar yang memanfaatkan celah akibat perubahan konfigurasi yang tidak melalui review berhasil menyusup atau mengganggu layanan',
                'kategori'         => 'Ketidaksesuaian Pengelolaan Aplikasi',
                'dampak_tipikal'   => 'Perubahan konfigurasi yang tidak diverifikasi menyebabkan gangguan layanan atau membuka celah keamanan; pemulihan membutuhkan waktu karena tidak ada dokumentasi perubahan',
                'area_dampak'      => ['Operasional TIK', 'Kinerja', 'Layanan Organisasi'],
                'kontrol_tipikal'  => 'Perubahan konfigurasi diterapkan langsung oleh staf teknis tanpa proses dokumentasi dan persetujuan yang formal',
                'mitigasi_tipikal' => 'Terapkan prosedur manajemen perubahan yang mengharuskan review, persetujuan, dan uji terlebih dahulu; dokumentasikan setiap perubahan; siapkan prosedur rollback',
            ],
        ]);

        // ── SPESIFIK: TENAGA OUTSOURCE ────────────────────────────────────
        $setId = $this->createSet(
            'subclass',
            self::ID_SK_OUT,
            '1.0',
            'Versi awal — kerawanan tambahan spesifik Tenaga Outsource.'
        );

        $this->insertItems($setId, [
            [
                'nomor_urut'       => 1,
                'deskripsi'        => 'Tenaga outsource diberikan akses ke sistem atau data tanpa pembatasan yang jelas tentang ruang lingkup, masa berlaku, dan tujuan akses',
                'ancaman_tipikal'  => 'Tenaga outsource yang memiliki akses melebihi kebutuhannya menyalahgunakan akses tersebut atau akses yang tidak dicabut setelah kontrak berakhir dimanfaatkan secara tidak sah',
                'kategori'         => 'Kesalahan Pengelolaan oleh Pihak Ketiga',
                'dampak_tipikal'   => 'Data dan sistem diakses melebihi yang diizinkan; akses yang tidak dicabut menjadi celah permanen',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi', 'Reputasi'],
                'kontrol_tipikal'  => 'Akses tenaga outsource diberikan secara luas untuk kemudahan operasional; pencabutan akses setelah kontrak berakhir tidak selalu dilakukan tepat waktu',
                'mitigasi_tipikal' => 'Tetapkan akses tenaga outsource berdasarkan kebutuhan minimum; berikan akses sementara dengan masa berlaku sesuai kontrak; pastikan akses dicabut segera setelah kontrak berakhir',
            ],
            [
                'nomor_urut'       => 2,
                'deskripsi'        => 'Tidak ada perjanjian kerahasiaan yang mengikat tenaga outsource sebelum mereka mendapatkan akses ke informasi sensitif organisasi',
                'ancaman_tipikal'  => 'Tenaga outsource membocorkan atau menggunakan informasi sensitif yang diperoleh selama bekerja untuk kepentingan diri sendiri atau pihak lain',
                'kategori'         => 'Kesalahan Pengelolaan oleh Pihak Ketiga',
                'dampak_tipikal'   => 'Informasi rahasia organisasi bocor melalui tenaga outsource; tidak ada dasar hukum yang kuat untuk menuntut pertanggungjawaban',
                'area_dampak'      => ['Hukum dan Regulasi', 'Reputasi', 'Finansial'],
                'kontrol_tipikal'  => 'Perjanjian kerahasiaan hanya ada dalam kontrak pengadaan jasa secara umum tanpa klausul yang spesifik dan mengikat secara individual',
                'mitigasi_tipikal' => 'Wajibkan penandatanganan perjanjian kerahasiaan individual sebelum memberikan akses ke informasi sensitif; pastikan perjanjian mencakup kewajiban yang jelas dan konsekuensi pelanggaran',
            ],
            [
                'nomor_urut'       => 3,
                'deskripsi'        => 'Tidak ada pengawasan yang memadai terhadap aktivitas tenaga outsource selama mereka bekerja di lingkungan sistem organisasi',
                'ancaman_tipikal'  => 'Tenaga outsource yang bekerja tanpa pengawasan mengakses informasi, sistem, atau area yang bukan menjadi lingkup pekerjaannya',
                'kategori'         => 'Kesalahan Pengelolaan oleh Pihak Ketiga',
                'dampak_tipikal'   => 'Informasi dan aset yang bukan lingkup pekerjaan tenaga outsource diakses tanpa sepengetahuan organisasi',
                'area_dampak'      => ['Operasional TIK', 'Hukum dan Regulasi'],
                'kontrol_tipikal'  => 'Tenaga outsource bekerja secara mandiri tanpa pendampingan atau pemantauan aktivitas yang memadai',
                'mitigasi_tipikal' => 'Dampingi tenaga outsource saat bekerja di area atau sistem yang sensitif; catat aktivitas yang dilakukan; batasi akses fisik dan digital hanya pada yang diperlukan',
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
