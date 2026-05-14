<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeVersion;
use App\Models\SeIndikator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SeVersionController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────
    public function index()
    {
        $versions = SeVersion::withCount(['indikators'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $nextKode   = SeVersion::generateKode();
        $totalAktif = SeVersion::where('is_active', true)->count();

        return view('admin.master-se.index', compact('versions', 'nextKode', 'totalAktif'));
    }

    // ─── Store versi baru (dari modal, tanpa indikator dulu) ──────
    public function storeVersion(Request $request)
    {
        $validated = $request->validate([
            'kode'      => 'required|string|max:20|unique:se_versions,kode',
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        SeVersion::create([
            'kode'       => $validated['kode'],
            'nama'       => $validated['nama'],
            'deskripsi'  => $validated['deskripsi'] ?? null,
            'is_active'  => false,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.master-se.index')
            ->with('success', "Versi {$validated['kode']} berhasil dibuat. Silakan lengkapi 10 indikator melalui halaman Detail.");
    }

    // ─── Show / Detail ────────────────────────────────────────────
    public function show(SeVersion $seVersion)
    {
        $seVersion->load('indikators', 'createdBy', 'activatedBy');
        return view('admin.master-se.show', compact('seVersion'));
    }

    // ─── Edit indikator ───────────────────────────────────────────
    public function edit(SeVersion $seVersion)
    {
        if ($seVersion->is_active) {
            return back()->with('error', 'Versi yang sedang aktif tidak dapat diedit.');
        }

        $seVersion->load('indikators');
        return view('admin.master-se.edit', compact('seVersion'));
    }

    // ─── Update indikator ─────────────────────────────────────────
    public function update(Request $request, SeVersion $seVersion)
    {
        if ($seVersion->is_active) {
            return back()->with('error', 'Versi tidak dapat diubah.');
        }

        $validated = $request->validate([
            'nama'        => 'required|string|max:100',
            'deskripsi'   => 'nullable|string|max:500',
            'indikators'  => 'required|array|size:10',
            'indikators.*.pertanyaan' => 'required|string|max:1000',
            'indikators.*.keterangan' => 'nullable|string|max:500',
            'indikators.*.pilihan_1'  => 'required|string|max:255',
            'indikators.*.nilai_1'    => 'required|integer|min:1|max:10',
            'indikators.*.pilihan_2'  => 'required|string|max:255',
            'indikators.*.nilai_2'    => 'required|integer|min:1|max:10',
            'indikators.*.pilihan_3'  => 'required|string|max:255',
            'indikators.*.nilai_3'    => 'required|integer|min:1|max:10',
        ]);

        DB::transaction(function () use ($seVersion, $validated) {
            $seVersion->update([
                'nama'      => $validated['nama'],
                'deskripsi' => $validated['deskripsi'] ?? null,
            ]);

            $seVersion->indikators()->delete();

            foreach ($validated['indikators'] as $urutan => $data) {
                SeIndikator::create([
                    'se_version_id' => $seVersion->id,
                    'urutan'        => $urutan + 1,
                    'pertanyaan'    => $data['pertanyaan'],
                    'keterangan'    => $data['keterangan'] ?? null,
                    'pilihan_1'     => $data['pilihan_1'],
                    'nilai_1'       => $data['nilai_1'],
                    'pilihan_2'     => $data['pilihan_2'],
                    'nilai_2'       => $data['nilai_2'],
                    'pilihan_3'     => $data['pilihan_3'],
                    'nilai_3'       => $data['nilai_3'],
                ]);
            }
        });

        return redirect()->route('admin.master-se.show', $seVersion)
            ->with('success', 'Indikator berhasil disimpan.');
    }

    // ─── Aktifkan ─────────────────────────────────────────────────
    public function activate(SeVersion $seVersion)
    {
        if ($seVersion->is_active) {
            return back()->with('info', 'Versi ini sudah aktif.');
        }
        DB::transaction(function () use ($seVersion) {
            SeVersion::where('is_active', true)->update(['is_active' => false]);
            $seVersion->update([
                'is_active'    => true,
                'activated_by' => Auth::id(),
                'activated_at' => now(),
            ]);
        });

        return back()->with('success', "Versi {$seVersion->kode} ({$seVersion->nama}) berhasil diaktifkan.");
    }

    // ─── Nonaktifkan ──────────────────────────────────────────────
    public function deactivate(SeVersion $seVersion)
    {
        if (!$seVersion->is_active) {
            return back()->with('info', 'Versi ini sudah tidak aktif.');
        }

        // BUSINESS RULE: harus selalu ada 1 versi aktif
        $totalVersi = SeVersion::count();
        if ($totalVersi === 1) {
            return back()->with('error', 'Tidak dapat menonaktifkan — harus ada minimal satu versi aktif.');
        }

        // Cek apakah ada versi lain yang bisa dijadikan aktif
        $adaVersiLain = SeVersion::where('id', '!=', $seVersion->id)->exists();
        if (!$adaVersiLain) {
            return back()->with('error', 'Tidak dapat menonaktifkan — harus ada minimal satu versi aktif.');
        }

        $seVersion->update(['is_active' => false]);

        return back()->with('success', "Versi {$seVersion->kode} berhasil dinonaktifkan. Silakan aktifkan versi lain.");
    }

    // ─── Hapus ────────────────────────────────────────────────────
    public function destroy(SeVersion $seVersion)
    {
        if ($seVersion->is_active) {
            return back()->with('error', 'Versi yang sedang aktif tidak dapat dihapus. Aktifkan versi lain terlebih dahulu.');
        }
        // Cek apakah setelah dihapus masih ada versi aktif
        $adaAktifLain = SeVersion::where('id', '!=', $seVersion->id)->where('is_active', true)->exists();
        if (!$adaAktifLain) {
            return back()->with('error', 'Tidak dapat menghapus — harus ada minimal satu versi aktif. Aktifkan versi lain terlebih dahulu.');
        }

        $seVersion->delete();

        return redirect()->route('admin.master-se.index')
            ->with('success', "Versi {$seVersion->kode} berhasil dihapus.");
    }
}
