<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Opd;
use App\Models\TahunAktif;
use App\Models\SubKlasifikasiAset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();

        $query = Asset::with(['opd', 'subKlasifikasi.klasifikasi'])
            ->withTrashed()
            ->when($tahunContext, fn($q) => $q->where('TahunAktif_id', $tahunContext->id));

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
                fn($q) => $q->where('klasifikasi', $klasifikasi)
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
        $subKlasifikasis = SubKlasifikasiAset::with('klasifikasi')
            ->orderBy('klasifikasi_aset_id')
            ->orderBy('subklasifikasiaset')
            ->get()
            ->groupBy(fn($sub) => $sub->klasifikasi->klasifikasiaset ?? 'Lainnya');

        return view('admin.assets.index', compact(
            'assets',
            'opds',
            'subKlasifikasis',
            'sortBy',
            'direction',
        ));
        // tahunContext sudah di-share via middleware, tidak perlu compact
    }

    public function store(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();

        if (! $tahunContext) {
            return back()->with('error', 'Tidak ada tahun aktif. Hubungi administrator.');
        }

        $tahunAktif = TahunAktif::getActive();
        if ($tahunContext->id !== $tahunAktif?->id) {
            return back()->with('error', 'Tambah aset hanya diizinkan pada tahun aktif.');
        }

        $request->validate([
            'opd_id'             => ['required', 'exists:opds,id'],
            'sub_klasifikasi_id' => ['required', 'uuid', 'exists:sub_klasifikasi_asets,id'],
            'nama_aset'          => ['required', 'string', 'max:200'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $asset = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $tahunContext) {

            // Lock — cegah race condition
            $sub      = SubKlasifikasiAset::with('klasifikasi')
                ->lockForUpdate()
                ->findOrFail($request->sub_klasifikasi_id);
            $kodeklas = $sub->klasifikasi->kodeklas;

            // Cari nomor terakhir dalam tahun + prefix yang sama
            $last = Asset::where('tahunaktif_id', $tahunContext->id)
                ->where('kode_aset', 'like', "{$kodeklas}-%")
                ->orderBy('kode_aset', 'desc')
                ->lockForUpdate()
                ->value('kode_aset');

            $nextNumber = 1;
            if ($last) {
                $parts = explode('-', $last);
                $nextNumber = (int) end($parts) + 1;
            }

            $kodeAset = $kodeklas . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            return Asset::create([
                'tahunaktif_id'      => $tahunContext->id,
                'opd_id'             => $request->opd_id,
                'sub_klasifikasi_id' => $request->sub_klasifikasi_id,
                'kode_aset'          => $kodeAset,
                'nama_aset'          => $request->nama_aset,
                'keterangan'          => $request->keterangan,
                'created_by'         => auth()->id(),
                'updated_by'         => auth()->id(),
            ]);
        });

        return back()->with('success', "Aset {$asset->nama_aset} berhasil ditambahkan dengan kode {$asset->kode_aset}.");
    }

    public function generateKode(Request $request)
    {
        $request->validate([
            'sub_klasifikasi_id' => ['required', 'uuid', 'exists:sub_klasifikasi_asets,id'],
        ]);

        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();

        // Ambil kodeklas dari relasi
        $sub      = SubKlasifikasiAset::with('klasifikasi')->findOrFail($request->sub_klasifikasi_id);
        $kodeklas = $sub->klasifikasi->kodeklas; // misal: "PL", "SP", "DI"

        // Cari nomor terakhir di tahun aktif dengan prefix yang sama
        $last = Asset::where('tahunaktif_id', $tahunContext?->id)
            ->where('kode_aset', 'like', "{$kodeklas}-%")
            ->orderBy('kode_aset', 'desc')
            ->value('kode_aset');

        // Parse nomor terakhir
        $nextNumber = 1;
        if ($last) {
            $parts = explode('-', $last);
            $nextNumber = (int) end($parts) + 1;
        }

        $kode = $kodeklas . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return response()->json(['kode' => $kode]);
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'opd_id'             => ['required', 'exists:opds,id'],
            'sub_klasifikasi_id' => ['required', 'uuid', 'exists:sub_klasifikasi_asets,id'],
            'nama_aset'          => ['required', 'string', 'max:200'],
        ]);

        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $asset, $tahunContext) {

            $subBaru = SubKlasifikasiAset::with('klasifikasi')
                ->lockForUpdate()
                ->findOrFail($request->sub_klasifikasi_id);

            $subLama = $asset->subKlasifikasi->klasifikasi->kodeklas ?? null;
            $subBaruKode = $subBaru->klasifikasi->kodeklas;

            // Generate kode baru hanya jika sub klasifikasi berubah
            if ($subLama !== $subBaruKode) {
                $last = Asset::where('tahunaktif_id', $tahunContext->id)
                    ->where('kode_aset', 'like', "{$subBaruKode}-%")
                    ->where('id', '!=', $asset->id) // exclude diri sendiri
                    ->orderBy('kode_aset', 'desc')
                    ->lockForUpdate()
                    ->value('kode_aset');

                $nextNumber = 1;
                if ($last) {
                    $parts = explode('-', $last);
                    $nextNumber = (int) end($parts) + 1;
                }

                $kodeAset = $subBaruKode . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            } else {
                $kodeAset = $asset->kode_aset; // tidak berubah
            }

            $asset->update([
                'opd_id'             => $request->opd_id,
                'sub_klasifikasi_id' => $request->sub_klasifikasi_id,
                'kode_aset'          => $kodeAset,
                'nama_aset'          => $request->nama_aset,
                'keterangan'          => $request->keterangan,
                'updated_by'         => auth()->id(),
            ]);
        });

        return back()->with('success', "Aset {$asset->fresh()->kode_aset} - {$asset->fresh()->nama_aset} berhasil diperbarui.");
    }

    public function destroy(Asset $asset)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();

        $tahunAktif = TahunAktif::getActive();
        if (! $tahunContext || $tahunContext->id !== $tahunAktif?->id) {
            return back()->with('error', 'Hapus aset hanya diizinkan pada tahun aktif.');
        }

        $asset->delete();

        return back()->with('success', "Aset {$asset->kode_aset} - {$asset->nama_aset} berhasil diarsipkan.");
    }

    public function restore(string $id)
    {
        $asset = Asset::onlyTrashed()->findOrFail($id);
        $asset->restore();
        return back()->with('success', "Aset {$asset->nama_aset} berhasil dipulihkan.");
    }
}
