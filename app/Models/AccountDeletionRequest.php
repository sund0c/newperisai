<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountDeletionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'reason',
        'scheduled_at',
        'cancelled_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Permintaan yang belum dibatalkan dan belum melewati waktu eksekusi */
    public function scopePending($query)
    {
        return $query->whereNull('cancelled_at')
            ->where('scheduled_at', '>', now());
    }

    /** Permintaan yang sudah waktunya dieksekusi dan belum dibatalkan */
    public function scopeDue($query)
    {
        return $query->whereNull('cancelled_at')
            ->where('scheduled_at', '<=', now());
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }

    public function isPending(): bool
    {
        return $this->cancelled_at === null && $this->scheduled_at->isFuture();
    }

    /** Sisa jam sebelum eksekusi */
    public function hoursRemaining(): int
    {
        return (int) max(0, now()->diffInHours($this->scheduled_at, false));
    }
}
