<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ReportAttachment extends Model
{
    protected $fillable = [
        'report_id',
        'type',
        'original_name',
        'stored_name',
        'disk',
        'path',
        'mime_type',
        'size',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Hapus file fisik saat model dihapus
     */
    protected static function booted(): void
    {
        static::deleting(function (ReportAttachment $attachment) {
            Storage::disk($attachment->disk)->delete($attachment->path);
        });
    }

    /**
     * Format ukuran file untuk tampilan
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
