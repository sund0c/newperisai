<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dpia;
use App\Models\DpiaThreshold;
use App\Models\DpiaRisiko;
use App\Models\DpiaTim;
use App\Models\RopaActivity;
use App\Models\Opd;
use App\Models\TahunAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class DpiaController extends Controller
{
    // ── INDEX ────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        $query = Dpia::with(['opd', 'ropaActivity'])
            ->where('tahunaktif_id', $tahunContext->id);

        if (auth()->user()->hasRole('opd')) {
            $query->where('opd_id', auth()->user()->opd_id);
        }
        if ($request->filled('opd_id')) {
            $query->where('opd_id', $request->opd_id);
        }
        if ($request->filled('search')) {
            $query->where('nama_aktivitas', 'like', '%' . $request->search . '%');
        }

        $dpias = $query->orderBy('kode')->paginate(15)->withQueryString();
        $total = Dpia::where('tahunaktif_id', $tahunContext->id)
            ->when(auth()->user()->hasRole('opd'), fn($q) =>
                $q->where('opd_id', auth()->user()->opd_id))
            ->count();

        $opds = Opd::orderBy('namaopd')->get();

        return view('admin.dpia.index', compact('dpias', 'total', 'opds', 'tahunContext'));
    }

    // ── CREATE ───────────────────────────────────────────────────

    public function create()
    {
        $this->authorizeActiveYear();

        $tahunContext = TahunAktif::find(session('tahun_context'))
            ?? TahunAktif::where('is_active', true)->firstOrFail();

        // RoPA yang belum punya DPIA & sudah ada indikator risiko
        $ropaQuery = RopaActivity::with('riskIndicators')
            ->where('tahunaktif_id', $tahunContext->id)
            ->whereHas('riskIndicators')
            ->whereDoesntHave('dpia');

        if (auth()->user()->hasRole('opd')) {
            $ropaQuery->where('opd_id', auth()->user()->opd_id);
        }

        $ropaList = $ropaQuery->orderBy('kode')->get();
        $opds     = Opd::orderBy('namaopd')->get();

        return view('admin.dpia.create', compact('ropaList', 'opds', 'tahunContext'));
    }

    // ── STORE ────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $this->authorizeActiveYear();

        $request->validate([
            'ropa_activity_id' => 'required|uuid|exists:ropa_activities,id|unique:dpias,ropa_activity_id',
            'penanggung_jawab' => 'required|string|max:255',
            'ppd'              => 'nullable|string|max:255',
            'tanggal_penyusunan' => 'required|date',
            'versi'            => 'nullable|string|max:10',
        ]);

        DB::transaction(function () use ($request) {
            $tahunContext = TahunAktif::find(session('tahun_context'))
                ?? TahunAktif::where('is_active', true)->firstOrFail();

            $ropa = RopaActivity::with(['opd', 'riskIndicators'])->findOrFail($request->ropa_activity_id);

            $dpia = Dpia::create([
                'tahunaktif_id'          => $tahunContext->id,
                'opd_id'                 => $ropa->opd_id,
                'ropa_activity_id'       => $ropa->id,
                'kode'                   => Dpia::generateKode(),
                'nama_aktivitas'         => $ropa->nama_aktivitas,
                'penanggung_jawab'       => $request->penanggung_jawab,
                'ppd'                    => $request->ppd,
                'tanggal_penyusunan'     => $request->tanggal_penyusunan,
                'versi'                  => $request->versi ?? '1.0',
                'konsultasi_stakeholder' => $request->konsultasi_stakeholder,
                'kriteria_risiko'        => $request->kriteria_risiko,
                'evaluasi_residual'      => $request->evaluasi_residual,
                'kesimpulan'             => $request->kesimpulan,
                'created_by'             => auth()->id(),
            ]);

            // Sync threshold dari RoPA otomatis
            $dpia->syncThresholdsFromRopa();

            // Simpan keterangan threshold jika diisi
            foreach ($request->input('threshold_keterangan', []) as $indikator => $ket) {
                $dpia->thresholds()->where('indikator', $indikator)
                    ->update(['keterangan' => $ket ?: null]);
            }

            $this->syncChildRecords($dpia, $request);
        });

        return redirect()->route('admin.dpia.index')
            ->with('success', 'DPIA berhasil dibuat.');
    }

    // ── EDIT ─────────────────────────────────────────────────────

    public function edit(Dpia $dpia)
    {
        $dpia->load([
            'opd', 'ropaActivity.riskIndicators',
            'thresholds', 'tim', 'risikos',
        ]);

        $isEditable = $dpia->isEditable();

        return view('admin.dpia.edit', compact('dpia', 'isEditable'));
    }

    // ── UPDATE ───────────────────────────────────────────────────

    public function update(Request $request, Dpia $dpia)
    {
        $this->authorizeActiveYear();

        if (!$dpia->isEditable()) {
            return back()->with('error', 'DPIA tahun non-aktif tidak dapat diubah.');
        }

        $request->validate([
            'penanggung_jawab'   => 'required|string|max:255',
            'ppd'                => 'nullable|string|max:255',
            'tanggal_penyusunan' => 'required|date',
            'versi'              => 'nullable|string|max:10',
        ]);

        DB::transaction(function () use ($request, $dpia) {
            $dpia->update([
                'penanggung_jawab'       => $request->penanggung_jawab,
                'ppd'                    => $request->ppd,
                'tanggal_penyusunan'     => $request->tanggal_penyusunan,
                'versi'                  => $request->versi ?? '1.0',
                'konsultasi_stakeholder' => $request->konsultasi_stakeholder,
                'kriteria_risiko'        => $request->kriteria_risiko,
                'evaluasi_residual'      => $request->evaluasi_residual,
                'kesimpulan'             => $request->kesimpulan,
                'updated_by'             => auth()->id(),
            ]);

            // Update keterangan threshold — terpenuhi tidak bisa diubah manual
            foreach ($request->input('threshold_keterangan', []) as $indikator => $ket) {
                $dpia->thresholds()->where('indikator', $indikator)
                    ->update(['keterangan' => $ket ?: null]);
            }

            $this->syncChildRecords($dpia, $request);
        });

        return redirect()->route('admin.dpia.edit', $dpia)
            ->with('success', 'DPIA berhasil disimpan.');
    }

    // ── DESTROY ──────────────────────────────────────────────────

    public function destroy(Dpia $dpia)
    {
        $this->authorizeActiveYear();

        if (!$dpia->isEditable()) {
            return back()->with('error', 'DPIA tahun non-aktif tidak dapat dihapus.');
        }

        $kode = $dpia->kode;
        $dpia->delete();

        return redirect()->route('admin.dpia.index')
            ->with('success', "DPIA {$kode} berhasil dihapus.");
    }

    // ── EXPORT PDF DETAIL ────────────────────────────────────────

    public function exportDetailPdf(Dpia $dpia)
    {
        $dpia->load(['opd', 'ropaActivity', 'thresholds', 'tim', 'risikos']);

        $meta = [
            'tahun'        => $dpia->tahunAktif?->tahun ?? '-',
            'generated_at' => now()->locale('id')->translatedFormat('l, d F Y H:i') . ' WITA',
        ];

        $payload = [
            'meta' => $meta,
            'dpia' => [
                'dpia_kode'              => $dpia->kode,
                'ropa_kode'              => $dpia->ropaActivity?->kode ?? '-',
                'ropa_nama'              => $dpia->ropaActivity?->nama_aktivitas ?? '-',
                'nama_aktivitas'         => $dpia->nama_aktivitas,
                'opd'                    => $dpia->opd?->namaopd ?? '-',
                'penanggung_jawab'       => $dpia->penanggung_jawab,
                'ppd'                    => $dpia->ppd ?? '-',
                'tanggal'                => $dpia->tanggal_penyusunan?->locale('id')->translatedFormat('d F Y') ?? '-',
                'versi'                  => $dpia->versi,
                'konsultasi_stakeholder' => $dpia->konsultasi_stakeholder,
                'kriteria_risiko'        => $dpia->kriteria_risiko,
                'evaluasi_residual'      => $dpia->evaluasi_residual,
                'kesimpulan'             => $dpia->kesimpulan,
                'threshold' => $dpia->thresholds->map(fn($t) => [
                    'trigger'    => DpiaThreshold::INDIKATOR_LABELS[$t->indikator] ?? $t->indikator,
                    'terpenuhi'  => $t->terpenuhi,
                    'keterangan' => $t->keterangan ?? '-',
                ])->toArray(),
                'tim_terlibat' => $dpia->tim->map(fn($t) =>
                    $t->nama_anggota . ' — ' . $t->peran
                )->toArray(),
                'risiko' => $dpia->risikos->map(fn($r) => [
                    'ancaman'    => $r->ancaman,
                    'likelihood' => $r->likelihood,
                    'dampak'     => $r->dampak,
                    'level'      => $r->level,
                    'mitigasi'   => $r->rencana_mitigasi,
                ])->toArray(),
            ],
        ];

        return $this->runPythonPdf(
            base_path('scripts/generate_dpia_v3_pdf.py'),
            $payload,
            'PERISAI_' . $dpia->kode . '_' . now()->format('Ymd_His') . '.pdf'
        );
    }

    // ── PRIVATE HELPERS ──────────────────────────────────────────

    private function syncChildRecords(Dpia $dpia, Request $request): void
    {
        // Tim
        $dpia->tim()->delete();
        foreach ($request->input('tim', []) as $i => $row) {
            if (empty($row['nama_anggota'])) continue;
            $dpia->tim()->create([
                'nama_anggota' => $row['nama_anggota'],
                'peran'        => $row['peran'] ?? '',
                'urutan'       => $i,
            ]);
        }

        // Risiko
        $dpia->risikos()->delete();
        foreach ($request->input('risiko', []) as $i => $row) {
            if (empty($row['ancaman'])) continue;
            $likelihood = $row['likelihood'] ?? 'Sedang';
            $dampak     = $row['dampak'] ?? 'Sedang';
            $dpia->risikos()->create([
                'ancaman'          => $row['ancaman'],
                'likelihood'       => $likelihood,
                'dampak'           => $dampak,
                'level'            => DpiaRisiko::computeLevel($likelihood, $dampak),
                'rencana_mitigasi' => $row['rencana_mitigasi'] ?? null,
                'urutan'           => $i,
            ]);
        }
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

    private function runPythonPdf(string $script, array $payload, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $tmpPdf = sys_get_temp_dir() . '/perisai_dpia_' . Str::random(8) . '.pdf';

        $process = new Process(
            ['python3', $script, $tmpPdf],
            null, null,
            json_encode($payload, JSON_UNESCAPED_UNICODE),
            60
        );
        $process->run();

        if (!$process->isSuccessful() || !file_exists($tmpPdf)) {
            Log::error('DPIA PDF generation failed', [
                'script' => $script,
                'stderr' => $process->getErrorOutput(),
            ]);
            abort(500, 'Gagal generate PDF DPIA. Periksa log server.');
        }

        return response()->file($tmpPdf, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ])->deleteFileAfterSend(true);
    }
}
