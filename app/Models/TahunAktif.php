<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TahunAktif extends Model
{
    use HasUuids;

    protected $table = 'tahunaktifs';

    protected $fillable = [
        'tahun',
        'is_active',
    ];

    protected $casts = [
        'tahun'     => 'integer',
        'is_active' => 'boolean',
    ];

    // ── Scopes ────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Ambil tahun yang sedang aktif (singleton).
     */
    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Aktifkan tahun ini, nonaktifkan semua yang lain.
     * Dibungkus transaksi supaya atomik.
     */
    public function activate(): void
    {
        \DB::transaction(function () {
            static::where('is_active', true)->update(['is_active' => false]);
            $this->update(['is_active' => true]);
        });
    }

    /**
     * Nonaktifkan tahun ini.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
