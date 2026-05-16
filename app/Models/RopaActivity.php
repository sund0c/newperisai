<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class RopaActivity extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'ropa_activities';

    protected $fillable = [
        'tahunaktif_id',
        'opd_id',
        'kode',
        'nama_aktivitas',
        'penanggung_jawab',
        'deskripsi_tujuan',
        'subjek_data',
        'sumber_pemerolehan',
        'penyimpanan_data',
        'metode_elektronik',
        'metode_non_elektronik',
        'referensi_dasar_hukum',
        'masa_retensi',
        'langkah_teknis',
        'langkah_organisasi',
        'proses_sebelumnya',
        'proses_setelahnya',
        'catatan',
        'narasi_risiko',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'metode_elektronik'     => 'boolean',
        'metode_non_elektronik' => 'boolean',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function tahunAktif(): BelongsTo
    {
        return $this->belongsTo(TahunAktif::class, 'tahunaktif_id');
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function legalBases(): HasMany
    {
        return $this->hasMany(RopaLegalBasis::class, 'ropa_activity_id');
    }

    public function personalDataTypes(): HasMany
    {
        return $this->hasMany(RopaPersonalDataType::class, 'ropa_activity_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(RopaRecipient::class, 'ropa_activity_id');
    }

    public function subjectRights(): HasMany
    {
        return $this->hasMany(RopaSubjectRight::class, 'ropa_activity_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(RopaAsset::class, 'ropa_activity_id');
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeForTahun($query, int|string|null $tahunaktifId)
    {
        if (!$tahunaktifId) return $query;
        return $query->where('tahunaktif_id', $tahunaktifId);
    }

    public function scopeForOpd($query, int|null $opdId)
    {
        if (!$opdId) return $query;
        return $query->where('opd_id', $opdId);
    }

    // ── Helpers ─────────────────────────────────────────────────

    public static function generateKode(): string
    {
        return DB::transaction(function () {
            $last = static::withTrashed()
                ->lockForUpdate()
                ->orderByRaw("CAST(SUBSTRING(kode, 6) AS UNSIGNED) DESC")
                ->value('kode');

            $next = $last ? (int) substr($last, 5) + 1 : 1;

            return 'RoPA-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }

    public function isEditable(): bool
    {
        $tahunAktif = TahunAktif::where('is_active', true)->value('id');
        return (string) $this->tahunaktif_id === (string) $tahunAktif;
    }
}
