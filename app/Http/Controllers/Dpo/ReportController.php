<?php

namespace App\Http\Controllers\Dpo;

use App\Models\AuditLog;
use App\Http\Controllers\Controller;
use App\Models\DpoProcess;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Middleware\SandidataMiddleware;

class ReportController extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // INDEX — daftar tiket yang masuk ke DPO (hanya yang valid)
    // ════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = DpoProcess::with(['report.reporter', 'handler'])
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

        return view('dpo.reports.index', compact('processes'));
    }

    // ════════════════════════════════════════════════════════════════
    // SHOW — detail tiket + panel aksi DPO
    // ════════════════════════════════════════════════════════════════

    public function show(DpoProcess $dpoProcess)
    {
        $dpoProcess->load(['report.reporter', 'report.images', 'report.documents', 'report.statusLogs.changer', 'handler']);

        return view('dpo.reports.show', compact('dpoProcess'));
    }

    // ════════════════════════════════════════════════════════════════
    // PROSES — DPO mulai proses mitigasi
    // ════════════════════════════════════════════════════════════════

    public function start(Request $request, DpoProcess $dpoProcess)
    {
        abort_if($dpoProcess->status !== 'notified', 403, 'Proses sudah dimulai sebelumnya.');

        $dpoProcess->update([
            'status'     => 'in_progress',
            'handled_by' => auth()->id(),
            'started_at' => now(),
        ]);

        return back()->with('success', 'Proses mitigasi dimulai.');
    }

    // ════════════════════════════════════════════════════════════════
    // CLOSE — upload laporan mitigasi + tutup proses
    // ════════════════════════════════════════════════════════════════

    public function close(Request $request, DpoProcess $dpoProcess)
    {
        abort_if($dpoProcess->status !== 'in_progress', 403, 'Proses belum dimulai atau sudah selesai.');

        $request->validate([
            'mitigation_file' => 'required|file|mimes:pdf|max:10240', // maks 10MB
            'notes'           => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($request, $dpoProcess) {
            $file    = $request->file('mitigation_file');
            $tmpPath = $file->getRealPath();

            // Enkripsi file via SEAL BSSN
            [$encContent, $error] = \App\Http\Middleware\SandidataMiddleware::sealFile($tmpPath);

            if ($error || !$encContent) {
                throw new \Exception('Gagal mengenkripsi file mitigasi.');
            }

            $path = "mitigations/{$dpoProcess->report_id}/" . Str::uuid() . '.enc';
            Storage::disk('local')->put($path, $encContent);

            $notes = $request->notes
                ? SandidataMiddleware::encryptValue(strip_tags($request->notes))
                : null;

            $dpoProcess->update([
                'status'                   => 'closed',
                'notes'                    => $notes,
                'mitigation_file'          => $path,
                'mitigation_file_original' => $file->getClientOriginalName(),
                'closed_at'                => now(),
            ]);
        });

        // DB::transaction(function () use ($request, $dpoProcess) {
        //     $file     = $request->file('mitigation_file');
        //     $uuid     = Str::uuid();
        //     $filename = $uuid . '.pdf';
        //     $path     = "mitigations/{$dpoProcess->report_id}/{$filename}";

        //     Storage::disk('local')->put($path, file_get_contents($file));

        //     $notes = $request->notes
        //         ? SandidataMiddleware::encryptValue(strip_tags($request->notes))
        //         : null;

        //     $dpoProcess->update([
        //         'status'                   => 'closed',
        //         'notes'                    => $notes,
        //         'mitigation_file'          => $path,
        //         'mitigation_file_original' => $file->getClientOriginalName(),
        //         'closed_at'                => now(),
        //     ]);
        // });

        return back()->with('success', 'Proses mitigasi selesai. Laporan berhasil diupload.');
    }

    public function showValidationFile(Report $report)
    {
        abort_if(!$report->validation_file, 404, 'File tidak tersedia.');

        $encPath = Storage::disk('local')->path($report->validation_file);

        abort_if(!file_exists($encPath), 404, 'File tidak ditemukan.');

        // Dekripsi via SEAL BSSN
        [$pdfContent, $error] = \App\Http\Middleware\SandidataMiddleware::unsealFile($encPath);

        if ($error || !$pdfContent) {
            abort(500, 'Gagal mendekripsi file.');
        }

        // Audit log setiap akses
        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'validation_file_accessed',
            'model_type' => 'Report',
            'model_id'   => $report->id,
            'new_values' => ['file' => $report->validation_file_original],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $filename = $report->validation_file_original ?? 'laporan-validasi.pdf';

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Content-Length'      => strlen($pdfContent),
            'Cache-Control'       => 'private, no-store',
        ]);
    }

    // public function showValidationFile(\App\Models\Report $report)
    // {
    //     abort_if(!$report->validation_file, 404);
    //     abort_unless(
    //         \Illuminate\Support\Facades\Storage::disk('local')->exists($report->validation_file),
    //         404
    //     );

    //     return \Illuminate\Support\Facades\Storage::disk('local')->response(
    //         $report->validation_file,
    //         $report->validation_file_original ?? 'laporan-validasi.pdf',
    //         ['Content-Type' => 'application/pdf', 'Cache-Control' => 'private, no-store']
    //     );
    // }

    // ════════════════════════════════════════════════════════════════
    // DOWNLOAD — laporan mitigasi PDF (hanya DPO sendiri)
    // ════════════════════════════════════════════════════════════════

    // public function download(DpoProcess $dpoProcess)
    // {
    //     abort_if(!$dpoProcess->mitigation_file, 404, 'Laporan mitigasi belum tersedia.');
    //     abort_unless(
    //         Storage::disk('local')->exists($dpoProcess->mitigation_file),
    //         404,
    //         'File tidak ditemukan.'
    //     );

    //     $filename = $dpoProcess->mitigation_file_original ?? 'laporan-mitigasi.pdf';

    //     return Storage::disk('local')->response(
    //         $dpoProcess->mitigation_file,
    //         $filename,
    //         [
    //             'Content-Type'           => 'application/pdf',
    //             'Content-Disposition'    => "inline; filename=\"{$filename}\"",
    //             'Cache-Control'          => 'private, no-store, no-cache',
    //             'X-Content-Type-Options' => 'nosniff',
    //         ]
    //     );
    // }
    public function download(DpoProcess $dpoProcess)
    {
        abort_if(!$dpoProcess->mitigation_file, 404, 'Laporan mitigasi belum tersedia.');

        $encPath = Storage::disk('local')->path($dpoProcess->mitigation_file);

        abort_if(!file_exists($encPath), 404, 'File tidak ditemukan.');

        // Dekripsi via SEAL BSSN
        [$pdfContent, $error] = \App\Http\Middleware\SandidataMiddleware::unsealFile($encPath);

        if ($error || !$pdfContent) {
            abort(500, 'Gagal mendekripsi file.');
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'mitigation_file_accessed',
            'model_type' => 'DpoProcess',
            'model_id'   => $dpoProcess->id,
            'new_values' => ['file' => $dpoProcess->mitigation_file_original],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $filename = $dpoProcess->mitigation_file_original ?? 'laporan-mitigasi.pdf';

        return response($pdfContent, 200, [
            'Content-Type'           => 'application/pdf',
            'Content-Disposition'    => 'inline; filename="' . $filename . '"',
            'Content-Length'         => strlen($pdfContent),
            'Cache-Control'          => 'private, no-store, no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    // ADD ACTIVITY — DPO catat aktivitas/progress penanganan
    // ════════════════════════════════════════════════════════════════

    public function addActivity(Request $request, DpoProcess $dpoProcess)
    {
        abort_if($dpoProcess->status !== 'in_progress', 403, 'Aktivitas hanya bisa ditambah saat proses sedang berjalan.');

        $request->validate([
            'type'  => 'required|in:update,notification,coordination,technical,other',
            'title' => 'required|string|max:200',
            'body'  => 'nullable|string|max:5000',
        ]);

        $title          = SandidataMiddleware::encryptValue(strip_tags($request->title));
        $body = $request->body
            ? SandidataMiddleware::encryptValue(strip_tags($request->body))
            : null;

        $dpoProcess->activityLogs()->create([
            'logged_by' => auth()->id(),
            'type'      => $request->type,
            'title'     => $title,
            'body'      => $body,
        ]);

        return back()->with('success', 'Aktivitas berhasil dicatat.');
    }
}
