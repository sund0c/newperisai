<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RopaActivity;
use App\Models\RopaSubjectRight;
use App\Models\RopaRiskIndicator;
use App\Models\RopaAsset;
use App\Models\Opd;
use App\Models\Asset;
use App\Models\TahunAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class RopaActivityController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $query = RopaActivity::with(['opd', 'riskIndicators'])->forTahun($tahunContext->id);

        if (auth()->user()->hasRole('Admin OPD')) {
            $query->forOpd(auth()->user()->opd_id);
        }
        if ($request->filled('opd_id')) {
            $query->forOpd($request->opd_id);
        }
        if ($request->filled('search')) {
            $query->where('nama_aktivitas', 'like', '%' . $request->search . '%');
        }

        $activities = $query->orderBy('kode')->paginate(15)->withQueryString();

        $total = RopaActivity::forTahun($tahunContext->id)
            ->when(auth()->user()->hasRole('Admin OPD'), fn($q) =>
            $q->forOpd(auth()->user()->opd_id))
            ->count();

        $opds = Opd::orderBy('namaopd')->get();

        return view('admin.ropa.index', compact('activities', 'total', 'opds', 'tahunContext'));
    }

    // ── CREATE ───────────────────────────────────────────────────

    public function create()
    {
        $this->authorizeActiveYear();
        $opds   = Opd::orderBy('namaopd')->get();
        $assets = $this->getAssets();
        return view('admin.ropa.create', compact('opds', 'assets'));
    }

    // ── STORE ────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $this->authorizeActiveYear();
        $this->validateRequest($request);

        DB::transaction(function () use ($request) {
            $tahunContext = TahunAktif::find(session('tahun_context'))
                ?? TahunAktif::where('is_active', true)->firstOrFail();

            $activity = RopaActivity::create([
                'tahunaktif_id'         => $tahunContext->id,
                'opd_id'                => $this->resolveOpdId($request),
                'kode'                  => RopaActivity::generateKode(),
                'nama_aktivitas'        => $request->nama_aktivitas,
                'penanggung_jawab'      => $request->penanggung_jawab,
                'deskripsi_tujuan'      => $request->deskripsi_tujuan,
                'subjek_data'           => $request->subjek_data,
                'sumber_pemerolehan'    => $request->sumber_pemerolehan,
                'penyimpanan_data'      => $request->penyimpanan_data,
                'metode_elektronik'     => $request->boolean('metode_elektronik'),
                'metode_non_elektronik' => $request->boolean('metode_non_elektronik'),
                'referensi_dasar_hukum' => $request->referensi_dasar_hukum,
                'masa_retensi'          => $request->masa_retensi,
                'technical_security_controls'       => array_values(array_filter($request->input('technical_security_controls', []))),
                'privacy_governance_controls'        => array_values(array_filter($request->input('privacy_governance_controls', []))),
                'organizational_governance_controls' => array_values(array_filter($request->input('organizational_governance_controls', []))),
                'proses_sebelumnya'     => $request->proses_sebelumnya,
                'proses_setelahnya'     => $request->proses_setelahnya,
                'catatan'               => $request->catatan,
                'narasi_risiko'         => $request->narasi_risiko,
                'created_by'            => auth()->id(),
            ]);

            $this->syncChildRecords($activity, $request);
        });

        return redirect()->route('admin.ropa.index')
            ->with('success', 'Aktivitas RoPA berhasil ditambahkan.');
    }

    // ── SHOW / EDIT ──────────────────────────────────────────────

    public function show(RopaActivity $ropaActivity)
    {
        return $this->edit($ropaActivity);
    }

    public function edit(RopaActivity $ropaActivity)
    {
        $ropaActivity->load([
            'opd',
            'legalBases',
            'personalDataTypes',
            'recipients',
            'subjectRights',
            'riskIndicators',
            'assets.asset',
        ]);

        $opds       = Opd::orderBy('namaopd')->get();
        $assets     = $this->getAssets();
        $isEditable = $ropaActivity->isEditable();

        return view('admin.ropa.edit', compact('ropaActivity', 'opds', 'assets', 'isEditable'));
    }

    // ── UPDATE ───────────────────────────────────────────────────

    public function update(Request $request, RopaActivity $ropaActivity)
    {
        $this->authorizeActiveYear();

        if (!$ropaActivity->isEditable()) {
            return back()->with('error', 'Aktivitas tahun non-aktif tidak dapat diubah.');
        }

        $this->validateRequest($request);

        DB::transaction(function () use ($request, $ropaActivity) {
            $ropaActivity->update([
                'opd_id'                => $this->resolveOpdId($request),
                'nama_aktivitas'        => $request->nama_aktivitas,
                'penanggung_jawab'      => $request->penanggung_jawab,
                'deskripsi_tujuan'      => $request->deskripsi_tujuan,
                'subjek_data'           => $request->subjek_data,
                'sumber_pemerolehan'    => $request->sumber_pemerolehan,
                'penyimpanan_data'      => $request->penyimpanan_data,
                'metode_elektronik'     => $request->boolean('metode_elektronik'),
                'metode_non_elektronik' => $request->boolean('metode_non_elektronik'),
                'referensi_dasar_hukum' => $request->referensi_dasar_hukum,
                'masa_retensi'          => $request->masa_retensi,
                'technical_security_controls'       => array_values(array_filter($request->input('technical_security_controls', []))),
                'privacy_governance_controls'        => array_values(array_filter($request->input('privacy_governance_controls', []))),
                'organizational_governance_controls' => array_values(array_filter($request->input('organizational_governance_controls', []))),
                'proses_sebelumnya'     => $request->proses_sebelumnya,
                'proses_setelahnya'     => $request->proses_setelahnya,
                'catatan'               => $request->catatan,
                'narasi_risiko'         => $request->narasi_risiko,
                'updated_by'            => auth()->id(),
            ]);

            $this->syncChildRecords($ropaActivity, $request);
        });

        return redirect()->route('admin.ropa.edit', $ropaActivity)
            ->with('success', 'Aktivitas RoPA berhasil disimpan.');
    }

    // ── DESTROY ──────────────────────────────────────────────────

    public function destroy(RopaActivity $ropaActivity)
    {
        $this->authorizeActiveYear();

        if (!$ropaActivity->isEditable()) {
            return back()->with('error', 'Aktivitas tahun non-aktif tidak dapat dihapus.');
        }

        $ropaActivity->delete();

        return redirect()->route('admin.ropa.index')
            ->with('success', "Aktivitas {$ropaActivity->kode} berhasil dihapus.");
    }

    // ── EXPORT PDF LIST ──────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $user    = auth()->user();
        $isAdmin = $user->hasRole(['Super Admin', 'admin']);

        $query = RopaActivity::with(['opd', 'legalBases', 'riskIndicators'])->forTahun($tahunContext->id);

        if (!$isAdmin) $query->forOpd($user->opd_id);
        if ($request->filled('opd_id')) $query->forOpd($request->opd_id);

        $activities = $query->orderBy('kode')->get();

        $meta = [
            'tahun'        => $tahunContext->tahun,
            'pemilik_aset' => 'PEMERINTAH PROVINSI BALI',
            'opd'          => $request->filled('opd_id')
                ? (Opd::find($request->opd_id)?->namaopd ?? 'Semua OPD')
                : 'Semua OPD',
            'total'        => $activities->count(),
            'generated_at' => now()->locale('id')->translatedFormat('l, d F Y H:i') . ' WITA',
        ];

        $rows = $activities->values()->map(fn($a, $i) => [
            'no'               => $i + 1,
            'kode'             => $a->kode,
            'nama_aktivitas'   => $a->nama_aktivitas,
            'opd'              => $a->opd?->namaopd ?? '-',
            'penanggung_jawab' => $a->penanggung_jawab,
            'dasar_pemrosesan' => $a->legalBases->pluck('dasar_pemrosesan')->toArray(),
            'dpia_required'    => $a->riskIndicators->isNotEmpty(),
        ])->toArray();

        return $this->runPythonPdf(
            base_path('scripts/generate_ropa_list_pdf.py'),
            ['meta' => $meta, 'rows' => $rows],
            'PERISAI_RoPA_' . $tahunContext->tahun . '_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    // ── EXPORT PDF DETAIL ────────────────────────────────────────

    public function exportDetailPdf(RopaActivity $ropaActivity)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $ropaActivity->load([
            'opd',
            'legalBases',
            'personalDataTypes',
            'recipients',
            'subjectRights',
            'riskIndicators',
            'assets.asset',
        ]);

        $meta = [
            'tahun'        => $tahunContext->tahun,
            'opd'          => $ropaActivity->opd?->namaopd ?? '-',
            'generated_at' => now()->locale('id')->translatedFormat('l, d F Y H:i') . ' WITA',
        ];

        $activity = [
            'kode'                  => $ropaActivity->kode,
            'nama_aktivitas'        => $ropaActivity->nama_aktivitas,
            'opd'                   => $ropaActivity->opd?->namaopd ?? '-',
            'penanggung_jawab'      => $ropaActivity->penanggung_jawab,
            'deskripsi_tujuan'      => $ropaActivity->deskripsi_tujuan,
            'subjek_data'           => $ropaActivity->subjek_data,
            'sumber_pemerolehan'    => $ropaActivity->sumber_pemerolehan,
            'penyimpanan_data'      => $ropaActivity->penyimpanan_data,
            'metode_elektronik'     => $ropaActivity->metode_elektronik,
            'metode_non_elektronik' => $ropaActivity->metode_non_elektronik,
            'referensi_dasar_hukum' => $ropaActivity->referensi_dasar_hukum,
            'masa_retensi'          => $ropaActivity->masa_retensi,
            'technical_security_controls'       => $ropaActivity->technical_security_controls,
            'privacy_governance_controls'        => $ropaActivity->privacy_governance_controls,
            'organizational_governance_controls' => $ropaActivity->organizational_governance_controls,
            'proses_sebelumnya'     => $ropaActivity->proses_sebelumnya,
            'proses_setelahnya'     => $ropaActivity->proses_setelahnya,
            'catatan'               => $ropaActivity->catatan,
            'narasi_risiko'         => $ropaActivity->narasi_risiko,
            'legal_bases'           => $ropaActivity->legalBases->map(fn($l) => [
                'dasar_pemrosesan' => $l->dasar_pemrosesan,
            ])->toArray(),
            'personal_data_types'   => $ropaActivity->personalDataTypes->map(fn($d) => [
                'jenis_data'  => $d->jenis_data,
                'is_spesifik' => $d->is_spesifik,
            ])->toArray(),
            'recipients'            => $ropaActivity->recipients->map(fn($r) => [
                'profil_penerima'      => $r->profil_penerima,
                'tipe'                 => $r->tipe,
                'peran'                => $r->peran,
                'tujuan_pengiriman'    => $r->tujuan_pengiriman,
                'mekanisme_pengiriman' => $r->mekanisme_pengiriman,
                'jenis_data_dikirim'   => $r->jenis_data_dikirim,
            ])->toArray(),
            'subject_rights'        => $ropaActivity->subjectRights->map(fn($h) => [
                'pasal' => $h->pasal,
            ])->toArray(),
            'risk_indicators'        => $ropaActivity->riskIndicators->map(fn($i) => [
                'indikator' => $i->indikator,
            ])->toArray(),
            'assets'                => $ropaActivity->assets->map(fn($a) => [
                'nama'       => $a->nama,
                'peran_aset' => $a->peran_aset,
            ])->toArray(),
        ];

        return $this->runPythonPdf(
            base_path('scripts/generate_ropa_detail_pdf.py'),
            ['meta' => $meta, 'activity' => $activity],
            'PERISAI_RoPA_' . $ropaActivity->kode . '_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    // ── PRIVATE HELPERS ──────────────────────────────────────────

    private function syncChildRecords(RopaActivity $activity, Request $request): void
    {
        // Legal bases
        $activity->legalBases()->delete();
        foreach ($request->input('dasar_pemrosesan', []) as $dasar) {
            $activity->legalBases()->create(['dasar_pemrosesan' => $dasar]);
        }

        // Personal data types
        $activity->personalDataTypes()->delete();
        foreach ($request->input('data_umum', []) as $jenis) {
            $activity->personalDataTypes()->create(['is_spesifik' => false, 'jenis_data' => $jenis]);
        }
        foreach ($request->input('data_spesifik', []) as $jenis) {
            $activity->personalDataTypes()->create(['is_spesifik' => true, 'jenis_data' => $jenis]);
        }

        // Subject rights
        $activity->subjectRights()->delete();
        foreach ($request->input('hak_subjek', []) as $pasal) {
            $activity->subjectRights()->create([
                'pasal'    => (int) $pasal,
                'nama_hak' => RopaSubjectRight::HAK[(int) $pasal] ?? '',
            ]);
        }

        // Assets
        $activity->assets()->delete();
        foreach ($request->input('assets', []) as $assetData) {
            if (empty($assetData['asset_instance_id']) && empty($assetData['nama_manual'])) continue;
            $activity->assets()->create([
                'asset_instance_id' => $assetData['asset_instance_id'] ?? null,
                'nama_manual'       => $assetData['nama_manual'] ?? null,
                'peran_aset'        => $assetData['peran_aset'] ?? 'primer',
            ]);
        }

        // Risk indicators
        $activity->riskIndicators()->delete();
        foreach ($request->input('indikator_risiko', []) as $indikator) {
            // data_spesifik dikontrol otomatis dari tab Data Pribadi — skip jika ada
            if ($indikator === 'data_spesifik') continue;
            $activity->riskIndicators()->create(['indikator' => $indikator]);
        }
        // Otomatis: jika ada data spesifik tercentang → indikator data_spesifik aktif
        $hasDataSpesifik = $activity->personalDataTypes()
            ->where('is_spesifik', true)->exists();
        if ($hasDataSpesifik) {
            $activity->riskIndicators()->firstOrCreate(['indikator' => 'data_spesifik']);
        }

        // Recipients
        if ($request->has('recipients')) {
            $activity->recipients()->delete();
            foreach ($request->input('recipients', []) as $row) {
                if (empty($row['profil_penerima'])) continue;
                $activity->recipients()->create([
                    'profil_penerima'      => $row['profil_penerima'],
                    'tipe'                 => $row['tipe'],
                    'peran'                => $row['tipe'] === 'eksternal' ? ($row['peran'] ?? null) : null,
                    'kontak_pic'           => $row['tipe'] === 'eksternal' ? ($row['kontak_pic'] ?? null) : null,
                    'tujuan_pengiriman'    => $row['tujuan_pengiriman'] ?? null,
                    'jenis_data_dikirim'   => $row['jenis_data_dikirim'] ?? null,
                    'mekanisme_pengiriman' => $row['mekanisme_pengiriman'] ?? null,
                ]);
            }
        }
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'nama_aktivitas'       => 'required|string|max:255',
            'penanggung_jawab'     => 'required|string|max:255',
            'deskripsi_tujuan'     => 'required|string',
            'subjek_data'          => 'required|string|max:255',
            'sumber_pemerolehan'   => 'required|string|max:255',
            'dasar_pemrosesan'     => 'required|array|min:1',
            'dasar_pemrosesan.*'   => 'in:consent,contractual,legal_obligation,vital_interests,public_interests,legitimate_interests,keseimbangan_kepentingan',
            'opd_id'               => 'nullable|exists:opds,id',
            'recipients.*.profil_penerima' => 'nullable|string|max:255',
            'recipients.*.tipe'            => 'nullable|in:internal,eksternal',
            'recipients.*.peran'           => 'nullable|in:pengendali,pengendali_bersama,prosesor',
            'assets.*.asset_instance_id'   => 'nullable|exists:assets,id',
            'assets.*.peran_aset'          => 'nullable|in:primer,pendukung,penyimpanan,transmisi',
            'indikator_risiko.*'           => 'in:keputusan_otomatis,data_spesifik,skala_besar,evaluasi_penskoran,pencocokan_data,teknologi_baru,membatasi_hak',
        ]);
    }

    private function resolveOpdId(Request $request): int
    {
        if (auth()->user()->hasRole('Admin OPD')) {
            return auth()->user()->opd_id;
        }
        return $request->opd_id;
    }

    private function authorizeActiveYear(): void
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();
        $tahunAktif   = TahunAktif::where('is_active', true)->firstOrFail();

        if ($tahunContext->id !== $tahunAktif->id) {
            abort(403, 'Operasi tidak diizinkan pada tahun non-aktif.');
        }
    }

    private function getAssets()
    {
        return Asset::with('subKlasifikasi.klasifikasi')
            ->whereHas('subKlasifikasi.klasifikasi', fn($q) =>
            $q->where('klasifikasiaset', 'Data & Informasi')
                ->orWhere('klasifikasiaset', 'Perangkat Lunak'))
            ->when(auth()->user()->hasRole('Admin OPD'), fn($q) =>
            $q->where('opd_id', auth()->user()->opd_id))
            ->orderBy('nama_aset')
            ->get();
    }

    private function runPythonPdf(string $script, array $payload, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $tmpPdf = sys_get_temp_dir() . '/perisai_ropa_' . Str::random(8) . '.pdf';

        $process = new Process(
            ['python3', $script, $tmpPdf],
            null,
            null,
            json_encode($payload, JSON_UNESCAPED_UNICODE),
            60
        );
        $process->run();

        if (!$process->isSuccessful() || !file_exists($tmpPdf)) {
            Log::error('RoPA PDF generation failed', [
                'script' => $script,
                'stderr' => $process->getErrorOutput(),
            ]);
            abort(500, 'Gagal generate PDF RoPA. Periksa log server.');
        }

        return response()->file($tmpPdf, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }
}
