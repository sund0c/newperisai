<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Opd;
use App\Models\TahunAktif;
use App\Models\KlasifikasiAset;
use App\Models\SubKlasifikasiAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

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
                'subKlasifikasi.klasifikasi',
                fn($q) => $q->where('klasifikasiaset', $klasifikasi)
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
        $opds         = Opd::orderBy('namaopd')->get();
        $klasifikasis = KlasifikasiAset::orderBy('klasifikasiaset')->get();

        $subKlasifikasis = SubKlasifikasiAset::with('klasifikasi')
            ->orderBy('klasifikasi_aset_id')
            ->orderBy('subklasifikasiaset')
            ->get()
            ->groupBy(fn($sub) => $sub->klasifikasi->klasifikasiaset ?? 'Lainnya');

        $totalAset = Asset::where('tahunaktif_id', $tahunContext?->id)->count();

        return view('admin.assets.index', compact(
            'assets',
            'opds',
            'subKlasifikasis',
            'klasifikasis',
            'sortBy',
            'direction',
            'totalAset',
        ));
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

    public function exportPdf(Request $request)
    {
        // ── 1. Validasi input ────────────────────────────────
        $request->validate([
            'tahun'          => ['required', 'exists:tahunaktifs,id'],
            'opd_id'         => ['nullable', 'exists:opds,id'],
            'klasifikasi_id' => ['nullable', 'exists:klasifikasi_asets,id'],
            'status'         => ['nullable', 'in:aktif,hapus'],
        ]);

        // ── 2. Query data ────────────────────────────────────
        $query = Asset::with(['subKlasifikasi.klasifikasi', 'opd']);

        // Filter: status (aktif = tidak trashed, hapus = hanya trashed)
        $status = $request->input('status', '');
        if ($status === 'hapus') {
            $query->onlyTrashed();
        } elseif ($status === 'aktif') {
            // default — withoutTrashed sudah default di Eloquent
        }
        // jika kosong (semua): tampilkan aktif + hapus
        // Uncomment baris berikut jika ingin "semua" benar-benar semua:
        // elseif ($status === '') { $query->withTrashed(); }

        // Filter: tahun
        $query->where('tahunaktif_id', $request->input('tahun'));

        // Filter: OPD
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->input('opd_id'));
        }

        // Filter: Klasifikasi — lewat relasi subKlasifikasi
        if ($request->filled('klasifikasi_id')) {
            $query->whereHas('subKlasifikasi', function ($q) use ($request) {
                $q->where('klasifikasi_aset_id', $request->input('klasifikasi_id'));
            });
        }

        $assets = $query->orderBy('kode_aset')->get();

        // ── 3. Siapkan data untuk Python script ─────────────
        $tahun      = \App\Models\TahunAktif::findOrFail($request->input('tahun'));
        $opd        = $request->filled('opd_id')
            ? \App\Models\Opd::find($request->input('opd_id'))
            : null;
        $klasifikasi = $request->filled('klasifikasi_id')
            ? \App\Models\KlasifikasiAset::find($request->input('klasifikasi_id'))
            : null;

        // Serialisasi ke array sederhana untuk dikirim ke Python
        $rows = $assets->map(function ($a, $idx) {
            return [
                'no'             => $idx + 1,
                'kode_aset'      => $a->kode_aset ?? '-',
                'nama_aset'      => $a->nama_aset ?? '-',
                'keterangan'     => $a->keterangan ?? '',
                'klasifikasi'    => $a->subKlasifikasi->klasifikasi->klasifikasiaset ?? '-',
                'sub_klasifikasi' => $a->subKlasifikasi->subklasifikasiaset ?? '-',
                'opd'            => $a->opd->namaopd ?? '-',
                'status'         => is_null($a->deleted_at) ? 'Aktif' : 'Dihapus',
            ];
        })->values()->toArray();

        $meta = [
            'tahun'        => $tahun->tahun,
            'opd'          => $opd?->namaopd ?? 'Semua OPD',
            'pemilik_aset' => $opd?->namaopd ?? 'PEMERINTAH PROVINSI BALI',  // ← baru
            'klasifikasi'  => $klasifikasi?->klasifikasiaset ?? 'Semua Klasifikasi',
            'status_label' => match ($status) {
                'aktif'  => 'Aktif',
                'hapus'  => 'Dihapus',
                default  => 'Semua',
            },
            'generated_at' => now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm'),
            'total'        => count($rows),
        ];

        // ── 4. Jalankan Python script untuk generate PDF ─────
        $payload  = json_encode(['meta' => $meta, 'rows' => $rows]);
        $script   = base_path('scripts/generate_asset_pdf.py');
        $tmpFile  = sys_get_temp_dir() . '/perisai_asset_' . Str::random(8) . '.pdf';

        // $process = \Symfony\Component\Process\Process::fromShellCommandline(
        //     "python3 {$script} '{$tmpFile}'",
        //     null,
        //     null,
        //     $payload,   // stdin
        //     60          // timeout 60 detik
        // );
        // $process->run();
        $process = new \Symfony\Component\Process\Process([
            '/opt/homebrew/bin/python3',   // ← ganti dengan output `which python3`
            $script,
            $tmpFile,
        ]);
        $process->setInput($payload);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful() || !file_exists($tmpFile)) {
            \Log::error('PDF generation failed', [
                'stderr' => $process->getErrorOutput(),
                'stdout' => $process->getOutput(),
            ]);
            abort(500, 'Gagal generate PDF. Periksa log server.');
        }

        $filename = 'PERISAI_Aset_' . $tahun->tahun . '_' . now()->format('Ymd_His') . '.pdf';

        return response()->file($tmpFile, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ])->deleteFileAfterSend(true);
    }
}
