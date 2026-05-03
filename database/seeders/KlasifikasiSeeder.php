<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KlasifikasiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('klasifikasi_asets')->insert([
            ['id' => 1,  'kodeklas' => 'DI', 'klasifikasiaset' => 'Data dan Informasi'],
            ['id' => 2,  'kodeklas' => 'PL', 'klasifikasiaset' => 'Perangkat Lunak'],
            ['id' => 3,  'kodeklas' => 'PK', 'klasifikasiaset' => 'Perangkat Keras'],
            ['id' => 4,  'kodeklas' => 'SP', 'klasifikasiaset' => 'Sarana Pendukung'],
            ['id' => 5,  'kodeklas' => 'SK', 'klasifikasiaset' => 'SDM dan Pihak Ketiga'],
        ]);
        DB::table('sub_klasifikasi_asets')->insert([
            ['id' => 1, 'klasifikasi_aset_id' => 1,  'subklasifikasiaset' => 'Proses Bisnis/Prosedur', 'penjelasan' => 'Dokumen yang berisikan panduan atau instruksi untuk melakukan suatu kegiatan. Contoh : SOP tentang keamanan informasi, Pedoman Penanganan Insiden, Renstra Organisasi.'],
            ['id' => 2, 'klasifikasi_aset_id' => 1,  'subklasifikasiaset' => 'Formulir', 'penjelasan' => 'Dokumen yang berisikan sejumlah pertanyaan atau kolom isian. Contoh : checklist backup data,  formulir permintaan akses.'],
            ['id' => 3, 'klasifikasi_aset_id' => 1,  'subklasifikasiaset' => 'Data Log dan Audit', 'penjelasan' => 'Dokumen yang berisikan log/riwayat dan/atau hasil audit. Contoh : data kepegawaian, change request, laporan hasil indeks kami, laporan hasil audit keamanan, laporan IT security assessment.'],
            ['id' => 4, 'klasifikasi_aset_id' => 1,  'subklasifikasiaset' => 'Database dan data files', 'penjelasan' => 'Dokumen yang tersimpan dalam database atau data yang berupa sumber program. Contoh : source code aplikasi.'],
            ['id' => 5, 'klasifikasi_aset_id' => 1,  'subklasifikasiaset' => 'Dokumen Kontrak dan Legal', 'penjelasan' => 'Dokumen kontrak yang terkait dengan layanan organisasi dan hukum. Contoh : kontrak dengan penyedia ISP, dokumen Perjanjian Kerahasiaan/Non-Disclosure Agreement, dokumen MoU / Perjanjian Kerjasama.'],
            ['id' => 6, 'klasifikasi_aset_id' => 3,  'subklasifikasiaset' => 'PC/Laptop/Smartphone', 'penjelasan' => 'Perangkat operasional yang mendukung layanan organisasi. Contoh : PC, Laptop, Smartphone. '],
            ['id' => 7, 'klasifikasi_aset_id' => 3,  'subklasifikasiaset' => 'Server', 'penjelasan' => 'Perangkat operasional yang mendukung pengembangan perangkat lunak. Contoh : rak server, server development, server production.'],
            ['id' => 8, 'klasifikasi_aset_id' => 3,  'subklasifikasiaset' => 'Perangkat Jaringan (Network Device)', 'penjelasan' => 'Perangkat Jaringan TI. Contoh: firewall, router, switch, repeater, bridge, access point, kabel jaringan.'],
            ['id' => 9, 'klasifikasi_aset_id' => 3,  'subklasifikasiaset' => 'Perangkat Penyimpanan (Storage Device)', 'penjelasan' => 'Perangkat yang digunakan untuk menyimpan data/informasi. Contoh: hardisk, flashdisk.'],
            ['id' => 10, 'klasifikasi_aset_id' => 2,  'subklasifikasiaset' => 'Sistem Operasi', 'penjelasan' => 'Sistem operasi. Contoh : OS server.'],
            ['id' => 11, 'klasifikasi_aset_id' => 2,  'subklasifikasiaset' => 'Sistem Utility', 'penjelasan' => 'Perangkat lunak yang digunakan untuk membantu mengelola, memelihara, dan mengoptimalkan kinerja sistem komputer (diluar bawaan sistem operasi). Contoh: Antivirus Kaspersky, WSUS, Citrix.'],
            ['id' => 12, 'klasifikasi_aset_id' => 2,  'subklasifikasiaset' => 'Aplikasi berbasis Website', 'penjelasan' => 'Perangkat lunak yang diakses melalui browser'],
            ['id' => 13, 'klasifikasi_aset_id' => 2,  'subklasifikasiaset' => 'Aplikasi berbasis Mobile', 'penjelasan' => 'Perangkat lunak yang diakses/dijalankan melalui perangkat mobile'],
            ['id' => 14, 'klasifikasi_aset_id' => 4,  'subklasifikasiaset' => 'Support Appliance', 'penjelasan' => 'Perangkat pendukung sebagai bagian dari fasilitas pendukung. Contoh : Genset, UPS, APAR, Smoke Detector, Sensor suhu dan kelembapan.'],
            ['id' => 15, 'klasifikasi_aset_id' => 4,  'subklasifikasiaset' => 'Support Facility', 'penjelasan' => 'Lokasi yang mendukung operasional data center. Contoh: lokasi DRC, backup site.'],
            ['id' => 16, 'klasifikasi_aset_id' => 5,  'subklasifikasiaset' => 'Management', 'penjelasan' => 'Personil pelaksana proses penyediaan layanan TI pada tingkat manajerial'],
            ['id' => 17, 'klasifikasi_aset_id' => 5,  'subklasifikasiaset' => 'Technical', 'penjelasan' => 'Personil pelaksana teknis proses penyediaan layanan TI'],
            ['id' => 18, 'klasifikasi_aset_id' => 5,  'subklasifikasiaset' => 'Tenaga Outsource', 'penjelasan' => 'Pihak ketiga/personil yang bekerja sama dalam pelaksanaan pekerjaan selama jangka waktu tertentu'],
            ['id' => 19, 'klasifikasi_aset_id' => 2,  'subklasifikasiaset' => 'Aplikasi berbasis Desktop', 'penjelasan' => 'Perangkat lunak yang diakses melalui desktop'],
        ]);
    }
}
