<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PrivacyController extends Controller
{
    /**
     * Tampilkan halaman kebijakan privasi CSIRT Bali.
     */
    public function index(): View
    {
        return view('privacy', [

            // Tanggal kebijakan
            'effectiveDate' => '1 Januari 2024',
            'lastUpdated'   => '15 April 2025',

            // Daftar tujuan penggunaan data
            'usages' => [
                'Memproses dan menindaklanjuti laporan insiden keamanan siber yang Anda kirimkan.',
                'Menghubungi Anda untuk klarifikasi atau informasi tambahan terkait laporan.',
                'Berkoordinasi dengan instansi pemerintah terkait dalam penanganan insiden.',
                'Menghasilkan laporan statistik insiden siber yang bersifat anonim.',
                'Meningkatkan kualitas layanan dan sistem pelaporan CSIRT Bali.',
                'Memenuhi kewajiban hukum dan regulasi yang berlaku.',
            ],

            // Informasi kontak DPO
            'contactEmail'   => 'dpo@csirt.baliprov.go.id',
            'contactPhone'   => '(0361) 123-4567',
            'contactAddress' => 'Jl. Basuki Rahmat No. 1, Denpasar, Bali 80232',
        ]);
    }
}
