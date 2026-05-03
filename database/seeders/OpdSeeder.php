<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('opds')->insert([
            ['id' => 1,  'namaopd' => 'DINAS KOMUNIKASI, INFORMATIKA, DAN STATISTIK',                              'created_at' => '2025-09-26 09:33:48', 'updated_at' => '2025-09-26 09:33:48'],
            ['id' => 2,  'namaopd' => 'DINAS PERINDUSTRIAN DAN PERDAGANGAN',                                       'created_at' => '2025-09-29 09:38:55', 'updated_at' => '2025-09-29 09:38:55'],
            ['id' => 3,  'namaopd' => 'BADAN PERENCANAAN PEMBANGUNAN DAERAH',                                      'created_at' => '2025-09-29 10:28:30', 'updated_at' => '2025-09-29 10:28:30'],
            ['id' => 4,  'namaopd' => 'INSPEKTORAT DAERAH',                                                        'created_at' => '2025-09-29 14:09:35', 'updated_at' => '2025-09-29 14:09:35'],
            ['id' => 5,  'namaopd' => 'DINAS KESEHATAN',                                                           'created_at' => '2025-09-29 19:47:55', 'updated_at' => '2025-09-29 19:47:55'],
            ['id' => 6,  'namaopd' => 'DINAS PERTANIAN DAN KETAHANAN PANGAN',                                      'created_at' => '2025-09-30 08:08:57', 'updated_at' => '2025-09-30 08:08:57'],
            ['id' => 7,  'namaopd' => 'DINAS KETENAGAKERJAAN DAN ENERGI SUMBER DAYA MINERAL',                      'created_at' => '2025-09-30 08:48:34', 'updated_at' => '2025-09-30 08:48:34'],
            ['id' => 8,  'namaopd' => 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU',                    'created_at' => '2025-09-30 10:11:10', 'updated_at' => '2025-09-30 10:11:10'],
            ['id' => 9,  'namaopd' => 'DINAS PEKERJAAN UMUM, PENATAAN RUANG, PERUMAHAN, DAN KAWASAN PERMUKIMAN',   'created_at' => '2025-09-30 15:02:59', 'updated_at' => '2025-09-30 15:02:59'],
            ['id' => 10, 'namaopd' => 'BIRO UMUM',                                                                 'created_at' => '2025-10-02 08:13:58', 'updated_at' => '2025-11-24 11:31:11'],
            ['id' => 11, 'namaopd' => 'BADAN PENANGGULANGAN BENCANA DAERAH',                                       'created_at' => '2025-10-02 08:32:47', 'updated_at' => '2025-10-02 08:32:47'],
            ['id' => 12, 'namaopd' => 'BIRO ORGANISASI',                                                           'created_at' => '2025-10-02 09:27:30', 'updated_at' => '2025-10-02 09:27:30'],
            ['id' => 13, 'namaopd' => 'DINAS PEMAJUAN MASYARAKAT ADAT',                                            'created_at' => '2025-10-06 08:24:11', 'updated_at' => '2025-10-06 08:24:11'],
            ['id' => 14, 'namaopd' => 'BADAN PENGELOLA KEUANGAN DAN ASET DAERAH',                                  'created_at' => '2025-10-07 15:19:25', 'updated_at' => '2025-10-07 15:19:25'],
            ['id' => 15, 'namaopd' => 'BIRO HUKUM',                                                                'created_at' => '2025-10-24 08:51:57', 'updated_at' => '2025-10-24 08:51:57'],
            ['id' => 16, 'namaopd' => 'DINAS PEMBERDAYAAN MASYARAKAT, DESA, KEPENDUDUKAN, DAN PENCATATAN SIPIL',   'created_at' => '2025-10-24 09:03:44', 'updated_at' => '2025-10-24 09:03:44'],
            ['id' => 17, 'namaopd' => 'BIRO PENGADAAN BARANG/JASA DAN PEREKONOMIAN',                               'created_at' => '2025-10-24 09:14:49', 'updated_at' => '2025-10-24 09:14:49'],
            ['id' => 18, 'namaopd' => 'DINAS PERHUBUNGAN',                                                         'created_at' => '2025-10-24 09:14:49', 'updated_at' => '2025-10-24 09:14:49'],
            ['id' => 19, 'namaopd' => 'BIRO PEMERINTAHAN DAN KESEJAHTERAAN RAKYAT',                                'created_at' => '2025-10-24 09:14:50', 'updated_at' => '2025-10-24 09:14:50'],
            ['id' => 20, 'namaopd' => 'DINAS PARIWISATA',                                                          'created_at' => '2025-10-24 09:14:51', 'updated_at' => '2025-10-24 09:14:51'],
            ['id' => 21, 'namaopd' => 'DINAS KEBUDAYAAN',                                                          'created_at' => '2025-10-24 09:18:36', 'updated_at' => '2025-10-24 09:18:36'],
            ['id' => 22, 'namaopd' => 'BADAN KESATUAN BANGSA DAN POLITIK',                                         'created_at' => '2025-10-24 09:23:00', 'updated_at' => '2025-10-24 09:23:00'],
            ['id' => 23, 'namaopd' => 'BADAN RISET DAN INOVASI DAERAH',                                            'created_at' => '2025-10-24 09:32:32', 'updated_at' => '2025-10-24 09:32:32'],
            ['id' => 24, 'namaopd' => 'DINAS KOPERASI, USAHA KECIL, DAN MENENGAH',                                 'created_at' => '2025-10-24 10:15:17', 'updated_at' => '2025-10-24 10:15:17'],
            ['id' => 25, 'namaopd' => 'BADAN PENDAPATAN DAERAH',                                                   'created_at' => '2025-10-24 10:23:44', 'updated_at' => '2025-10-24 10:23:44'],
            ['id' => 26, 'namaopd' => 'RUMAH SAKIT JIWA',                                                          'created_at' => '2025-10-24 12:38:17', 'updated_at' => '2025-10-24 12:38:17'],
            ['id' => 27, 'namaopd' => 'RUMAH SAKIT UMUM DAERAH BALI MANDARA',                                      'created_at' => '2025-10-24 20:57:01', 'updated_at' => '2025-10-24 20:57:01'],
            ['id' => 28, 'namaopd' => 'DINAS PENDIDIKAN, KEPEMUDAAN, DAN OLAH RAGA',                               'created_at' => '2025-10-25 12:14:54', 'updated_at' => '2025-10-25 12:14:54'],
            ['id' => 29, 'namaopd' => 'RUMAH SAKIT MATA BALI MANDARA',                                             'created_at' => '2025-10-27 09:46:11', 'updated_at' => '2025-10-27 09:46:11'],
            ['id' => 30, 'namaopd' => 'DINAS SOSIAL, PEMBERDAYAAN PEREMPUAN, DAN PERLINDUNGAN ANAK',               'created_at' => '2025-10-27 12:16:06', 'updated_at' => '2025-10-27 12:16:06'],
            ['id' => 31, 'namaopd' => 'DINAS KELAUTAN DAN PERIKANAN',                                              'created_at' => '2025-10-29 14:43:54', 'updated_at' => '2025-10-29 14:43:54'],
            ['id' => 32, 'namaopd' => 'BADAN PENGHUBUNG',                                                          'created_at' => '2025-10-31 13:51:00', 'updated_at' => '2025-10-31 13:51:00'],
            ['id' => 33, 'namaopd' => 'SEKRETARIAT DEWAN PERWAKILAN RAKYAT DAERAH',                                'created_at' => '2025-11-06 07:59:57', 'updated_at' => '2025-11-06 07:59:57'],
            ['id' => 34, 'namaopd' => 'SATUAN POLISI PAMONG PRAJA',                                                'created_at' => '2025-11-17 10:31:13', 'updated_at' => '2025-11-17 10:31:13'],
            ['id' => 35, 'namaopd' => 'BIRO HUMAS DAN PROTOKOL',                                                   'created_at' => '2025-11-24 09:08:22', 'updated_at' => '2025-11-24 09:08:22'],
            ['id' => 36, 'namaopd' => 'RUMAH SAKIT UMUM DHARMA YADNYA',                                            'created_at' => '2025-11-24 09:24:16', 'updated_at' => '2025-11-24 09:24:16'],
            ['id' => 37, 'namaopd' => 'DINAS KEHUTANAN DAN LINGKUNGAN HIDUP',                                      'created_at' => '2025-11-24 09:43:12', 'updated_at' => '2025-11-24 09:43:12'],
            ['id' => 38, 'namaopd' => 'UPTD TURYAPADA TOWER KOMUNIKASI BALI SMART 6.0 KERTHI BALI',               'created_at' => '2025-11-25 10:55:07', 'updated_at' => '2025-11-25 10:55:07'],
            ['id' => 39, 'namaopd' => 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA',                    'created_at' => '2025-11-25 12:23:11', 'updated_at' => '2025-11-25 12:23:11'],
            ['id' => 40, 'namaopd' => 'UPTD PENGEMBANGAN DAN INTEGRASI LAYANAN DIGITAL',                           'created_at' => '2025-11-25 13:40:25', 'updated_at' => '2025-11-25 13:40:25'],
        ]);
    }
}
