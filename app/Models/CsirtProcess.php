<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CsirtProcess extends Model
{
    protected $fillable = [
        'report_id',
        'handled_by',
        'status',
        'notes',
        'mitigation_file',
        'mitigation_file_original',
        'notified_at',
        'started_at',
        'closed_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'started_at'  => 'datetime',
        'closed_at'   => 'datetime',
    ];

    // -------------------------------------------------------
    // Relasi
    // -------------------------------------------------------

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    public static function statusLabel(): array
    {
        return [
            'notified'    => 'Menunggu Proses',
            'in_progress' => 'Sedang Diproses',
            'closed'      => 'Selesai',
        ];
    }

    public static function statusColor(): array
    {
        return [
            'notified'    => 'yellow',
            'in_progress' => 'blue',
            'closed'      => 'green',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColor()[$this->status] ?? 'gray';
    }

    public function hasMitigationFile(): bool
    {
        return !empty($this->mitigation_file);
    }
}
