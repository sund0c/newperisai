<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SePenilaian extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'se_penilaians';

    protected $fillable = [
        'asset_instance_id',
        'se_version_id',
        'status',
        'catatan',
        'total_nilai',
        'kategori_se',
        'dinilai_oleh',
        'diverifikasi_oleh',
        'dinilai_at',
        'diverifikasi_at',
    ];

    protected $casts = [
        'total_nilai'      => 'decimal:2',
        'dinilai_at'       => 'datetime',
        'diverifikasi_at'  => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────
    public function assetInstance(): BelongsTo
    {
        return $this->belongsTo(AssetInstance::class, 'asset_instance_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(SeVersion::class, 'se_version_id');
    }

    public function jawabans(): HasMany
    {
        return $this->hasMany(SePenilaianJawaban::class, 'se_penilaian_id')
            ->orderBy('urutan_indikator');
    }

    public function dinilaiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_oleh');
    }

    public function diverifikasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function hitungTotalNilai(): float
    {
        return $this->jawabans->sum('nilai_dipilih');
    }

    /**
     * Kategorisasi berdasarkan total nilai (10 indikator, nilai 1–3 tiap indikator).
     * Total range: 10–30.
     * Sesuaikan threshold sesuai kebijakan.
     */
    public function tentukanKategori(float $total): string
    {
        return match (true) {
            $total <= 14 => 'Rendah',
            $total <= 19 => 'Sedang',
            $total <= 24 => 'Tinggi',
            default      => 'Sangat Tinggi',
        };
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isFinal(): bool
    {
        return $this->status === 'final';
    }
}
