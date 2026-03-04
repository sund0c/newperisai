<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ReportAttachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    /**
     * Serve file attachment dari private storage dengan access control:
     * - Public user: hanya bisa akses attachment milik laporannya sendiri
     * - Support / Admin: bisa akses semua attachment
     * - Belum login: otomatis ditolak (middleware auth di route)
     */
    public function show(ReportAttachment $attachment): StreamedResponse
    {
        $user = auth()->user();

        // Support dan admin bisa akses semua
        $canAccess = $user->hasAnyRole(['admin', 'support'])
            || $attachment->report->user_id === $user->id;

        abort_if(!$canAccess, 403);

        abort_unless(
            Storage::disk($attachment->disk)->exists($attachment->path),
            404,
            'File tidak ditemukan.'
        );

        $mimeType = $attachment->mime_type;
        $filename = $attachment->original_name;

        // Gambar dan PDF preview inline di browser, lainnya force download
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
}
