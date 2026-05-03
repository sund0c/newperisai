<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KlasifikasiAset;
use App\Models\SubKlasifikasiAset;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Schema;


class KlasifikasiAsetController extends Controller
{
    public function index()
    {
        $klasifikasis = KlasifikasiAset::all();
        return view('admin.klasifikasi.index', compact('klasifikasis'));
    }

    public function show(KlasifikasiAset $klasifikasi)
    {
        $subklasifikasis = SubKlasifikasiAset::withTrashed()->where('klasifikasi_aset_id', $klasifikasi->id)
            ->orderBy('subklasifikasiaset')
            ->get();

        return view('admin.klasifikasi.show', compact('klasifikasi', 'subklasifikasis'));
    }


    public function storeSubklas(Request $request, KlasifikasiAset $klasifikasi)
    {
        $request->validate([
            'subklasifikasiaset' => ['required', 'string', 'max:255'],
            'penjelasan'         => ['nullable', 'string'],
        ]);

        SubKlasifikasiAset::create([
            'klasifikasi_aset_id' => $klasifikasi->id,
            'subklasifikasiaset'  => $request->subklasifikasiaset,
            'penjelasan'          => $request->penjelasan,
        ]);

        return redirect()
            ->route('admin.klasifikasi.show', $klasifikasi)
            ->with('success', 'Sub klasifikasi berhasil ditambahkan.');
    }

    public function updateSubklas(Request $request, KlasifikasiAset $klasifikasi, SubKlasifikasiAset $subklasifikasi)
    {
        $request->validate([
            'subklasifikasiaset' => ['required', 'string', 'max:255'],
            'penjelasan'         => ['nullable', 'string'],
        ]);

        $subklasifikasi->update([
            'subklasifikasiaset' => $request->subklasifikasiaset,
            'penjelasan'         => $request->penjelasan,
        ]);

        return redirect()
            ->route('admin.klasifikasi.show', $klasifikasi)
            ->with('success', 'Sub klasifikasi berhasil diperbarui.');
    }

    public function destroySubklas(KlasifikasiAset $klasifikasi, SubKlasifikasiAset $subklasifikasi)
    {
        $subklasifikasi->delete();

        return redirect()
            ->route('admin.klasifikasi.show', $klasifikasi)
            ->with('success', 'Sub klasifikasi berhasil dihapus.');
    }

    public function restoreSubklas(KlasifikasiAset $klasifikasi, $id)
    {
        $sub = SubKlasifikasiAset::withTrashed()
            ->where('id', $id)
            ->where('klasifikasi_aset_id', $klasifikasi->id)
            ->firstOrFail();

        $sub->restore();

        return redirect()
            ->route('admin.klasifikasi.show', $klasifikasi)
            ->with('success', 'Sub klasifikasi berhasil dipulihkan.');
    }

    // public function create()
    // {
    //     return view('admin.klasifikasiaset.create');
    // }
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'klasifikasiaset' => 'required|string|max:255',
    //     ]);

    //     KlasifikasiAset::create($request->only('klasifikasiaset'));
    //     return redirect()->route('klasifikasiaset.index')->with('success', 'Klasifikasi berhasil ditambahkan.');
    // }
    // public function edit(KlasifikasiAset $klasifikasiaset)
    // {
    //     return view('admin.klasifikasiaset.edit', compact('klasifikasiaset'));
    // }
    // public function update(Request $request, KlasifikasiAset $klasifikasiaset)
    // {
    //     $request->validate([
    //         'klasifikasiaset' => 'required|string|max:255',
    //     ]);

    //     $klasifikasiaset->update($request->only('klasifikasiaset'));
    //     return redirect()->route('klasifikasiaset.index')->with('success', 'Klasifikasi berhasil diupdate.');
    // }
    // public function destroy(KlasifikasiAset $klasifikasiaset)
    // {
    //     $klasifikasiaset->delete();
    //     return redirect()->route('klasifikasiaset.index')->with('success', 'Klasifikasi berhasil dihapus.');
    // }
}
