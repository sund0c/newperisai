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
        // Resolve year context sama seperti AssetController
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::getActive();

        // Base query: aset aktif (tidak terhapus) pada tahun context
        $query = Asset::with([
            'opd',
            'subKlasifikasi.klasifikasi',
            'criticality',
        ])
            ->where('TahunAktif_id', $tahunContext?->id);

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

        // Filter: Klasifikasi (via subKlasifikasi.klasifikasi)
        if ($klasifikasi = $request->klasifikasi) {
            $query->whereHas(
                'subKlasifikasi.klasifikasi',
                fn($q) => $q->where('klasifikasiaset', $klasifikasi)
            );
        }

        // Filter: Kritikalitas
        if ($kritikalitas = $request->kritikalitas) {
            $query->whereHas(
                'criticality',
                fn($q) =>
                $q->where('kritikalitas', $kritikalitas)
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

        // Stats
        $totalAset      = Asset::where('TahunAktif_id', $tahunContext?->id)->count();
        $totalDinilai   = AssetCriticality::whereHas(
            'asset',
            fn($q) =>
            $q->where('TahunAktif_id', $tahunContext?->id)
        )->count();
        $totalBelumNilai = $totalAset - $totalDinilai;
        $totalTinggi    = AssetCriticality::whereHas(
            'asset',
            fn($q) =>
            $q->where('TahunAktif_id', $tahunContext?->id)
        )->where('kritikalitas', 3)->count();

        $opds        = Opd::orderBy('namaopd')->get();
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
            'totalDinilai',
            'totalBelumNilai',
            'totalTinggi',
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
            'confidentiality.in'       => 'Nilai Confidentiality tidak valid.',
            'integrity.in'             => 'Nilai Integrity tidak valid.',
            'availability.in'          => 'Nilai Availability tidak valid.',
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
}
