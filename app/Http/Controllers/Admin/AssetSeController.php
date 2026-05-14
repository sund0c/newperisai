<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetSePenilaian;
use App\Models\Opd;
use App\Models\SeVersion;
use App\Models\TahunAktif;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetSeController extends Controller
{
    const KLASIFIKASI_SE = 'Perangkat Lunak';

    // ── Index ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $user      = Auth::user();
        $isAdmin   = $user->hasRole(['Super Admin', 'admin']);
        $seVersion = SeVersion::where('is_active', true)->first();

        $query = Asset::with(['opd', 'subKlasifikasi.klasifikasi', 'sePenilaian'])
            ->where('tahunaktif_id', $tahunContext->id)
            ->whereHas('subKlasifikasi.klasifikasi', function ($q) {
                $q->where('klasifikasiaset', self::KLASIFIKASI_SE);
            });

        if (! $isAdmin) {
            $query->where('opd_id', $user->opd_id);
        }

        if ($isAdmin && $request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        if ($request->filled('kategori_se')) {
            if ($request->kategori_se === 'unassessed') {
                $query->whereDoesntHave('sePenilaian');
            } else {
                $query->whereHas('sePenilaian', function ($q) use ($request) {
                    $q->where('kategori_se', $request->kategori_se);
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_aset', 'like', "%{$search}%")
                    ->orWhere('kode_aset', 'like', "%{$search}%");
            });
        }

        $sortBy  = $request->get('sort', 'kode_aset');
        $sortDir = in_array($request->get('direction'), ['asc', 'desc'])
            ? $request->get('direction') : 'asc';

        if (! in_array($sortBy, ['kode_aset', 'nama_aset'])) {
            $sortBy = 'kode_aset';
        }

        $query->orderBy($sortBy, $sortDir);

        $assets = $query->paginate(20)->withQueryString();

        // Stats — hanya aset Perangkat Lunak
        $statsQuery = Asset::where('tahunaktif_id', $tahunContext->id)
            ->whereHas('subKlasifikasi.klasifikasi', function ($q) {
                $q->where('klasifikasiaset', self::KLASIFIKASI_SE);
            });
        if (! $isAdmin) {
            $statsQuery->where('opd_id', $user->opd_id);
        }
        $totalAset = $statsQuery->count();

        $seStats = DB::table('asset_se_penilaians')
            ->join('assets', 'asset_se_penilaians.asset_id', '=', 'assets.id')
            ->join('sub_klasifikasi_asets', 'assets.sub_klasifikasi_id', '=', 'sub_klasifikasi_asets.id')
            ->join('klasifikasi_asets', 'sub_klasifikasi_asets.klasifikasi_aset_id', '=', 'klasifikasi_asets.id')
            ->where('assets.tahunaktif_id', $tahunContext->id)
            ->where('klasifikasi_asets.klasifikasiaset', self::KLASIFIKASI_SE)
            ->when(! $isAdmin, function ($q) use ($user) {
                $q->where('assets.opd_id', $user->opd_id);
            })
            ->whereNull('asset_se_penilaians.deleted_at')
            ->selectRaw('kategori_se, count(*) as total')
            ->groupBy('kategori_se')
            ->pluck('total', 'kategori_se');

        $stats = [
            'total'     => $totalAset,
            'strategis' => $seStats->get('STRATEGIS', 0),
            'tinggi'    => $seStats->get('TINGGI', 0),
            'rendah'    => $seStats->get('RENDAH', 0),
            'belum'     => $totalAset - $seStats->sum(),
        ];

        $opds = $isAdmin ? Opd::orderBy('namaopd')->get() : collect();

        return view('admin.asset-se.index', compact(
            'assets',
            'stats',
            'opds',
            'seVersion',
            'tahunContext',
            'isAdmin'
        ));
    }

    // ── Update individual (AJAX PUT) ─────────────────────────────

    public function update(Request $request, Asset $asset): JsonResponse
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        if ($asset->tahunaktif_id !== $tahunContext->id) {
            return response()->json([
                'message' => 'Tidak dapat mengubah data pada tahun yang tidak aktif.',
            ], 403);
        }

        $user = Auth::user();
        if (! $user->hasRole(['Super Admin', 'admin']) && $asset->opd_id !== $user->opd_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $seVersion    = SeVersion::where('is_active', true)->firstOrFail();
        $indikatorIds = $seVersion->indikators()->orderBy('urutan')->pluck('id')->toArray();

        $rules = [];
        foreach ($indikatorIds as $id) {
            $rules[$id] = 'required|in:a,b,c';
        }
        $validated = $request->validate($rules, [
            'required' => 'Semua indikator wajib dijawab.',
            'in'       => 'Jawaban harus a, b, atau c.',
        ]);

        $jawabans = [];
        foreach ($indikatorIds as $id) {
            $jawabans[$id] = $validated[$id];
        }

        $total    = AssetSePenilaian::hitungTotal($jawabans);
        $kategori = AssetSePenilaian::tentukanKategori($total);

        DB::transaction(function () use ($asset, $seVersion, $tahunContext, $jawabans, $total, $kategori, $user) {
            AssetSePenilaian::updateOrCreate(
                ['asset_id' => $asset->id, 'tahunaktif_id' => $tahunContext->id],
                [
                    'se_version_id' => $seVersion->id,
                    'jawabans'      => $jawabans,
                    'total_nilai'   => $total,
                    'kategori_se'   => $kategori,
                    'dinilai_oleh'  => $user->id,
                    'dinilai_pada'  => now(),
                ]
            );
        });

        return response()->json([
            'message'  => 'Penilaian SE berhasil disimpan.',
            'kategori' => $kategori,
            'total'    => $total,
        ]);
    }

    // ── Bulk update (POST form) ───────────────────────────────────

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $seVersion    = SeVersion::where('is_active', true)->firstOrFail();
        $indikatorIds = $seVersion->indikators()->orderBy('urutan')->pluck('id')->toArray();

        $rules = [
            'asset_ids'   => ['required', 'array', 'min:1'],
            'asset_ids.*' => ['required', 'exists:assets,id'],
        ];
        foreach ($indikatorIds as $id) {
            $rules[$id] = 'required|in:a,b,c';
        }

        $validated = $request->validate($rules);

        $jawabans = [];
        foreach ($indikatorIds as $id) {
            $jawabans[$id] = $validated[$id];
        }

        $total    = AssetSePenilaian::hitungTotal($jawabans);
        $kategori = AssetSePenilaian::tentukanKategori($total);

        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        abort_if(! $tahunContext, 403, 'Tahun aktif tidak ditemukan.');

        $updated = 0;

        DB::transaction(function () use ($validated, $seVersion, $tahunContext, $jawabans, $total, $kategori, &$updated) {
            foreach ($validated['asset_ids'] as $assetId) {
                $asset = Asset::find($assetId);

                if (! $asset || (string) $asset->tahunaktif_id !== (string) $tahunContext->id) {
                    continue;
                }

                AssetSePenilaian::updateOrCreate(
                    ['asset_id' => $assetId, 'tahunaktif_id' => $tahunContext->id],
                    [
                        'se_version_id' => $seVersion->id,
                        'jawabans'      => $jawabans,
                        'total_nilai'   => $total,
                        'kategori_se'   => $kategori,
                        'dinilai_oleh'  => Auth::id(),
                        'dinilai_pada'  => now(),
                    ]
                );

                $updated++;
            }
        });

        return redirect()
            ->route('admin.asset-se.index', $request->only(['search', 'opd_id', 'kategori_se', 'sort', 'direction']))
            ->with('success', "Berhasil menyimpan penilaian SE (<strong>{$kategori}</strong>) untuk <strong>{$updated}</strong> aset.");
    }

    // ── Export PDF via Python/ReportLab ──────────────────────────

    public function exportPdf(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $user      = Auth::user();
        $isAdmin   = $user->hasRole(['Super Admin', 'admin']);
        $seVersion = SeVersion::where('is_active', true)->first();

        $query = Asset::with(['sePenilaian', 'opd', 'subKlasifikasi.klasifikasi'])
            ->where('tahunaktif_id', $tahunContext->id)
            ->whereHas('subKlasifikasi.klasifikasi', function ($q) {
                $q->where('klasifikasiaset', self::KLASIFIKASI_SE);
            });

        if (! $isAdmin) {
            $query->where('opd_id', $user->opd_id);
        }

        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        if ($request->filled('kategori_se')) {
            $query->whereHas('sePenilaian', function ($q) use ($request) {
                $q->where('kategori_se', $request->kategori_se);
            });
        }

        $assets = $query->orderBy('kode_aset')->get();

        $filterKategoriLabel = 'Semua';
        if ($request->get('kategori_se') === 'STRATEGIS') $filterKategoriLabel = 'STRATEGIS';
        if ($request->get('kategori_se') === 'TINGGI')    $filterKategoriLabel = 'TINGGI';
        if ($request->get('kategori_se') === 'RENDAH')    $filterKategoriLabel = 'RENDAH';

        $meta = [
            'tahun'        => $tahunContext->tahun,
            'pemilik_aset' => 'PEMERINTAH PROVINSI BALI',
            'opd'          => $request->filled('opd_id')
                ? (Opd::find($request->opd_id)->namaopd ?? 'Semua OPD')
                : 'Semua OPD',
            'kategori_se'  => $filterKategoriLabel,
            'versi_se'     => $seVersion ? $seVersion->kode : '-',
            'generated_at' => now()->locale('id')->translatedFormat('l, d F Y H:i') . ' WITA',
            'total'        => $assets->count(),
            'strategis'    => $assets->filter(function ($a) {
                return optional($a->sePenilaian)->kategori_se === 'STRATEGIS';
            })->count(),
            'tinggi'       => $assets->filter(function ($a) {
                return optional($a->sePenilaian)->kategori_se === 'TINGGI';
            })->count(),
            'rendah'       => $assets->filter(function ($a) {
                return optional($a->sePenilaian)->kategori_se === 'RENDAH';
            })->count(),
            'belum'        => $assets->filter(function ($a) {
                return ! $a->sePenilaian;
            })->count(),
        ];

        $rows = $assets->values()->map(function ($asset, $idx) {
            $se = $asset->sePenilaian;
            return [
                'no'              => $idx + 1,
                'kode_aset'       => $asset->kode_aset ?? '-',
                'nama_aset'       => $asset->nama_aset ?? '-',
                'keterangan'      => $asset->keterangan ?? '',
                'sub_klasifikasi' => optional($asset->subKlasifikasi)->subklasifikasiaset ?? '-',
                'klasifikasi'     => optional(optional($asset->subKlasifikasi)->klasifikasi)->klasifikasiaset ?? '-',
                'opd'             => optional($asset->opd)->namaopd ?? '-',
                'total_nilai'     => $se ? $se->total_nilai : null,
                'kategori_se'     => $se ? $se->kategori_se : null,
            ];
        })->toArray();

        $payload = json_encode(['meta' => $meta, 'rows' => $rows], JSON_UNESCAPED_UNICODE);

        $script = base_path('scripts/generate_se_pdf.py');
        $tmpPdf = sys_get_temp_dir() . '/perisai_se_' . \Illuminate\Support\Str::random(8) . '.pdf';

        $process = new \Symfony\Component\Process\Process(
            ['python3', $script, $tmpPdf],
            null,
            null,
            $payload,
            60
        );
        $process->run();

        if (! $process->isSuccessful() || ! file_exists($tmpPdf)) {
            \Log::error('SE PDF generation failed', [
                'stderr' => $process->getErrorOutput(),
                'stdout' => $process->getOutput(),
            ]);
            abort(500, 'Gagal generate PDF SE. Periksa log server.');
        }

        $filename = 'PERISAI_SE_' . $tahunContext->tahun . '_' . now()->format('Ymd_His') . '.pdf';

        return response()->file($tmpPdf, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }
}
