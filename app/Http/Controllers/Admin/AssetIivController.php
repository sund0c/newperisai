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
use Illuminate\Http\RedirectResponse;

class AssetIivController extends Controller
{
    // ── Index ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $user    = Auth::user();
        $isAdmin = $user->hasRole(['Super Admin', 'admin']);

        $query = Asset::with(['iiv', 'opd', 'subKlasifikasi.klasifikasi'])
            ->where('tahunaktif_id', $tahunContext->id);

        if (! $isAdmin) {
            $query->where('opd_id', $user->opd_id);
        }

        if ($isAdmin && $request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }

        if ($request->filled('nilai_iiv')) {
            if ($request->nilai_iiv === 'unassessed') {
                $query->whereDoesntHave('iiv');
            } else {
                $query->whereHas('iiv', function ($q) use ($request) {
                    $q->where('nilai_iiv', $request->nilai_iiv);
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

        if (! in_array($sortBy, ['kode_aset', 'nama_aset', 'nilai_iiv'])) {
            $sortBy = 'kode_aset';
        }

        if ($sortBy === 'nilai_iiv') {
            $query->leftJoin('asset_iivs', 'assets.id', '=', 'asset_iivs.asset_id')
                ->orderBy('asset_iivs.nilai_iiv', $sortDir)
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
            'total'       => $totalAset,
            'vital'       => $iivStats->get(AssetIiv::VITAL, 0),
            'tidak_vital' => $iivStats->get(AssetIiv::TIDAK_VITAL, 0),
            'belum'       => $totalAset - $iivStats->sum(),
        ];

        $opds    = $isAdmin ? Opd::orderBy('namaopd')->get() : collect();
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

    // ── Update ───────────────────────────────────────────────────

    public function update(Request $request, Asset $asset)
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
            'message'         => 'Penilaian IIV berhasil disimpan.',
            'nilai_iiv'       => $iiv->nilai_iiv,
            'nilai_iiv_label' => $labelMap[$iiv->nilai_iiv],
        ]);
    }

    // ── Export PDF via Python/ReportLab ──────────────────────────

    public function exportPdf(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $user    = Auth::user();
        $isAdmin = $user->hasRole(['Super Admin', 'admin']);

        // Relasi sama dengan index()
        $query = Asset::with(['iiv', 'opd', 'subKlasifikasi.klasifikasi'])
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

        $assets = $query->orderBy('kode_aset')->get();

        $filterIivLabel = match ($request->get('nilai_iiv')) {
            '2'     => 'VITAL',
            '1'     => 'TIDAK VITAL',
            default => 'Semua',
        };

        $meta = [
            'tahun'        => $tahunContext->tahun,
            'pemilik_aset' => 'PEMERINTAH PROVINSI BALI',
            'opd'          => $request->filled('opd_id')
                ? (Opd::find($request->opd_id)?->namaopd ?? 'Semua OPD')
                : 'Semua OPD',
            'nilai_iiv'    => $filterIivLabel,
            'generated_at' => now()->locale('id')->translatedFormat('l, d F Y H:i') . ' WITA',
            'total'        => $assets->count(),
            'vital'        => $assets->filter(fn($a) => $a->iiv?->nilai_iiv === AssetIiv::VITAL)->count(),
            'tidak_vital'  => $assets->filter(fn($a) => $a->iiv?->nilai_iiv === AssetIiv::TIDAK_VITAL)->count(),
            'belum'        => $assets->filter(fn($a) => ! $a->iiv)->count(),
        ];

        // Kirim nilai dimensi sebagai INTEGER agar Python bisa proses DIM_SHORT
        $rows = $assets->values()->map(function ($asset, $idx) {
            $iiv = $asset->iiv;
            return [
                'no'                    => $idx + 1,
                'kode_aset'             => $asset->kode_aset ?? '-',
                'nama_aset'             => $asset->nama_aset ?? '-',
                'keterangan'            => $asset->keterangan ?? '',
                'sub_klasifikasi'       => optional($asset->subKlasifikasi)->subklasifikasiaset ?? '-',
                'klasifikasi'           => optional($asset->subKlasifikasi?->klasifikasi)->klasifikasiaset ?? '-',
                'opd'                   => optional($asset->opd)->namaopd ?? '-',
                'dampak_operasional'    => $iiv?->dampak_operasional,
                'dampak_data_informasi' => $iiv?->dampak_data_informasi,
                'dampak_finansial'      => $iiv?->dampak_finansial,
                'dampak_umum'           => $iiv?->dampak_umum,
                'dampak_ketergantungan' => $iiv?->dampak_ketergantungan,
                'nilai_iiv'             => $iiv?->nilai_iiv,
            ];
        })->toArray();

        $payload = json_encode(['meta' => $meta, 'rows' => $rows], JSON_UNESCAPED_UNICODE);

        $script = base_path('scripts/generate_iiv_pdf.py');
        $tmpPdf = sys_get_temp_dir() . '/perisai_iiv_' . \Illuminate\Support\Str::random(8) . '.pdf';

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

        return response()->file($tmpPdf, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_ids'               => ['required', 'array', 'min:1'],
            'asset_ids.*'             => ['required', 'exists:assets,id'],
            'dampak_operasional'      => ['required', 'in:1,2,3'],
            'dampak_data_informasi'   => ['required', 'in:1,2,3'],
            'dampak_finansial'        => ['required', 'in:1,2,3'],
            'dampak_umum'             => ['required', 'in:1,2,3'],
            'dampak_ketergantungan'   => ['required', 'in:1,2,3'],
        ]);

        $dims = [
            'dampak_operasional'    => (int) $validated['dampak_operasional'],
            'dampak_data_informasi' => (int) $validated['dampak_data_informasi'],
            'dampak_finansial'      => (int) $validated['dampak_finansial'],
            'dampak_umum'           => (int) $validated['dampak_umum'],
            'dampak_ketergantungan' => (int) $validated['dampak_ketergantungan'],
        ];

        // nilai_iiv = nilai tertinggi dari 5 dimensi (logika sama dengan update individual)
        $nilaiIIV = max(array_values($dims));

        $tahunContext  = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();
        $activeTahunId = $tahunContext?->id;

        abort_if(!$activeTahunId, 403, 'Tahun aktif tidak ditemukan.');

        $updated = 0;

        DB::transaction(function () use ($validated, $dims, $nilaiIIV, $activeTahunId, &$updated) {
            foreach ($validated['asset_ids'] as $assetId) {
                $asset = Asset::find($assetId);

                if (!$asset || (string) $asset->tahunaktif_id !== (string) $activeTahunId) {
                    continue;
                }

                // Sesuaikan nama model dengan yang ada di project (AssetIIV / AssetVital / dll)
                AssetIIV::updateOrCreate(
                    ['asset_id' => $assetId],
                    array_merge($dims, [
                        'nilai_iiv'   => $nilaiIIV,
                        'assessed_by' => Auth::id(),
                    ])
                );

                $updated++;
            }
        });

        $label = $nilaiIIV >= 2 ? 'VITAL' : 'TIDAK VITAL';

        return redirect()
            ->route('admin.asset-iiv.index', request()->only(['search', 'opd_id', 'nilai_iiv', 'sort', 'direction']))
            ->with('success', "Berhasil menyimpan penilaian IIV (<strong>{$label}</strong>) untuk <strong>{$updated}</strong> aset.");
    }
}
