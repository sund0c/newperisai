<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;



use App\Models\Asset;
use App\Models\Opd;
use App\Models\RiskRegister;
use App\Models\RiskRegisterItem;
use App\Models\VulnerabilityItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RiskRegisterController extends Controller
{
    // ─────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $user     = auth()->user();
        $tahunCtx = session('tahun_context');

        $query = RiskRegister::with(['asset.subKlasifikasi', 'opd', 'tahunaktif'])
            ->when($tahunCtx, fn($q) => $q->where('tahunaktif_id', $tahunCtx))
            ->when(
                $user->hasRole(['Admin OPD', 'Operator', 'Viewer']),
                fn($q) => $q->where('opd_id', $user->opd_id)
            )
            ->when(
                $request->search,
                fn($q) =>
                $q->where('kode_rr', 'like', '%' . $request->search . '%')
                    ->orWhereHas(
                        'asset',
                        fn($sq) =>
                        $sq->where('nama_aset', 'like', '%' . $request->search . '%')
                            ->orWhere('kode_aset', 'like', '%' . $request->search . '%')
                    )
            )
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at');

        $registers = $query->paginate(15)->withQueryString();

        return view('admin.risk-register.index', compact('registers'));
    }

    // ─────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────
    public function create()
    {
        $user     = auth()->user();
        $tahunCtx = session('tahun_context');

        $assets = Asset::with(['subKlasifikasi.klasifikasi', 'opd'])
            ->when($tahunCtx, fn($q) => $q->where('tahunaktif_id', $tahunCtx))
            ->when(
                $user->hasRole(['Admin OPD', 'Operator']),
                fn($q) => $q->where('opd_id', $user->opd_id)
            )
            ->orderBy('nama_aset')
            ->get();

        return view('admin.risk-register.create', compact('assets'));
    }

    // ─────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'asset_id'   => 'required|uuid|exists:assets,id',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $user  = auth()->user();
        $asset = Asset::with(['opd', 'tahunaktif'])->findOrFail($request->asset_id);

        $tahun     = $asset->tahunaktif->tahun ?? now()->year;
        $opdPrefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $asset->opd->namaopd ?? 'OPD'), 0, 4));

        $versiTerakhir = RiskRegister::withTrashed()
            ->where('asset_id', $request->asset_id)
            ->max('versi') ?? 0;

        $kodeRr = RiskRegister::generateKode($tahun, $opdPrefix);

        $rr = DB::transaction(function () use ($request, $user, $asset, $versiTerakhir, $kodeRr) {
            return RiskRegister::create([
                'kode_rr'       => $kodeRr,
                'asset_id'      => $request->asset_id,
                'tahunaktif_id' => $asset->tahunaktif_id,
                'opd_id'        => $asset->opd_id,
                'versi'         => $versiTerakhir + 1,
                'status'        => 'draft',
                'keterangan'    => $request->keterangan,
                'dibuat_oleh'   => $user->id,
            ]);
        });

        return redirect()->route('admin.risk-register.edit', $rr)
            ->with('success', 'Risk Register ' . $kodeRr . ' berhasil dibuat.');
    }

    // ─────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────
    public function show(RiskRegister $riskRegister)
    {
        $riskRegister->load([
            'asset.subKlasifikasi.klasifikasi',
            'asset.opd',
            'opd',
            'tahunaktif',
            'items',
            'dibuatOleh',
            'difinalisasiOleh',
        ]);

        $versions = RiskRegister::where('asset_id', $riskRegister->asset_id)
            ->orderByDesc('versi')
            ->get(['id', 'kode_rr', 'versi', 'status', 'created_at']);

        return view('admin.risk-register.show', compact('riskRegister', 'versions'));
    }

    // ─────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────
    public function edit(RiskRegister $riskRegister)
    {
        abort_if($riskRegister->isFinal(), 403, 'Risk Register yang sudah final tidak dapat diubah.');

        $riskRegister->load(['asset.subKlasifikasi', 'opd', 'items']);

        $subKlasifikasiId = $riskRegister->asset->sub_klasifikasi_id ?? null;

        $masterKerawanan = VulnerabilityItem::with("set")
            ->when(
                $subKlasifikasiId,
                fn($q) =>
                $q->whereHas(
                    'set',
                    fn($sq) =>
                    $sq->where('scope_id', $subKlasifikasiId)
                )
            )
            ->orderBy('ancaman_tipikal')
            ->get();

        $areaDampakOptions = [
            'Finansial',
            'Reputasi',
            'Kinerja',
            'Layanan Organisasi',
            'Operasional TIK',
            'Hukum dan Regulasi',
        ];

        $keputusanOptions = ['Accept', 'Mitigate', 'Transfer', 'Avoid'];
        $prioritasOptions = ['Tinggi', 'Sedang', 'Rendah'];

        return view('admin.risk-register.edit', compact(
            'riskRegister',
            'masterKerawanan',
            'areaDampakOptions',
            'keputusanOptions',
            'prioritasOptions',
        ));
    }

    // ─────────────────────────────────────────
    // UPDATE (header/keterangan)
    // ─────────────────────────────────────────
    public function update(Request $request, RiskRegister $riskRegister)
    {
        abort_if($riskRegister->isFinal(), 403);
        $request->validate(['keterangan' => 'nullable|string|max:1000']);
        $riskRegister->update(['keterangan' => $request->keterangan]);
        return back()->with('success', 'Keterangan berhasil disimpan.');
    }

    // ─────────────────────────────────────────
    // FINALIZE
    // ─────────────────────────────────────────
    public function finalize(RiskRegister $riskRegister)
    {
        abort_if($riskRegister->isFinal(), 403);
        abort_if($riskRegister->items()->count() === 0, 422, 'Tambahkan minimal satu item risiko sebelum finalisasi.');

        $riskRegister->update([
            'status'            => 'final',
            'difinalisasi_oleh' => auth()->id(),
            'difinalisasi_at'   => now(),
        ]);

        return redirect()->route('admin.risk-register.show', $riskRegister)
            ->with('success', 'Risk Register ' . $riskRegister->kode_rr . ' v' . $riskRegister->versi . ' berhasil difinalisasi.');
    }

    // ─────────────────────────────────────────
    // REVISI — buat versi baru dari final
    // ─────────────────────────────────────────
    public function revisi(RiskRegister $riskRegister)
    {
        abort_if($riskRegister->isDraft(), 403);

        $existingDraft = RiskRegister::where('asset_id', $riskRegister->asset_id)
            ->where('status', 'draft')
            ->first();

        if ($existingDraft) {
            return redirect()->route('admin.risk-register.edit', $existingDraft)
                ->with('info', 'Sudah ada draft revisi untuk aset ini.');
        }

        $versiTerakhir = RiskRegister::withTrashed()
            ->where('asset_id', $riskRegister->asset_id)
            ->max('versi');

        $tahun     = $riskRegister->tahunaktif->tahun ?? now()->year;
        $opdPrefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $riskRegister->opd->namaopd ?? 'OPD'), 0, 4));
        $kodeRr    = RiskRegister::generateKode($tahun, $opdPrefix);

        $newRr = DB::transaction(function () use ($riskRegister, $versiTerakhir, $kodeRr) {
            $newRr = RiskRegister::create([
                'kode_rr'       => $kodeRr,
                'asset_id'      => $riskRegister->asset_id,
                'tahunaktif_id' => $riskRegister->tahunaktif_id,
                'opd_id'        => $riskRegister->opd_id,
                'versi'         => $versiTerakhir + 1,
                'status'        => 'draft',
                'keterangan'    => $riskRegister->keterangan,
                'dibuat_oleh'   => auth()->id(),
            ]);

            foreach ($riskRegister->items as $item) {
                RiskRegisterItem::create(array_merge(
                    $item->only($item->getFillable()),
                    ['risk_register_id' => $newRr->id]
                ));
            }

            return $newRr;
        });

        return redirect()->route('admin.risk-register.edit', $newRr)
            ->with('success', 'Revisi v' . $newRr->versi . ' berhasil dibuat dari ' . $riskRegister->kode_rr . '.');
    }

    // ─────────────────────────────────────────
    // ITEM STORE
    // ─────────────────────────────────────────
    public function storeItem(Request $request, RiskRegister $riskRegister)
    {
        abort_if($riskRegister->isFinal(), 403);

        $validated = $request->validate([
            'jenis_risiko'             => 'nullable|string|max:100',
            'ancaman'                  => 'required|string|max:500',
            'kerawanan'                => 'required|string|max:500',
            'kategori'                 => 'nullable|string|max:100',
            'dampak_detail'            => 'nullable|string',
            'area_dampak'              => 'nullable|array',
            'area_dampak.*'            => 'string',
            'vulnerability_item_id'    => 'nullable|uuid',
            'kontrol_saat_ini'         => 'nullable|string',
            'rencana_aksi'             => 'nullable|string',
            'inherent_dampak'          => 'required|integer|between:1,5',
            'inherent_kemungkinan'     => 'required|integer|between:1,5',
            'keputusan_penanganan'     => 'nullable|string|max:50',
            'prioritas_risiko'         => 'nullable|string|max:30',
            'opsi_penanganan'          => 'nullable|string|max:100',
            'keluaran'                 => 'nullable|string',
            'target_jadwal'            => 'nullable|string|max:100',
            'penanggung_jawab'         => 'nullable|string|max:150',
            'ada_residual_risk'        => 'nullable|boolean',
            'residual_dampak'          => 'nullable|integer|between:1,5',
            'residual_kemungkinan'     => 'nullable|integer|between:1,5',
            'residual_status'          => 'nullable|string|max:30',
            'rencana_kontrol_tambahan' => 'nullable|string',
            'risk_owner'               => 'nullable|string|max:150',
        ]);

        $riskNo = $riskRegister->items()->count() + 1;

        $inherentSkor  = RiskRegisterItem::hitungSkor($request->inherent_kemungkinan, $request->inherent_dampak);
        $inherentLevel = RiskRegisterItem::skorKeLevel($inherentSkor);

        $residualSkor = $residualLevel = null;
        $adaResidual  = $request->boolean('ada_residual_risk');
        if ($adaResidual && $request->residual_dampak && $request->residual_kemungkinan) {
            $residualSkor  = RiskRegisterItem::hitungSkor($request->residual_kemungkinan, $request->residual_dampak);
            $residualLevel = RiskRegisterItem::skorKeLevel($residualSkor);
        }

        RiskRegisterItem::create([
            'risk_register_id'         => $riskRegister->id,
            'risk_no'                  => $riskNo,
            'jenis_risiko'             => $request->jenis_risiko,
            'ancaman'                  => $request->ancaman,
            'kerawanan'                => $request->kerawanan,
            'kategori'                 => $request->kategori,
            'dampak_detail'            => $request->dampak_detail,
            'area_dampak'              => $request->area_dampak,
            'vulnerability_item_id'    => $request->vulnerability_item_id,
            'kontrol_saat_ini'         => $request->kontrol_saat_ini,
            'rencana_aksi'             => $request->rencana_aksi,
            'inherent_dampak'          => $request->inherent_dampak,
            'inherent_kemungkinan'     => $request->inherent_kemungkinan,
            'inherent_skor'            => $inherentSkor,
            'inherent_level'           => $inherentLevel,
            'keputusan_penanganan'     => $request->keputusan_penanganan,
            'prioritas_risiko'         => $request->prioritas_risiko,
            'opsi_penanganan'          => $request->opsi_penanganan,
            'keluaran'                 => $request->keluaran,
            'target_jadwal'            => $request->target_jadwal,
            'penanggung_jawab'         => $request->penanggung_jawab,
            'ada_residual_risk'        => $adaResidual,
            'residual_dampak'          => $adaResidual ? $request->residual_dampak : null,
            'residual_kemungkinan'     => $adaResidual ? $request->residual_kemungkinan : null,
            'residual_skor'            => $residualSkor,
            'residual_level'           => $residualLevel,
            'residual_status'          => $request->residual_status,
            'rencana_kontrol_tambahan' => $request->rencana_kontrol_tambahan,
            'risk_owner'               => $request->risk_owner,
        ]);

        return back()->with('success', 'Item risiko #' . $riskNo . ' berhasil ditambahkan.');
    }

    // ─────────────────────────────────────────
    // ITEM UPDATE
    // ─────────────────────────────────────────
    public function updateItem(Request $request, RiskRegister $riskRegister, RiskRegisterItem $item)
    {
        abort_if($riskRegister->isFinal(), 403);
        abort_if($item->risk_register_id !== $riskRegister->id, 403);

        $request->validate([
            'jenis_risiko'             => 'nullable|string|max:100',
            'ancaman'                  => 'required|string|max:500',
            'kerawanan'                => 'required|string|max:500',
            'kategori'                 => 'nullable|string|max:100',
            'dampak_detail'            => 'nullable|string',
            'area_dampak'              => 'nullable|array',
            'vulnerability_item_id'    => 'nullable|uuid',
            'kontrol_saat_ini'         => 'nullable|string',
            'rencana_aksi'             => 'nullable|string',
            'inherent_dampak'          => 'required|integer|between:1,5',
            'inherent_kemungkinan'     => 'required|integer|between:1,5',
            'keputusan_penanganan'     => 'nullable|string|max:50',
            'prioritas_risiko'         => 'nullable|string|max:30',
            'opsi_penanganan'          => 'nullable|string|max:100',
            'keluaran'                 => 'nullable|string',
            'target_jadwal'            => 'nullable|string|max:100',
            'penanggung_jawab'         => 'nullable|string|max:150',
            'ada_residual_risk'        => 'nullable|boolean',
            'residual_dampak'          => 'nullable|integer|between:1,5',
            'residual_kemungkinan'     => 'nullable|integer|between:1,5',
            'residual_status'          => 'nullable|string|max:30',
            'rencana_kontrol_tambahan' => 'nullable|string',
            'risk_owner'               => 'nullable|string|max:150',
        ]);

        $inherentSkor  = RiskRegisterItem::hitungSkor($request->inherent_kemungkinan, $request->inherent_dampak);
        $inherentLevel = RiskRegisterItem::skorKeLevel($inherentSkor);

        $residualSkor = $residualLevel = null;
        $adaResidual  = $request->boolean('ada_residual_risk');
        if ($adaResidual && $request->residual_dampak && $request->residual_kemungkinan) {
            $residualSkor  = RiskRegisterItem::hitungSkor($request->residual_kemungkinan, $request->residual_dampak);
            $residualLevel = RiskRegisterItem::skorKeLevel($residualSkor);
        }

        $item->update([
            'jenis_risiko'             => $request->jenis_risiko,
            'ancaman'                  => $request->ancaman,
            'kerawanan'                => $request->kerawanan,
            'kategori'                 => $request->kategori,
            'dampak_detail'            => $request->dampak_detail,
            'area_dampak'              => $request->area_dampak,
            'vulnerability_item_id'    => $request->vulnerability_item_id,
            'kontrol_saat_ini'         => $request->kontrol_saat_ini,
            'rencana_aksi'             => $request->rencana_aksi,
            'inherent_dampak'          => $request->inherent_dampak,
            'inherent_kemungkinan'     => $request->inherent_kemungkinan,
            'inherent_skor'            => $inherentSkor,
            'inherent_level'           => $inherentLevel,
            'keputusan_penanganan'     => $request->keputusan_penanganan,
            'prioritas_risiko'         => $request->prioritas_risiko,
            'opsi_penanganan'          => $request->opsi_penanganan,
            'keluaran'                 => $request->keluaran,
            'target_jadwal'            => $request->target_jadwal,
            'penanggung_jawab'         => $request->penanggung_jawab,
            'ada_residual_risk'        => $adaResidual,
            'residual_dampak'          => $adaResidual ? $request->residual_dampak : null,
            'residual_kemungkinan'     => $adaResidual ? $request->residual_kemungkinan : null,
            'residual_skor'            => $residualSkor,
            'residual_level'           => $residualLevel,
            'residual_status'          => $request->residual_status,
            'rencana_kontrol_tambahan' => $request->rencana_kontrol_tambahan,
            'risk_owner'               => $request->risk_owner,
        ]);

        return back()->with('success', 'Item risiko #' . $item->risk_no . ' berhasil diperbarui.');
    }

    // ─────────────────────────────────────────
    // ITEM DESTROY
    // ─────────────────────────────────────────
    public function destroyItem(RiskRegister $riskRegister, RiskRegisterItem $item)
    {
        abort_if($riskRegister->isFinal(), 403);
        abort_if($item->risk_register_id !== $riskRegister->id, 403);

        $deletedNo = $item->risk_no;
        $item->delete();

        // Renumber
        $riskRegister->items()->orderBy('risk_no')->each(function ($i, $idx) {
            $i->update(['risk_no' => $idx + 1]);
        });

        return back()->with('success', 'Item risiko #' . $deletedNo . ' berhasil dihapus.');
    }

    // ─────────────────────────────────────────
    // API — data master kerawanan untuk Alpine.js
    // ─────────────────────────────────────────
    public function getMasterKerawanan(Request $request)
    {
        $item = VulnerabilityItem::findOrFail($request->id);

        return response()->json([
            'ancaman'          => $item->ancaman_tipikal,
            'kerawanan'        => $item->deskripsi,
            'dampak_detail'    => $item->dampak_tipikal,
            'kontrol_saat_ini' => $item->kontrol_tipikal,
            'rencana_aksi'     => $item->mitigasi_tipikal,
        ]);
    }
}
