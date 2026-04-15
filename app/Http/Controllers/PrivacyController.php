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
            'effectiveDate' => '1 Januari 2025',
            'lastUpdated'   => '4 April 2025',

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
            'contactEmail'   => 'csirt@baliprov.go.id',
            'contactPhone'   => '(0361) 225859',
            'contactAddress' => 'Jl. DI Panjautan No 7 Denpasar, Bali 80235',
        ]);
    }
}
