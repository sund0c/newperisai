<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetIiv;
use App\Models\Opd;
use App\Models\TahunAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssetIivController extends Controller
{
    // ── Index ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $user    = Auth::user();
        $isAdmin = $user->hasRole(['Super Admin', 'Admin']);

        // Base query: aset sesuai tahun konteks
        $query = Asset::with([
            'iiv',
            'opd',
            'klasifikasiAset',
            'subKlasifikasiAset',
        ])
            ->where('tahunaktif_id', $tahunContext->id);

        // OPD user hanya lihat aset milik OPD-nya
        if (! $isAdmin) {
            $query->where('opd_id', $user->opd_id);
        }

        // Filter: OPD (admin only)
        if ($isAdmin && $request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        // Filter: nilai IIV
        if ($request->filled('nilai_iiv')) {
            $query->whereHas('iiv', function ($q) use ($request) {
                $q->where('nilai_iiv', $request->nilai_iiv);
            });
        }

        // Filter: search nama/kode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_aset', 'like', "%{$search}%")
                    ->orWhere('kode_aset', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy  = $request->get('sort', 'kode_aset');
        $sortDir = $request->get('dir', 'asc');

        $allowedSort = ['kode_aset', 'nama_aset', 'nilai_iiv'];
        if (! in_array($sortBy, $allowedSort)) {
            $sortBy = 'kode_aset';
        }

        if ($sortBy === 'nilai_iiv') {
            $query->leftJoin('asset_iivs', 'assets.id', '=', 'asset_iivs.asset_id')
                ->orderBy('asset_iivs.nilai_iiv', $sortDir)
                ->select('assets.*');
        } elseif ($sortBy === 'opd') {
            $query->leftJoin('opds', 'assets.opd_id', '=', 'opds.id')
                ->orderBy('opds.nama_opd', $sortDir)
                ->select('assets.*');
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $assets = $query->paginate(20)->withQueryString();

        // Stats
        $statsQuery = Asset::where('tahunaktif_id', $tahunContext->id);
        if (! $isAdmin) {
            $statsQuery->where('opd_id', $user->opd_id);
        }

        $totalAset = $statsQuery->count();

        $iivStats = DB::table('asset_iivs')
            ->join('assets', 'asset_iivs.asset_id', '=', 'assets.id')
            ->where('assets.tahunaktif_id', $tahunContext->id)
            ->when(! $isAdmin, fn($q) => $q->where('assets.opd_id', $user->opd_id))
            ->whereNull('asset_iivs.deleted_at')
            ->selectRaw('nilai_iiv, count(*) as total')
            ->groupBy('nilai_iiv')
            ->pluck('total', 'nilai_iiv');

        $stats = [
            'total'     => $totalAset,
            'kritis'    => $iivStats->get(AssetIiv::KRITIS, 0),
            'terbatas'  => $iivStats->get(AssetIiv::TERBATAS, 0),
            'minor'     => $iivStats->get(AssetIiv::MINOR, 0),
            'belum'     => $totalAset - $iivStats->sum(),
        ];

        $opds    = $isAdmin ? Opd::orderBy('nama_opd')->get() : collect();
        $options = AssetIiv::options();

        return view('admin.asset-iiv.index', compact(
            'assets',
            'stats',
            'opds',
            'options',
            'tahunContext',
            'isAdmin'
        ));
    }

    // ── Upsert (store + update digabung) ─────────────────────────

    public function upsert(Request $request, Asset $asset)
    {
        // Pastikan aset sesuai tahun aktif
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        if ($asset->tahunaktif_id !== $tahunContext->id) {
            return response()->json([
                'message' => 'Tidak dapat mengubah data pada tahun yang tidak aktif.',
            ], 403);
        }

        // Otorisasi: OPD user hanya edit aset miliknya
        $user = Auth::user();
        if (! $user->hasRole(['Super Admin', 'Admin']) && $asset->opd_id !== $user->opd_id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'dampak_operasional'    => ['required', Rule::in([1, 2, 3])],
            'dampak_data_informasi' => ['required', Rule::in([1, 2, 3])],
            'dampak_finansial'      => ['required', Rule::in([1, 2, 3])],
            'dampak_umum'           => ['required', Rule::in([1, 2, 3])],
            'dampak_ketergantungan' => ['required', Rule::in([1, 2, 3])],
        ], [
            'required' => 'Semua dimensi wajib diisi.',
            'in'       => 'Nilai harus MINOR, TERBATAS, atau KRITIS.',
        ]);

        $nilaiIiv = AssetIiv::computeNilaiIiv(
            $validated['dampak_operasional'],
            $validated['dampak_data_informasi'],
            $validated['dampak_finansial'],
            $validated['dampak_umum'],
            $validated['dampak_ketergantungan'],
        );

        $iiv = DB::transaction(function () use ($asset, $validated, $nilaiIiv, $user) {
            return AssetIiv::updateOrCreate(
                ['asset_id' => $asset->id],
                array_merge($validated, [
                    'nilai_iiv'   => $nilaiIiv,
                    'assessed_by' => $user->id,
                ])
            );
        });

        $labelMap = AssetIiv::labelMap();

        return response()->json([
            'message'        => 'Penilaian IIV berhasil disimpan.',
            'nilai_iiv'      => $iiv->nilai_iiv,
            'nilai_iiv_label' => $labelMap[$iiv->nilai_iiv],
        ]);
    }

    // ── Export PDF via Python/ReportLab ─────────────────────────

    public function exportPdf(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $user    = Auth::user();
        $isAdmin = $user->hasRole(['Super Admin', 'Admin']);

        $query = Asset::with(['iiv', 'opd', 'klasifikasiAset', 'subKlasifikasiAset'])
            ->where('tahunaktif_id', $tahunContext->id);

        if (! $isAdmin) {
            $query->where('opd_id', $user->opd_id);
        }

        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        if ($request->filled('nilai_iiv')) {
            $query->whereHas('iiv', fn($q) => $q->where('nilai_iiv', $request->nilai_iiv));
        }

        $assets   = $query->orderBy('kode_aset')->get();
        $labelMap = AssetIiv::labelMap();

        // ── Bangun payload JSON untuk Python script ──────────────

        $iivStats = $assets->groupBy(fn($a) => $a->iiv?->nilai_iiv ?? 0);

        $filterIivLabel = match ($request->get('nilai_iiv')) {
            '3'     => 'KRITIS',
            '2'     => 'TERBATAS',
            '1'     => 'MINOR',
            default => 'Semua',
        };

        $meta = [
            'tahun'        => $tahunContext->tahun,
            'opd'          => $request->filled('opd_id')
                ? (Opd::find($request->opd_id)?->nama_opd ?? 'Semua OPD')
                : 'Semua OPD',
            'filter_iiv'   => $filterIivLabel,
            'generated_at' => now()->locale('id')->translatedFormat('l, d F Y H:i') . ' WITA',
            'generated_by' => $user->name,
            'total'        => $assets->count(),
            'kritis'       => $iivStats->get(AssetIiv::KRITIS,   collect())->count(),
            'terbatas'     => $iivStats->get(AssetIiv::TERBATAS, collect())->count(),
            'minor'        => $iivStats->get(AssetIiv::MINOR,    collect())->count(),
            'belum'        => $iivStats->get(0,                   collect())->count(),
        ];

        $rows = $assets->values()->map(function ($asset, $idx) use ($labelMap) {
            $iiv = $asset->iiv;
            $dimLabel = fn($v) => $labelMap[$v] ?? '—';
            return [
                'no'                     => $idx + 1,
                'kode_aset'              => $asset->kode_aset ?? '-',
                'nama_aset'              => $asset->nama_aset ?? '-',
                'sub_klasifikasi'        => optional($asset->subKlasifikasiAset)->nama_sub_klasifikasi ?? '',
                'klasifikasi'            => optional($asset->klasifikasiAset)->nama_klasifikasi ?? '-',
                'opd'                    => optional($asset->opd)->nama_opd ?? '-',
                'dampak_operasional'     => $iiv ? $dimLabel($iiv->dampak_operasional)    : '—',
                'dampak_data_informasi'  => $iiv ? $dimLabel($iiv->dampak_data_informasi) : '—',
                'dampak_finansial'       => $iiv ? $dimLabel($iiv->dampak_finansial)      : '—',
                'dampak_umum'            => $iiv ? $dimLabel($iiv->dampak_umum)           : '—',
                'dampak_ketergantungan'  => $iiv ? $dimLabel($iiv->dampak_ketergantungan) : '—',
                'nilai_iiv'              => $iiv ? ($labelMap[$iiv->nilai_iiv] ?? '—')    : '—',
            ];
        })->toArray();

        $payload = json_encode(['meta' => $meta, 'rows' => $rows], JSON_UNESCAPED_UNICODE);

        // ── Jalankan Python script ────────────────────────────────

        $script  = base_path('scripts/generate_iiv_pdf.py');
        $tmpPdf  = sys_get_temp_dir() . '/perisai_iiv_' . \Illuminate\Support\Str::random(8) . '.pdf';

        $process = new \Symfony\Component\Process\Process(
            ['python3', $script, $tmpPdf],
            null,
            null,
            $payload,
            60
        );
        $process->run();

        if (! $process->isSuccessful() || ! file_exists($tmpPdf)) {
            \Log::error('IIV PDF generation failed', [
                'stderr' => $process->getErrorOutput(),
                'stdout' => $process->getOutput(),
            ]);
            abort(500, 'Gagal generate PDF IIV. Periksa log server.');
        }

        $filename = 'PERISAI_IIV_' . $tahunContext->tahun . '_' . now()->format('Ymd_His') . '.pdf';

        return response()->download($tmpPdf, $filename, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(true);
    }
}
