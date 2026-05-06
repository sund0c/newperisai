<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAktif;
use Illuminate\Http\Request;

class TahunAktifController extends Controller
{
    public function index()
    {
        $tahuns = TahunAktif::orderBy('tahun', 'desc')->get();

        return view('admin.tahunaktif.index', compact('tahuns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => [
                'required',
                'integer',
                'min:2020',
                'max:2099',
                'unique:tahunaktifs,tahun',
            ],
        ], [
            'tahun.unique' => 'Tahun tersebut sudah terdaftar.',
            'tahun.min'    => 'Tahun minimal adalah 2020.',
            'tahun.max'    => 'Tahun maksimal adalah 2099.',
        ]);

        TahunAktif::create([
            'tahun'     => $request->tahun,
            'is_active' => false,
        ]);

        return back()->with('success', "Tahun {$request->tahun} berhasil ditambahkan.");
    }

    public function activate(TahunAktif $tahunAktif)
    {
        $tahunAktif->activate();

        return back()->with('success', "Tahun {$tahunAktif->tahun} berhasil diaktifkan.");
    }

    public function deactivate(TahunAktif $tahunAktif)
    {
        if (!$tahunAktif->is_active) {
            return back()->with('error', "Tahun {$tahunAktif->tahun} memang sudah tidak aktif.");
        }

        $tahunAktif->deactivate();

        return back()->with('success', "Tahun {$tahunAktif->tahun} berhasil dinonaktifkan.");
    }

    public function destroy(TahunAktif $tahunAktif)
    {
        if ($tahunAktif->is_active) {
            return back()->with('error', "Tahun {$tahunAktif->tahun} tidak dapat dihapus karena sedang aktif.");
        }

        // TODO: cek apakah sudah ada asset yang terikat ke tahun ini
        // if ($tahunAktif->assets()->exists()) {
        //     return back()->with('error', "Tahun {$tahunAktif->tahun} tidak dapat dihapus — sudah memiliki data aset.");
        // }

        $tahunAktif->delete();

        return back()->with('success', "Tahun {$tahunAktif->tahun} berhasil dihapus.");
    }
}
