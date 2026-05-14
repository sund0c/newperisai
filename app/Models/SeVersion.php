<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeVersion extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'se_versions';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'is_active',
        'created_by',
        'activated_by',
        'activated_at',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'activated_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────
    public function indikators(): HasMany
    {
        return $this->hasMany(SeIndikator::class, 'se_version_id')->orderBy('urutan');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(SePenilaian::class, 'se_version_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by', 'id');
    }

    // ─── Scopes ───────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Generate kode otomatis: SE-V001, SE-V002, dst.
     */
    public static function generateKode(): string
    {
        $last = static::withTrashed()
            ->where('kode', 'like', 'SE-V%')
            ->orderByDesc('kode')
            ->value('kode');

        if (!$last) {
            return 'SE-V001';
        }

        $num = (int) substr($last, 4);
        return 'SE-V' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }

    public function getIndikatorCountAttribute(): int
    {
        return $this->indikators()->count();
    }

    public function getPenilaianCountAttribute(): int
    {
        return $this->penilaians()->count();
    }

    /**
     * Versi bisa diaktifkan hanya jika memiliki indikator.
     * Cek sebelum activate: $version->canBeActivated()
     */
    public function canBeActivated(): bool
    {
        return $this->indikators()->count() > 0;
    }

    /**
     * Versi yang sudah punya penilaian tidak boleh dihapus permanent.
     */
    public function canBeDeleted(): bool
    {
        return $this->penilaians()->count() === 0;
    }
}
