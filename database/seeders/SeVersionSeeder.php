<?php

namespace Database\Seeders;

use App\Models\SeVersion;
use App\Models\SeIndikator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeVersionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $version = SeVersion::create([
                'kode'      => 'SE-V001',
                'nama'      => 'Versi 1',
                'deskripsi' => 'Versi Indeks KAMI',
                'is_active' => true,
            ]);

            $indikators = [
                [
                    'urutan'     => 1,
                    'pertanyaan' => 'Nilai investasi sistem elektronik yang terpasang',
                    'keterangan' => null,
                    'pilihan_1'  => 'Lebih dari Rp.30 Miliar',
                    'pilihan_2'  => 'Lebih dari Rp.3 Miliar s/d Rp.30 Miliar',
                    'pilihan_3'  => 'Kurang dari Rp.3 Miliar',
                ],
                [
                    'urutan'     => 2,
                    'pertanyaan' => 'Total anggaran operasional tahunan yang dialokasikan untuk pengelolaan Sistem Elektronik',
                    'keterangan' => null,
                    'pilihan_1'  => 'Lebih dari Rp.10 Miliar',
                    'pilihan_2'  => 'Lebih dari Rp.1 Miliar s/d Rp.10 Miliar',
                    'pilihan_3'  => 'Kurang dari Rp.1 Miliar',
                ],
                [
                    'urutan'     => 3,
                    'pertanyaan' => 'Memiliki kewajiban kepatuhan terhadap Peraturan atau Standar tertentu',
                    'keterangan' => null,
                    'pilihan_1'  => 'Peraturan atau Standar nasional dan internasional',
                    'pilihan_2'  => 'Peraturan atau Standar nasional',
                    'pilihan_3'  => 'Tidak ada Peraturan khusus',
                ],
                [
                    'urutan'     => 4,
                    'pertanyaan' => 'Menggunakan teknik kriptografi khusus untuk keamanan informasi dalam Sistem Elektronik',
                    'keterangan' => null,
                    'pilihan_1'  => 'Teknik kriptografi khusus yang disertifikasi oleh Negara',
                    'pilihan_2'  => 'Teknik kriptografi sesuai standar industri, tersedia secara publik atau dikembangkan sendiri',
                    'pilihan_3'  => 'Tidak ada penggunaan teknik kriptografi',
                ],
                [
                    'urutan'     => 5,
                    'pertanyaan' => 'Jumlah pengguna Sistem Elektronik',
                    'keterangan' => null,
                    'pilihan_1'  => 'Lebih dari 5.000 pengguna',
                    'pilihan_2'  => '1.000 sampai dengan 5.000 pengguna',
                    'pilihan_3'  => 'Kurang dari 1.000 pengguna',
                ],
                [
                    'urutan'     => 6,
                    'pertanyaan' => 'Data pribadi yang dikelola Sistem Elektronik',
                    'keterangan' => null,
                    'pilihan_1'  => 'Data pribadi yang memiliki hubungan dengan Data Pribadi lainnya',
                    'pilihan_2'  => 'Data pribadi individu dan/atau terkait kepemilikan badan usaha',
                    'pilihan_3'  => 'Tidak ada data pribadi',
                ],
                [
                    'urutan'     => 7,
                    'pertanyaan' => 'Tingkat klasifikasi/kekritisan Data terhadap ancaman keamanan informasi',
                    'keterangan' => null,
                    'pilihan_1'  => 'Sangat Rahasia',
                    'pilihan_2'  => 'Rahasia dan/atau Terbatas',
                    'pilihan_3'  => 'Biasa',
                ],
                [
                    'urutan'     => 8,
                    'pertanyaan' => 'Tingkat kekritisan proses dalam Sistem Elektronik terhadap ancaman keamanan informasi',
                    'keterangan' => null,
                    'pilihan_1'  => 'Proses berdampak langsung pada layanan publik dan hajat hidup orang banyak',
                    'pilihan_2'  => 'Proses berdampak tidak langsung pada hajat hidup orang banyak',
                    'pilihan_3'  => 'Proses hanya berdampak pada bisnis internal perusahaan',
                ],
                [
                    'urutan'     => 9,
                    'pertanyaan' => 'Dampak dari kegagalan Sistem Elektronik',
                    'keterangan' => null,
                    'pilihan_1'  => 'Membahayakan pertahanan keamanan negara',
                    'pilihan_2'  => 'Layanan publik nasional atau sektor lain terganggu',
                    'pilihan_3'  => 'Gangguan layanan publik 1 provinsi / instansi',
                ],
                [
                    'urutan'     => 10,
                    'pertanyaan' => 'Potensi kerugian dari insiden keamanan informasi (sabotase, terorisme)',
                    'keterangan' => null,
                    'pilihan_1'  => 'Menimbulkan korban jiwa',
                    'pilihan_2'  => 'Kerugian finansial',
                    'pilihan_3'  => 'Gangguan operasional sementara',
                ],
            ];

            foreach ($indikators as $data) {
                SeIndikator::create(array_merge($data, [
                    'se_version_id' => $version->id,
                ]));
            }
        });
    }
}
