<?php

namespace App\Http\Controllers\Csirt;

use App\Http\Controllers\Controller;
use App\Models\CsirtProcess;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // INDEX — daftar tiket yang masuk ke CSIRT (hanya yang valid)
    // ════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = CsirtProcess::with(['report.reporter', 'handler'])
            ->latest();

        // Filter status proses
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by ticket number atau judul
        if ($request->filled('search')) {
            $query->whereHas('report', function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%');
            });
        }

        // Urut: notified & in_progress dulu, closed di bawah
        $query->orderByRaw("FIELD(status, 'in_progress', 'notified', 'closed')");

        $processes = $query->paginate(20)->withQueryString();

        return view('csirt.reports.index', compact('processes'));
    }

    // ════════════════════════════════════════════════════════════════
    // SHOW — detail tiket + panel aksi CSIRT
    // ════════════════════════════════════════════════════════════════

    public function show(CsirtProcess $csirtProcess)
    {
        $csirtProcess->load(['report.reporter', 'report.images', 'report.documents', 'report.statusLogs.changer', 'handler']);

        return view('csirt.reports.show', compact('csirtProcess'));
    }

    // ════════════════════════════════════════════════════════════════
    // PROSES — CSIRT mulai proses mitigasi
    // ════════════════════════════════════════════════════════════════

    public function start(Request $request, CsirtProcess $csirtProcess)
    {
        abort_if($csirtProcess->status !== 'notified', 403, 'Proses sudah dimulai sebelumnya.');

        $csirtProcess->update([
            'status'     => 'in_progress',
            'handled_by' => auth()->id(),
            'started_at' => now(),
        ]);

        return back()->with('success', 'Proses mitigasi dimulai.');
    }

    // ════════════════════════════════════════════════════════════════
    // CLOSE — upload laporan mitigasi + tutup proses
    // ════════════════════════════════════════════════════════════════

    public function close(Request $request, CsirtProcess $csirtProcess)
    {
        abort_if($csirtProcess->status !== 'in_progress', 403, 'Proses belum dimulai atau sudah selesai.');

        $request->validate([
            'mitigation_file' => 'required|file|mimes:pdf|max:10240', // maks 10MB
            'notes'           => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($request, $csirtProcess) {
            $file     = $request->file('mitigation_file');
            $uuid     = Str::uuid();
            $filename = $uuid . '.pdf';
            $path     = "mitigations/{$csirtProcess->report_id}/{$filename}";

            Storage::disk('local')->put($path, file_get_contents($file));

            $csirtProcess->update([
                'status'                   => 'closed',
                'notes'                    => $request->notes,
                'mitigation_file'          => $path,
                'mitigation_file_original' => $file->getClientOriginalName(),
                'closed_at'                => now(),
            ]);
        });

        return back()->with('success', 'Proses mitigasi selesai. Laporan berhasil diupload.');
    }

    // ════════════════════════════════════════════════════════════════
    // DOWNLOAD — laporan mitigasi PDF (hanya CSIRT sendiri)
    // ════════════════════════════════════════════════════════════════

    public function download(CsirtProcess $csirtProcess)
    {
        abort_if(!$csirtProcess->mitigation_file, 404, 'Laporan mitigasi belum tersedia.');
        abort_unless(
            Storage::disk('local')->exists($csirtProcess->mitigation_file),
            404,
            'File tidak ditemukan.'
        );

        $filename = $csirtProcess->mitigation_file_original ?? 'laporan-mitigasi.pdf';

        return Storage::disk('local')->response(
            $csirtProcess->mitigation_file,
            $filename,
            [
                'Content-Type'           => 'application/pdf',
                'Content-Disposition'    => "inline; filename=\"{$filename}\"",
                'Cache-Control'          => 'private, no-store, no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}
