<?php

namespace App\Http\Controllers\Support;

use App\Models\AuditLog;
use App\Http\Controllers\Controller;
use App\Models\CsirtProcess;
use App\Models\Report;
use App\Models\ReportAttachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Middleware\SandidataMiddleware;

class AttachmentController extends Controller
{
    /**
     * Serve attachment PoC (gambar / PDF) dari private storage.
     * Support dan admin bisa akses semua.
     */
    public function show(ReportAttachment $attachment): StreamedResponse
    {
        abort_unless(
            Storage::disk($attachment->disk)->exists($attachment->path),
            404,
            'File tidak ditemukan.'
        );

        $mimeType    = $attachment->mime_type;
        $filename    = $attachment->original_name;
        $disposition = in_array($mimeType, ['image/jpeg', 'image/png', 'application/pdf'])
            ? 'inline'
            : 'attachment';

        return Storage::disk($attachment->disk)->response(
            $attachment->path,
            $filename,
            [
                'Content-Type'           => $mimeType,
                'Content-Disposition'    => "{$disposition}; filename=\"{$filename}\"",
                'Cache-Control'          => 'private, no-store, no-cache',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options'        => 'SAMEORIGIN',
            ]
        );
    }

    /**
     * Download e-certificate PDF dari private storage.
     */
    public function downloadCertificate(Report $report): StreamedResponse
    {
        abort_if(!$report->certificate_file, 404, 'e-Sertifikat belum tersedia.');
        abort_unless(
            Storage::disk('local')->exists($report->certificate_file),
            404,
            'File e-sertifikat tidak ditemukan.'
        );

        $filename = $report->certificate_file_original ?? 'e-sertifikat.pdf';

        return Storage::disk('local')->response(
            $report->certificate_file,
            $filename,
            [
                'Content-Type'           => 'application/pdf',
                'Content-Disposition'    => "inline; filename=\"{$filename}\"",
                'Cache-Control'          => 'private, no-store, no-cache',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    /**
     * Download laporan mitigasi CSIRT dari private storage.
     * Hanya support dan admin yang bisa akses.
     */
    public function downloadMitigation(CsirtProcess $csirtProcess)
    {
        abort_if(!$csirtProcess->mitigation_file, 404, 'Laporan mitigasi belum tersedia.');

        $encPath = Storage::disk('local')->path($csirtProcess->mitigation_file);
        abort_if(!file_exists($encPath), 404, 'File laporan mitigasi tidak ditemukan.');

        [$pdfContent, $error] = \App\Http\Middleware\SandidataMiddleware::unsealFile($encPath);

        if ($error || !$pdfContent) {
            abort(500, 'Gagal mendekripsi file mitigasi.');
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'csirt_mitigation_file_accessed',
            'model_type' => 'CsirtProcess',
            'model_id'   => $csirtProcess->id,
            'new_values' => ['file' => $csirtProcess->mitigation_file_original],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $filename = $csirtProcess->mitigation_file_original ?? 'laporan-mitigasi.pdf';

        return response($pdfContent, 200, [
            'Content-Type'           => 'application/pdf',
            'Content-Disposition'    => 'inline; filename="' . $filename . '"',
            'Content-Length'         => strlen($pdfContent),
            'Cache-Control'          => 'private, no-store, no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function downloadDpoMitigation(\App\Models\DpoProcess $dpoProcess)
    {
        abort_if(!$dpoProcess->mitigation_file, 404, 'Laporan belum tersedia.');

        $encPath = Storage::disk('local')->path($dpoProcess->mitigation_file);
        abort_if(!file_exists($encPath), 404, 'File tidak ditemukan.');

        [$pdfContent, $error] = \App\Http\Middleware\SandidataMiddleware::unsealFile($encPath);

        if ($error || !$pdfContent) {
            abort(500, 'Gagal mendekripsi file DPO.');
        }

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'dpo_mitigation_file_accessed',
            'model_type' => 'DpoProcess',
            'model_id'   => $dpoProcess->id,
            'new_values' => ['file' => $dpoProcess->mitigation_file_original],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $filename = $dpoProcess->mitigation_file_original ?? 'laporan-dpo.pdf';

        return response($pdfContent, 200, [
            'Content-Type'           => 'application/pdf',
            'Content-Disposition'    => 'inline; filename="' . $filename . '"',
            'Content-Length'         => strlen($pdfContent),
            'Cache-Control'          => 'private, no-store, no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
