<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KlasifikasiAset;
use App\Models\SubKlasifikasiAset;
use App\Models\TahunAktif;




class KlasifikasiSeeder extends Seeder
{
    public function run(): void
    {

        $tahuns = [
            ['tahun' => 2026, 'is_active' => true],
            ['tahun' => 2027, 'is_active' => false],
        ];

        foreach ($tahuns as $item) {
            TahunAktif::firstOrCreate(
                ['tahun' => $item['tahun']],
                ['is_active' => $item['is_active']]
            );
        }

        $this->command->info('  ✓ tahunaktifs — ' . count($tahuns) . ' record(s) seeded.');
        // 1. Insert klasifikasi
        $data = [
            ['kodeklas' => 'DI', 'klasifikasiaset' => 'Data dan Informasi'],
            ['kodeklas' => 'PL', 'klasifikasiaset' => 'Perangkat Lunak'],
            ['kodeklas' => 'PK', 'klasifikasiaset' => 'Perangkat Keras'],
            ['kodeklas' => 'SP', 'klasifikasiaset' => 'Sarana Pendukung'],
            ['kodeklas' => 'SK', 'klasifikasiaset' => 'SDM dan Pihak Ketiga'],
        ];

        foreach ($data as $item) {
            KlasifikasiAset::create($item);
        }

        // 2. Ambil UUID berdasarkan kodeklas
        $di = KlasifikasiAset::where('kodeklas', 'DI')->value('id');
        $pl = KlasifikasiAset::where('kodeklas', 'PL')->value('id');
        $pk = KlasifikasiAset::where('kodeklas', 'PK')->value('id');
        $sp = KlasifikasiAset::where('kodeklas', 'SP')->value('id');
        $sk = KlasifikasiAset::where('kodeklas', 'SK')->value('id');

        // 3. Insert sub klasifikasi pakai UUID
        $subData = [
            ['klasifikasi_aset_id' => $di, 'subklasifikasiaset' => 'Proses Bisnis/Prosedur',           'penjelasan' => 'Dokumen yang berisikan panduan atau instruksi untuk melakukan suatu kegiatan. Contoh : SOP tentang keamanan informasi, Pedoman Penanganan Insiden, Renstra Organisasi.'],
            ['klasifikasi_aset_id' => $di, 'subklasifikasiaset' => 'Formulir',                          'penjelasan' => 'Dokumen yang berisikan sejumlah pertanyaan atau kolom isian. Contoh : checklist backup data, formulir permintaan akses.'],
            ['klasifikasi_aset_id' => $di, 'subklasifikasiaset' => 'Data Log dan Audit',                'penjelasan' => 'Dokumen yang berisikan log/riwayat dan/atau hasil audit. Contoh : data kepegawaian, change request, laporan hasil indeks kami, laporan hasil audit keamanan, laporan IT security assessment.'],
            ['klasifikasi_aset_id' => $di, 'subklasifikasiaset' => 'Database dan data files',           'penjelasan' => 'Dokumen yang tersimpan dalam database atau data yang berupa sumber program. Contoh : source code aplikasi.'],
            ['klasifikasi_aset_id' => $di, 'subklasifikasiaset' => 'Dokumen Kontrak dan Legal',         'penjelasan' => 'Dokumen kontrak yang terkait dengan layanan organisasi dan hukum. Contoh : kontrak dengan penyedia ISP, dokumen Perjanjian Kerahasiaan/Non-Disclosure Agreement, dokumen MoU / Perjanjian Kerjasama.'],
            ['klasifikasi_aset_id' => $pk, 'subklasifikasiaset' => 'PC/Laptop/Smartphone',              'penjelasan' => 'Perangkat operasional yang mendukung layanan organisasi. Contoh : PC, Laptop, Smartphone.'],
            ['klasifikasi_aset_id' => $pk, 'subklasifikasiaset' => 'Server',                            'penjelasan' => 'Perangkat operasional yang mendukung pengembangan perangkat lunak. Contoh : rak server, server development, server production.'],
            ['klasifikasi_aset_id' => $pk, 'subklasifikasiaset' => 'Perangkat Jaringan (Network Device)', 'penjelasan' => 'Perangkat Jaringan TI. Contoh: firewall, router, switch, repeater, bridge, access point, kabel jaringan.'],
            ['klasifikasi_aset_id' => $pk, 'subklasifikasiaset' => 'Perangkat Penyimpanan (Storage Device)', 'penjelasan' => 'Perangkat yang digunakan untuk menyimpan data/informasi. Contoh: hardisk, flashdisk.'],
            ['klasifikasi_aset_id' => $pl, 'subklasifikasiaset' => 'Sistem Operasi',                    'penjelasan' => 'Sistem operasi. Contoh : OS server.'],
            ['klasifikasi_aset_id' => $pl, 'subklasifikasiaset' => 'Sistem Utility',                    'penjelasan' => 'Perangkat lunak yang digunakan untuk membantu mengelola, memelihara, dan mengoptimalkan kinerja sistem komputer (diluar bawaan sistem operasi). Contoh: Antivirus Kaspersky, WSUS, Citrix.'],
            ['klasifikasi_aset_id' => $pl, 'subklasifikasiaset' => 'Aplikasi berbasis Website',         'penjelasan' => 'Perangkat lunak yang diakses melalui browser.'],
            ['klasifikasi_aset_id' => $pl, 'subklasifikasiaset' => 'Aplikasi berbasis Mobile',          'penjelasan' => 'Perangkat lunak yang diakses/dijalankan melalui perangkat mobile.'],
            ['klasifikasi_aset_id' => $sp, 'subklasifikasiaset' => 'Support Appliance',                 'penjelasan' => 'Perangkat pendukung sebagai bagian dari fasilitas pendukung. Contoh : Genset, UPS, APAR, Smoke Detector, Sensor suhu dan kelembapan.'],
            ['klasifikasi_aset_id' => $sp, 'subklasifikasiaset' => 'Support Facility',                  'penjelasan' => 'Lokasi yang mendukung operasional data center. Contoh: lokasi DRC, backup site.'],
            ['klasifikasi_aset_id' => $sk, 'subklasifikasiaset' => 'Management',                        'penjelasan' => 'Personil pelaksana proses penyediaan layanan TI pada tingkat manajerial.'],
            ['klasifikasi_aset_id' => $sk, 'subklasifikasiaset' => 'Technical',                         'penjelasan' => 'Personil pelaksana teknis proses penyediaan layanan TI.'],
            ['klasifikasi_aset_id' => $sk, 'subklasifikasiaset' => 'Tenaga Outsource',                  'penjelasan' => 'Pihak ketiga/personil yang bekerja sama dalam pelaksanaan pekerjaan selama jangka waktu tertentu.'],
            ['klasifikasi_aset_id' => $pl, 'subklasifikasiaset' => 'Aplikasi berbasis Desktop',         'penjelasan' => 'Perangkat lunak yang diakses melalui desktop.'],
        ];

        foreach ($subData as $item) {
            SubKlasifikasiAset::create($item);
        }
    }
}
