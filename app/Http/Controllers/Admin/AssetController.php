<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Opd;
use App\Models\SubKlasifikasiAset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['opd', 'subKlasifikasi'])->withTrashed();

        if ($search = $request->search) {
            $query->where(
                fn($q) => $q
                    ->where('nama_aset', 'like', "%{$search}%")
                    ->orWhere('kode_aset', 'like', "%{$search}%")
            );
        }

        if ($opdId = $request->opd_id) {
            $query->where('opd_id', $opdId);
        }

        if ($klasifikasi = $request->klasifikasi) {
            $query->whereHas(
                'subKlasifikasi',
                fn($q) =>
                $q->where('klasifikasi', $klasifikasi)
            );
        }

        if ($request->status === 'deleted') {
            $query->onlyTrashed();
        } elseif ($request->status === 'active') {
            $query->withoutTrashed();
        }

        $sortBy    = in_array($request->sort, ['kode_aset', 'nama_aset', 'created_at'])
            ? $request->sort : 'created_at';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $direction);

        $assets          = $query->paginate(20)->withQueryString();
        $opds            = Opd::orderBy('namaopd')->get();
        $subKlasifikasis = SubKlasifikasiAset::orderBy('klasifikasi')->orderBy('nama')->get()->groupBy('klasifikasi');

        return view('admin.assets.index', compact(
            'assets',
            'opds',
            'subKlasifikasis',
            'sortBy',
            'direction'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opd_id'             => ['required', 'uuid', 'exists:opds,id'],
            'sub_klasifikasi_id' => ['required', 'uuid', 'exists:sub_klasifikasis,id'],
            'kode_aset'          => ['required', 'string', 'max:30', 'unique:assets,kode_aset'],
            'nama_aset'          => ['required', 'string', 'max:200'],
        ]);

        Asset::create([
            'opd_id'             => $request->opd_id,
            'sub_klasifikasi_id' => $request->sub_klasifikasi_id,
            'kode_aset'          => $request->kode_aset,
            'nama_aset'          => $request->nama_aset,
            'created_by'         => auth()->id(),
            'updated_by'         => auth()->id(),
        ]);

        return back()->with('success', "Aset {$request->nama_aset} berhasil ditambahkan.");
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'opd_id'             => ['required', 'uuid', 'exists:opds,id'],
            'sub_klasifikasi_id' => ['required', 'uuid', 'exists:sub_klasifikasis,id'],
            'kode_aset'          => ['required', 'string', 'max:30', 'unique:assets,kode_aset,' . $asset->id],
            'nama_aset'          => ['required', 'string', 'max:200'],
        ]);

        $asset->update([
            'opd_id'             => $request->opd_id,
            'sub_klasifikasi_id' => $request->sub_klasifikasi_id,
            'kode_aset'          => $request->kode_aset,
            'nama_aset'          => $request->nama_aset,
            'updated_by'         => auth()->id(),
        ]);

        return back()->with('success', "Aset {$asset->nama_aset} berhasil diperbarui.");
    }

    public function destroy(Asset $asset)
    {
        if ($asset->instances()->exists()) {
            return back()->with(
                'error',
                "Aset {$asset->nama_aset} tidak dapat dihapus — masih memiliki data instance."
            );
        }

        $asset->delete();
        return back()->with('success', "Aset {$asset->nama_aset} berhasil dihapus.");
    }

    public function restore(string $id)
    {
        $asset = Asset::onlyTrashed()->findOrFail($id);
        $asset->restore();
        return back()->with('success', "Aset {$asset->nama_aset} berhasil dipulihkan.");
    }
}
