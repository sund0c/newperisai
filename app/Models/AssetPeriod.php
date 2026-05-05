<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Enums\JenisPeriode;


class AssetPeriod extends Model
{
    use HasUuids;

    protected $table = 'asset_periods';

    protected $fillable = [
        'nama_periode',
        'jenis_periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'is_active'       => 'boolean',
        'jenis_periode'   => JenisPeriode::class, // cast otomatis
    ];


    // ── Scopes ──────────────────────────────────────────────

    public function scopeAktif($query, string $jenis)
    {
        return $query->where('jenis_periode', $jenis)
            ->where('is_active', true);
    }

    public function scopeByJenis($query, string $jenis)
    {
        return $query->where('jenis_periode', $jenis);
    }

    public function scopeBerjalan($query)
    {
        $today = now()->toDateString();
        return $query->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today);
    }

    // ── Accessors ────────────────────────────────────────────

    public function getBerjalanAttribute(): bool
    {
        return now()->between($this->tanggal_mulai, $this->tanggal_selesai);
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_active && $this->berjalan) return 'Aktif & Berjalan';
        if ($this->is_active)                    return 'Aktif';
        if ($this->berjalan)                     return 'Berjalan';
        if (now()->isAfter($this->tanggal_selesai)) return 'Selesai';
        return 'Terjadwal';
    }

    // ── Methods ──────────────────────────────────────────────

    public function activate(): void
    {
        // Non-aktifkan semua periode sejenis, lalu aktifkan ini
        self::where('jenis_periode', $this->jenis_periode)
            ->where('id', '!=', $this->id)
            ->update(['is_active' => false]);

        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    // Cek overlap hanya dalam jenis yang sama
    public static function hasOverlap(
        string  $jenis,
        string  $mulai,
        string  $selesai,
        ?string $excludeId = null
    ): bool {
        return self::where('jenis_periode', $jenis)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($mulai, $selesai) {
                $q->whereBetween('tanggal_mulai', [$mulai, $selesai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
                    ->orWhere(function ($q) use ($mulai, $selesai) {
                        $q->where('tanggal_mulai', '<=', $mulai)
                            ->where('tanggal_selesai', '>=', $selesai);
                    });
            })
            ->exists();
    }
}
