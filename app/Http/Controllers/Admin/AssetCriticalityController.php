<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCriticality;
use App\Models\Opd;
use App\Models\TahunAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssetCriticalityController extends Controller
{
    // ── Index ─────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();

        $tahunId = $tahunContext?->id;

        // Base query
        $query = Asset::with([
            'opd',
            'subKlasifikasi.klasifikasi',
            'criticality',
        ])
            ->where('TahunAktif_id', $tahunId);

        // Filter: search
        if ($search = $request->search) {
            $query->where(
                fn($q) =>
                $q->where('nama_aset', 'like', "%{$search}%")
                    ->orWhere('kode_aset', 'like', "%{$search}%")
            );
        }

        // Filter: OPD
        if ($opdId = $request->opd_id) {
            $query->where('opd_id', $opdId);
        }

        // Filter: Klasifikasi
        if ($klasifikasi = $request->klasifikasi) {
            $query->whereHas(
                'subKlasifikasi.klasifikasi',
                fn($q) => $q->where('kodeklas', $klasifikasi)
            );
        }

        // Filter: Kritikalitas
        if ($kritikalitas = $request->kritikalitas) {
            $query->whereHas(
                'criticality',
                fn($q) =>
                $q->where('kritikalitas', (int) $kritikalitas)
            );
        }

        // Filter: belum dinilai
        if ($request->status === 'unassessed') {
            $query->whereDoesntHave('criticality');
        }

        // Sort
        $sortBy    = in_array($request->sort, ['kode_aset', 'nama_aset', 'created_at'])
            ? $request->sort : 'kode_aset';
        $direction = $request->direction === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy, $direction);

        $assets = $query->paginate(20)->withQueryString();

        // ── Stats ─────────────────────────────────────────────
        $totalAset = Asset::where('TahunAktif_id', $tahunId)->count();

        $critBase = AssetCriticality::whereHas(
            'asset',
            fn($q) => $q->where('TahunAktif_id', $tahunId)
        );

        $totalTinggi  = (clone $critBase)->where('kritikalitas', 3)->count();
        $totalSedang  = (clone $critBase)->where('kritikalitas', 2)->count();
        $totalRendah  = (clone $critBase)->where('kritikalitas', 1)->count();
        $totalDinilai = $totalTinggi + $totalSedang + $totalRendah;
        $totalBelumNilai = $totalAset - $totalDinilai;

        // ── OPD list ──────────────────────────────────────────
        // Ganti 'nama_opd' jika nama kolom berbeda di tabel opds
        $opds = Opd::orderBy('namaopd')->get();

        $ciaOptions  = AssetCriticality::$CIA_OPTIONS;
        $levelLabels = AssetCriticality::$LEVEL_LABELS;
        $levelColors = AssetCriticality::$LEVEL_COLORS;

        return view('admin.asset-criticality.index', compact(
            'assets',
            'opds',
            'tahunContext',
            'sortBy',
            'direction',
            'totalAset',
            'totalTinggi',
            'totalSedang',
            'totalRendah',
            'totalBelumNilai',
            'ciaOptions',
            'levelLabels',
            'levelColors',
        ));
    }

    // ── Update / Store (upsert) ───────────────────────────────

    public function update(Request $request, string $assetId)
    {
        $asset = Asset::findOrFail($assetId);

        $validated = $request->validate([
            'confidentiality' => ['required', Rule::in([1, 2, 3])],
            'integrity'       => ['required', Rule::in([1, 2, 3])],
            'availability'    => ['required', Rule::in([1, 2, 3])],
        ], [
            'confidentiality.required' => 'Nilai Confidentiality wajib dipilih.',
            'integrity.required'       => 'Nilai Integrity wajib dipilih.',
            'availability.required'    => 'Nilai Availability wajib dipilih.',
        ]);

        $kritikalitas = AssetCriticality::computeKritikalitas(
            (int) $validated['confidentiality'],
            (int) $validated['integrity'],
            (int) $validated['availability'],
        );

        DB::transaction(function () use ($asset, $validated, $kritikalitas) {
            AssetCriticality::updateOrCreate(
                ['asset_id' => $asset->id],
                [
                    'confidentiality' => $validated['confidentiality'],
                    'integrity'       => $validated['integrity'],
                    'availability'    => $validated['availability'],
                    'kritikalitas'    => $kritikalitas,
                    'assessed_by'     => Auth::id(),
                ]
            );
        });

        $levelLabel = AssetCriticality::$LEVEL_LABELS[$kritikalitas];

        return back()->with(
            'success',
            "Kritikalitas aset <strong>{$asset->nama_aset}</strong> berhasil disimpan — Level: <strong>{$levelLabel}</strong>"
        );
    }

    // ── Export PDF ────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();

        $query = Asset::with(['opd', 'subKlasifikasi.klasifikasi', 'criticality'])
            ->where('TahunAktif_id', $tahunContext?->id);

        if ($opdId = $request->opd_id) {
            $query->where('opd_id', $opdId);
        }

        if ($klasifikasi = $request->klasifikasi) {
            $query->whereHas(
                'subKlasifikasi.klasifikasi',
                fn($q) => $q->where('kodeklas', $klasifikasi)
            );
        }

        if ($kritikalitas = $request->kritikalitas) {
            if ($kritikalitas === 'unassessed') {
                $query->whereDoesntHave('criticality');
            } else {
                $query->whereHas(
                    'criticality',
                    fn($q) => $q->where('kritikalitas', (int) $kritikalitas)
                );
            }
        }

        $assets = $query->orderBy('kode_aset')->get();

        $levelLabels = AssetCriticality::$LEVEL_LABELS;
        $opd = $request->opd_id ? Opd::find($request->opd_id) : null;

        $payload = [
            'meta' => [
                'tahun'        => $tahunContext?->tahun ?? '-',
                'pemilik_aset' => $opd?->namaopd ?? 'PEMERINTAH PROVINSI BALI',
                'opd'          => $opd?->namaopd ?? 'Semua OPD',
                'klasifikasi'  => $request->klasifikasi ?? 'Semua',
                'kritikalitas' => $request->kritikalitas
                    ? ($levelLabels[(int)$request->kritikalitas] ?? ucfirst($request->kritikalitas))
                    : 'Semua',
                'total'        => $assets->count(),
                'generated_at' => now()->format('d/m/Y H:i'),
            ],
            'rows' => $assets->map(fn($a, $i) => [
                'no'              => $i + 1,
                'kode_aset'       => $a->kode_aset,
                'nama_aset'       => $a->nama_aset,
                'keterangan'      => $a->keterangan,
                'opd'             => $a->opd?->namaopd ?? '-',
                'klasifikasi'     => $a->subKlasifikasi?->klasifikasi?->klasifikasiaset ?? '-',
                'sub_klasifikasi' => $a->subKlasifikasi?->subklasifikasiaset ?? '-',
                'confidentiality' => $a->criticality?->confidentiality,
                'integrity'       => $a->criticality?->integrity,
                'availability'    => $a->criticality?->availability,
                'kritikalitas'    => $a->criticality?->kritikalitas,
            ])->values()->toArray(),
        ];

        $tmpPdf = tempnam(sys_get_temp_dir(), 'perisai_crit_') . '.pdf';
        $script = base_path('scripts/generate_criticality_pdf.py');

        $process = new \Symfony\Component\Process\Process(
            ['python3', $script, $tmpPdf],
            null,
            null,
            json_encode($payload),
            60
        );
        $process->run();

        if (!$process->isSuccessful() || !file_exists($tmpPdf)) {
            abort(500, 'Gagal generate PDF: ' . $process->getErrorOutput());
        }

        $filename = 'PERISAI_Kritikalitas_' . ($tahunContext?->tahun ?? 'ALL') . '_' . now()->format('Ymd_His') . '.pdf';


        return response()->file($tmpPdf, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }
}
