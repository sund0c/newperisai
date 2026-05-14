<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetSePenilaian extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'asset_se_penilaians';

    protected $fillable = [
        'asset_id',
        'se_version_id',
        'tahunaktif_id',
        'jawabans',
        'total_nilai',
        'kategori_se',
        'dinilai_oleh',
        'dinilai_pada',
    ];

    protected $casts = [
        'jawabans'     => 'array',
        'total_nilai'  => 'integer',
        'dinilai_pada' => 'datetime',
    ];

    // ─── Relasi ──────────────────────────────────────────────────

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function seVersion(): BelongsTo
    {
        return $this->belongsTo(SeVersion::class, 'se_version_id');
    }

    public function tahunAktif(): BelongsTo
    {
        return $this->belongsTo(TahunAktif::class, 'tahunaktif_id');
    }

    public function dinilaiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }

    // ─── Business Logic ──────────────────────────────────────────

    public static function hitungTotal(array $jawabans): int
    {
        $map   = ['a' => 5, 'b' => 2, 'c' => 1];
        $total = 0;
        foreach ($jawabans as $v) {
            $total += isset($map[$v]) ? $map[$v] : 0;
        }
        return $total;
    }

    public static function tentukanKategori(int $total): string
    {
        if ($total >= 35) return 'STRATEGIS';
        if ($total >= 16) return 'TINGGI';
        return 'RENDAH';
    }

    // ─── Accessors ───────────────────────────────────────────────

    public function getKategoriColorAttribute(): string
    {
        if ($this->kategori_se === 'STRATEGIS') return 'purple';
        if ($this->kategori_se === 'TINGGI')    return 'amber';
        if ($this->kategori_se === 'RENDAH')    return 'green';
        return 'gray';
    }

    public function getKategoriClassAttribute(): string
    {
        if ($this->kategori_se === 'STRATEGIS') return 'bg-purple-100 text-purple-700';
        if ($this->kategori_se === 'TINGGI')    return 'bg-amber-100 text-amber-700';
        if ($this->kategori_se === 'RENDAH')    return 'bg-green-100 text-green-700';
        return 'bg-gray-100 text-gray-400';
    }
}
